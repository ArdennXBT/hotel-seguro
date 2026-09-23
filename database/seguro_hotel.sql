-- ════════════════════════════════════════════════════════
-- BASE DE DONNÉES — Hôtel SEGURO
-- Système de réservation avec gestion des rôles
-- Version : 1.0
-- ════════════════════════════════════════════════════════

-- ────────────────────────────────────────────────────────
-- TABLE : users
-- Clients + admins (différenciés par le champ `role`)
-- Un compte est créé automatiquement à la 1ère réservation
-- ────────────────────────────────────────────────────────
CREATE TABLE users (
  id            CHAR(36)        NOT NULL DEFAULT (UUID()),
  nom           VARCHAR(100)    NOT NULL,
  prenom        VARCHAR(100)    NOT NULL,
  email         VARCHAR(191)    NOT NULL,
  code_client   VARCHAR(20)     NOT NULL COMMENT 'Code d\'identification envoyé par email',
  telephone     VARCHAR(20)     NULL,
  pays          VARCHAR(80)     NULL,
  role          ENUM('client','admin','super_admin') NOT NULL DEFAULT 'client',
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login    DATETIME        NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_email       (email),
  UNIQUE KEY uq_code_client (code_client),
  INDEX idx_role (role)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : chambres
-- Catalogue des hébergements SEGURO
-- ────────────────────────────────────────────────────────
CREATE TABLE chambres (
  id                CHAR(36)        NOT NULL DEFAULT (UUID()),
  nom               VARCHAR(150)    NOT NULL,
  type              ENUM('standard','superieure','suite','villa') NOT NULL,
  superficie_m2     TINYINT UNSIGNED NOT NULL,
  prix_nuit         DECIMAL(10,2)   NOT NULL COMMENT 'Prix en FCFA',
  capacite_max      TINYINT UNSIGNED NOT NULL DEFAULT 2,
  capacite_enfants  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  description       TEXT            NULL,
  amenities         JSON            NULL COMMENT '["Wifi","Piscine privée",...]',
  image_principale  VARCHAR(255)    NULL,
  disponible        TINYINT(1)      NOT NULL DEFAULT 1,
  etage             TINYINT UNSIGNED NULL,
  numero            SMALLINT UNSIGNED NULL,

  PRIMARY KEY (id),
  INDEX idx_type       (type),
  INDEX idx_disponible (disponible),
  INDEX idx_prix       (prix_nuit)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : options
-- Services supplémentaires (petit-déj, transfert, spa…)
-- ────────────────────────────────────────────────────────
CREATE TABLE options (
  id          CHAR(36)        NOT NULL DEFAULT (UUID()),
  nom         VARCHAR(150)    NOT NULL,
  description TEXT            NULL,
  prix        DECIMAL(10,2)   NOT NULL COMMENT 'Prix unitaire en FCFA',
  unite       VARCHAR(50)     NOT NULL DEFAULT 'par réservation'
              COMMENT 'Ex: par nuit, par personne, par trajet',
  actif       TINYINT(1)      NOT NULL DEFAULT 1,

  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : reservations
-- Cœur du système — statut "en_cours" par défaut
-- Validée uniquement par un admin
-- ────────────────────────────────────────────────────────
CREATE TABLE reservations (
  id                  CHAR(36)        NOT NULL DEFAULT (UUID()),
  reference           VARCHAR(30)     NOT NULL COMMENT 'Ex: SEGURO-2025-4821',
  user_id             CHAR(36)        NOT NULL,
  chambre_id          CHAR(36)        NOT NULL,
  date_arrivee        DATE            NOT NULL,
  date_depart         DATE            NOT NULL,
  nb_adultes          TINYINT UNSIGNED NOT NULL DEFAULT 1,
  nb_enfants          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  prix_nuit           DECIMAL(10,2)   NOT NULL COMMENT 'Prix snapshot au moment de la résa',
  prix_options        DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  prix_total          DECIMAL(10,2)   NOT NULL,
  statut              ENUM(
                        'en_cours',     -- Créée par le client, en attente admin
                        'validee',      -- Confirmée par l'admin
                        'modifiee',     -- Modifiée par le client
                        'annulee',      -- Annulée (client ou admin)
                        'terminee'      -- Séjour terminé
                      ) NOT NULL DEFAULT 'en_cours',
  demandes_speciales  TEXT            NULL,
  note_admin          TEXT            NULL COMMENT 'Note interne admin uniquement',
  created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                      ON UPDATE CURRENT_TIMESTAMP,
  valide_par          CHAR(36)        NULL COMMENT 'user_id de l\'admin validateur',
  valide_at           DATETIME        NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_reference (reference),
  FOREIGN KEY fk_resa_user    (user_id)    REFERENCES users(id)    ON DELETE RESTRICT,
  FOREIGN KEY fk_resa_chambre (chambre_id) REFERENCES chambres(id) ON DELETE RESTRICT,
  FOREIGN KEY fk_resa_admin   (valide_par) REFERENCES users(id)    ON DELETE SET NULL,
  INDEX idx_statut        (statut),
  INDEX idx_dates         (date_arrivee, date_depart),
  INDEX idx_user          (user_id),
  INDEX idx_chambre       (chambre_id),

  CONSTRAINT chk_dates CHECK (date_depart > date_arrivee),
  CONSTRAINT chk_adultes CHECK (nb_adultes >= 1)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : reservation_options
-- Options choisies par le client pour une réservation
-- ────────────────────────────────────────────────────────
CREATE TABLE reservation_options (
  id              CHAR(36)        NOT NULL DEFAULT (UUID()),
  reservation_id  CHAR(36)        NOT NULL,
  option_id       CHAR(36)        NOT NULL,
  quantite        TINYINT UNSIGNED NOT NULL DEFAULT 1,
  prix_unitaire   DECIMAL(10,2)   NOT NULL COMMENT 'Snapshot du prix au moment de la résa',

  PRIMARY KEY (id),
  FOREIGN KEY fk_ro_resa   (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
  FOREIGN KEY fk_ro_option (option_id)      REFERENCES options(id)      ON DELETE RESTRICT,
  INDEX idx_resa (reservation_id)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : indisponibilites
-- Blocage manuel de chambre (entretien, événement…)
-- Alimenté aussi par les réservations validées (via trigger)
-- ────────────────────────────────────────────────────────
CREATE TABLE indisponibilites (
  id          CHAR(36)        NOT NULL DEFAULT (UUID()),
  chambre_id  CHAR(36)        NOT NULL,
  date_debut  DATE            NOT NULL,
  date_fin    DATE            NOT NULL,
  motif       VARCHAR(255)    NOT NULL DEFAULT 'Réservation',
  created_by  CHAR(36)        NULL COMMENT 'admin ou NULL si système',
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  FOREIGN KEY fk_indispo_chambre (chambre_id) REFERENCES chambres(id) ON DELETE CASCADE,
  FOREIGN KEY fk_indispo_user    (created_by) REFERENCES users(id)    ON DELETE SET NULL,
  INDEX idx_chambre_dates (chambre_id, date_debut, date_fin),

  CONSTRAINT chk_indispo_dates CHECK (date_fin > date_debut)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : admins
-- Droits étendus des utilisateurs admin
-- ────────────────────────────────────────────────────────
CREATE TABLE admins (
  id          CHAR(36)        NOT NULL DEFAULT (UUID()),
  user_id     CHAR(36)        NOT NULL,
  niveau      ENUM('moderateur','admin','super_admin') NOT NULL DEFAULT 'admin',
  permissions JSON            NULL
              COMMENT '{"valider_resa":true,"annuler_resa":true,"gerer_chambres":true,"voir_logs":true}',
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_user (user_id),
  FOREIGN KEY fk_admin_user (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────
-- TABLE : logs_actions
-- Traçabilité complète de toutes les actions
-- ────────────────────────────────────────────────────────
CREATE TABLE logs_actions (
  id            CHAR(36)        NOT NULL DEFAULT (UUID()),
  user_id       CHAR(36)        NULL,
  action        VARCHAR(100)    NOT NULL
                COMMENT 'Ex: RESERVATION_CREEE, RESERVATION_VALIDEE, CHAMBRE_MODIFIEE',
  table_cible   VARCHAR(60)     NULL,
  cible_id      CHAR(36)        NULL,
  avant         JSON            NULL COMMENT 'État avant modification',
  apres         JSON            NULL COMMENT 'État après modification',
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address    VARCHAR(45)     NULL,

  PRIMARY KEY (id),
  FOREIGN KEY fk_log_user (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_action    (action),
  INDEX idx_cible     (table_cible, cible_id),
  INDEX idx_date      (created_at),
  INDEX idx_user_log  (user_id)
) ENGINE=InnoDB;

-- ════════════════════════════════════════════════════════
-- TRIGGER : Bloque automatiquement la chambre quand
--           une réservation passe en statut "validee"
-- ════════════════════════════════════════════════════════
DELIMITER //
CREATE TRIGGER trg_indispo_apres_validation
AFTER UPDATE ON reservations
FOR EACH ROW
BEGIN
  IF NEW.statut = 'validee' AND OLD.statut != 'validee' THEN
    INSERT INTO indisponibilites (id, chambre_id, date_debut, date_fin, motif, created_by)
    VALUES (UUID(), NEW.chambre_id, NEW.date_arrivee, NEW.date_depart,
            CONCAT('Réservation #', NEW.reference), NEW.valide_par);
  END IF;

  -- Si annulation, supprime l'indisponibilité liée
  IF NEW.statut = 'annulee' AND OLD.statut = 'validee' THEN
    DELETE FROM indisponibilites
    WHERE chambre_id = OLD.chambre_id
      AND date_debut = OLD.date_arrivee
      AND date_fin   = OLD.date_depart
      AND motif      = CONCAT('Réservation #', OLD.reference);
  END IF;
END//
DELIMITER ;

-- ════════════════════════════════════════════════════════
-- DONNÉES INITIALES
-- ════════════════════════════════════════════════════════

-- Chambres SEGURO (issues du brief §3.1)
INSERT INTO chambres (id, nom, type, superficie_m2, prix_nuit, capacite_max, capacite_enfants, description, amenities, image_principale, disponible, etage, numero) VALUES
(UUID(), 'Standard Confort',        'standard',   28,  55000,  2, 0, 'Literie haut de gamme, espace de travail ergonomique, Smart TV, salle de bain avec douche à l\'italienne.',       '["Wifi fibre","Smart TV","Clim","Coffre-fort","Minibar"]',           'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&q=80', 1, 1, 101),
(UUID(), 'Standard Confort Plus',   'standard',   32,  65000,  2, 1, 'Même prestations qu\'une Standard, avec un espace légèrement plus généreux et une vue sur le jardin.',          '["Wifi fibre","Smart TV","Clim","Coffre-fort","Vue jardin"]',         'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80', 1, 1, 102),
(UUID(), 'Supérieure Nature',       'superieure', 42,  95000,  2, 1, 'Balcon privatif, douche à l\'italienne en travertin, vue dégagée sur les espaces verts.',                       '["Wifi fibre","Smart TV","Clim","Balcon","Baignoire","Minibar"]',     'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80', 1, 2, 201),
(UUID(), 'Supérieure Vue Mer',      'superieure', 45, 110000,  2, 1, 'Même prestations que la Supérieure Nature, avec une vue exceptionnelle sur le Golfe de Guinée.',               '["Wifi fibre","Smart TV","Clim","Balcon","Vue mer","Baignoire"]',     'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&q=80', 1, 3, 301),
(UUID(), 'Suite Junior',            'suite',      58, 145000,  2, 1, 'Coin salon séparé, dressing walk-in, baignoire et douche indépendantes, matières nobles.',                      '["Wifi fibre","Smart TV","Clim","Salon","Dressing","Baignoire","Butler"]', 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&q=80', 1, 3, 302),
(UUID(), 'Suite Royale Panoramique','suite',      85, 285000,  2, 0, 'La chambre la plus prestigieuse. Terrasse privée, baignoire îlot en marbre, salon séparé, butler dédié.',    '["Wifi fibre","Smart TV","Clim","Terrasse","Baignoire îlot","Butler 24h","Minibar premium"]', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80', 1, 4, 401),
(UUID(), 'Chambre Familiale',       'superieure', 52, 120000,  4, 2, 'Deux chambres communicantes. Idéale pour les familles avec enfants.',                                          '["Wifi fibre","Smart TV","Clim","Communicante","2 salles de bain"]',  'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=800&q=80', 1, 2, 202),
(UUID(), 'Villa Privée avec Piscine','villa',    120, 420000,  4, 2, 'Villa indépendante avec piscine privée à débordement, jardin paysager, cuisine équipée, terrasse panoramique.', '["Piscine privée","Cuisine équipée","Jardin","Butler 24h","Wifi fibre","Smart TV","2 King beds"]', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 1, 0, 1);

-- Options de services
INSERT INTO options (id, nom, description, prix, unite, actif) VALUES
(UUID(), 'Petit-déjeuner',        'Buffet tropical complet chaque matin pour toute la chambre.',                           25000,  'par nuit',       1),
(UUID(), 'Transfert aéroport',    'Véhicule climatisé avec chauffeur privé depuis l\'aéroport de Lomé, aller simple.',     35000,  'par trajet',     1),
(UUID(), 'Soin Spa 60 min',       'Massage aux huiles botaniques locales pour 2 personnes.',                               60000,  'par séance',     1),
(UUID(), 'Dîner romantique',      'Table privative en terrasse, menu 4 services gastronomiques pour 2 personnes.',         45000,  'par soirée',     1),
(UUID(), 'Excursion Ganvié',      'Pirogue guidée sur le lac Nokoué, village lacustre de Ganvié. Par personne.',           20000,  'par personne',   1),
(UUID(), 'Accueil Champagne',     'Bouteille de champagne et assortiment de fruits de saison à l\'arrivée.',               15000,  'par réservation',1),
(UUID(), 'Session Jet Ski',       'Session de jet ski 30 min sur le Golfe de Guinée. Par personne.',                      30000,  'par personne',   1),
(UUID(), 'Croisière Yacht',       'Sortie en mer privée sur le yacht SEGURO. Coucher de soleil inclus.',                  150000, 'par croisière',  1);

-- Super admin par défaut
INSERT INTO users (id, nom, prenom, email, code_client, telephone, role)
VALUES (UUID(), 'WATEBA', 'Marius', 'direction@hotelseguro.com', 'ADMIN-001', '+228 00 00 00 00', 'super_admin');

-- ════════════════════════════════════════════════════════
-- VUE : Calendrier des indisponibilités
-- Utilisée par le sélecteur de dates côté client
-- ════════════════════════════════════════════════════════
CREATE OR REPLACE VIEW v_calendrier_indisponibilites AS
SELECT
  c.id            AS chambre_id,
  c.nom           AS chambre_nom,
  c.type          AS chambre_type,
  i.date_debut,
  i.date_fin,
  i.motif,
  DATEDIFF(i.date_fin, i.date_debut) AS nb_nuits_bloquees
FROM indisponibilites i
JOIN chambres c ON c.id = i.chambre_id
WHERE i.date_fin >= CURDATE()
ORDER BY i.chambre_id, i.date_debut;

-- ════════════════════════════════════════════════════════
-- VUE : Tableau de bord admin
-- Résumé des réservations par statut
-- ════════════════════════════════════════════════════════
CREATE OR REPLACE VIEW v_dashboard_admin AS
SELECT
  r.statut,
  COUNT(*)                        AS total,
  SUM(r.prix_total)               AS chiffre_affaires,
  AVG(DATEDIFF(r.date_depart, r.date_arrivee)) AS duree_moyenne
FROM reservations r
WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY r.statut;

-- ════════════════════════════════════════════════════════
-- VUE : Disponibilités en temps réel
-- Retourne les chambres libres pour une période donnée
-- Exemple : SELECT * FROM v_chambres_disponibles
--           WHERE date_arrivee = '2025-06-01'
--             AND date_depart  = '2025-06-05';
-- ════════════════════════════════════════════════════════
CREATE OR REPLACE VIEW v_chambres_disponibles AS
SELECT
  c.id,
  c.nom,
  c.type,
  c.superficie_m2,
  c.prix_nuit,
  c.capacite_max,
  c.capacite_enfants,
  c.description,
  c.amenities,
  c.etage,
  c.numero
FROM chambres c
WHERE c.disponible = 1;
