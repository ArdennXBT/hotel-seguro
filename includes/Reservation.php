<?php
/**
 * ════════════════════════════════════════════════════════
 * CLASS RESERVATION — Cœur du système de réservation
 * ════════════════════════════════════════════════════════
 */

require_once 'database.php';

class Reservation {
    private $conn;
    private $table_name = "reservations";
    
    public $id;
    public $reference;
    public $user_id;
    public $chambre_id;
    public $date_arrivee;
    public $date_depart;
    public $nb_adultes;
    public $nb_enfants;
    public $prix_nuit;
    public $prix_options;
    public $prix_total;
    public $statut;
    public $demandes_speciales;
    public $note_admin;
    public $created_at;
    public $updated_at;
    public $valide_par;
    public $valide_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crée une nouvelle réservation (statut "en_cours" par défaut)
     */
    public function create($user_id, $chambre_id, $date_arrivee, $date_depart, $nb_adultes, $nb_enfants = 0, $demandes_speciales = null) {
        try {
            // Récupérer les infos de la chambre pour le prix
            $chambre_query = "SELECT prix_nuit FROM chambres WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($chambre_query);
            $stmt->bindParam(1, $chambre_id);
            $stmt->execute();
            $chambre = $stmt->fetch();
            
            if (!$chambre) {
                throw new Exception("Chambre introuvable");
            }
            
            // Calculer le nombre de nuits
            $nb_nuits = $this->calculerNuits($date_arrivee, $date_depart);
            $prix_nuit = $chambre['prix_nuit'];
            $prix_total = $nb_nuits * $prix_nuit;
            
            // Générer une référence unique
            $this->reference = $this->generateReference();
            
            $query = "INSERT INTO " . $this->table_name . "
                    (reference, user_id, chambre_id, date_arrivee, date_depart, nb_adultes, nb_enfants, prix_nuit, prix_total, statut, demandes_speciales)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_cours', ?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->reference);
            $stmt->bindParam(2, $user_id);
            $stmt->bindParam(3, $chambre_id);
            $stmt->bindParam(4, $date_arrivee);
            $stmt->bindParam(5, $date_depart);
            $stmt->bindParam(6, $nb_adultes);
            $stmt->bindParam(7, $nb_enfants);
            $stmt->bindParam(8, $prix_nuit);
            $stmt->bindParam(9, $prix_total);
            $stmt->bindParam(10, $demandes_speciales);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                $this->user_id = $user_id;
                $this->chambre_id = $chambre_id;
                $this->date_arrivee = $date_arrivee;
                $this->date_depart = $date_depart;
                $this->nb_adultes = $nb_adultes;
                $this->nb_enfants = $nb_enfants;
                $this->prix_nuit = $prix_nuit;
                $this->prix_total = $prix_total;
                $this->statut = 'en_cours';
                $this->demandes_speciales = $demandes_speciales;
                
                // Logger la création
                $this->logAction('RESERVATION_CREEE', $user_id);
                
                return $this;
            }
        } catch(PDOException $exception) {
            echo "Erreur: " . $exception->getMessage();
        }
        return false;
    }
    
    /**
     * Modifie une réservation (client uniquement)
     */
    public function modify($date_arrivee = null, $date_depart = null, $nb_adultes = null, $nb_enfants = null, $demandes_speciales = null, $chambre_id = null) {
        try {
            // Vérifier que la réservation peut être modifiée
            if (!in_array($this->statut, ['en_cours', 'validee'])) {
                throw new Exception("Cette réservation ne peut plus être modifiée");
            }
            
            $old_data = $this->getReservationData();
            
            $updates = [];
            $params = [];
            
            if ($date_arrivee) {
                $updates[] = "date_arrivee = ?";
                $params[] = $date_arrivee;
                $this->date_arrivee = $date_arrivee;
            }
            
            if ($date_depart) {
                $updates[] = "date_depart = ?";
                $params[] = $date_depart;
                $this->date_depart = $date_depart;
            }
            
            if ($nb_adultes !== null) {
                $updates[] = "nb_adultes = ?";
                $params[] = $nb_adultes;
                $this->nb_adultes = $nb_adultes;
            }
            
            if ($nb_enfants !== null) {
                $updates[] = "nb_enfants = ?";
                $params[] = $nb_enfants;
                $this->nb_enfants = $nb_enfants;
            }
            
            if ($demandes_speciales !== null) {
                $updates[] = "demandes_speciales = ?";
                $params[] = $demandes_speciales;
                $this->demandes_speciales = $demandes_speciales;
            }
            
            // Changement de chambre
            if ($chambre_id) {
                $updates[] = "chambre_id = ?";
                $params[] = $chambre_id;
                $this->chambre_id = $chambre_id;
                
                // Récupérer le nouveau prix de la chambre
                $chambre_query = "SELECT prix_nuit FROM chambres WHERE id = ? LIMIT 1";
                $stmt = $this->conn->prepare($chambre_query);
                $stmt->bindParam(1, $chambre_id);
                $stmt->execute();
                $chambre = $stmt->fetch();
                
                if ($chambre) {
                    $this->prix_nuit = $chambre['prix_nuit'];
                    $updates[] = "prix_nuit = ?";
                    $params[] = $this->prix_nuit;
                }
            }
            
            // Recalculer le prix si les dates ou la chambre changent
            if ($date_arrivee || $date_depart || $chambre_id) {
                $nb_nuits = $this->calculerNuits($this->date_arrivee, $this->date_depart);
                $new_total = $nb_nuits * $this->prix_nuit;
                $updates[] = "prix_total = ?";
                $params[] = $new_total;
                $this->prix_total = $new_total;
            }
            
            if (count($updates) > 0) {
                $updates[] = "statut = 'modifiee'";
                $params[] = $this->id;
                
                $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                
                for ($i = 0; $i < count($params); $i++) {
                    $stmt->bindParam($i + 1, $params[$i]);
                }
                
                if ($stmt->execute()) {
                    $this->statut = 'modifiee';
                    
                    // Logger la modification
                    $new_data = $this->getReservationData();
                    $this->logAction('RESERVATION_MODIFIEE', $this->user_id, json_encode($old_data), json_encode($new_data));
                    
                    return true;
                }
            }
        } catch(PDOException $exception) {
            echo "Erreur: " . $exception->getMessage();
        }
        return false;
    }
    
    /**
     * Annule une réservation
     */
    public function cancel($raison = null) {
        try {
            if ($this->statut === 'terminee') {
                throw new Exception("Impossible d'annuler une réservation terminée");
            }
            
            $old_statut = $this->statut;
            
            $query = "UPDATE " . $this->table_name . " SET statut = 'annulee', updated_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            
            if ($stmt->execute()) {
                $this->statut = 'annulee';
                
                // Logger l'annulation
                $this->logAction('RESERVATION_ANNULEE', $this->user_id, $old_statut, 'annulee');
                
                return true;
            }
        } catch(PDOException $exception) {
            echo "Erreur: " . $exception->getMessage();
        }
        return false;
    }
    
    /**
     * Valide une réservation (admin uniquement)
     */
    public function validate($admin_id) {
        try {
            if ($this->statut !== 'en_cours' && $this->statut !== 'modifiee') {
                throw new Exception("Cette réservation ne peut pas être validée");
            }
            
            $query = "UPDATE " . $this->table_name . " 
                    SET statut = 'validee', valide_par = ?, valide_at = NOW(), updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $admin_id);
            $stmt->bindParam(2, $this->id);
            
            if ($stmt->execute()) {
                $this->statut = 'validee';
                $this->valide_par = $admin_id;
                $this->valide_at = date('Y-m-d H:i:s');
                
                // Logger la validation
                $this->logAction('RESERVATION_VALIDEE', $admin_id, 'en_cours', 'validee');
                
                return true;
            }
        } catch(PDOException $exception) {
            echo "Erreur: " . $exception->getMessage();
        }
        return false;
    }
    
    /**
     * Récupère une réservation par son ID
     */
    public function getById($id) {
        $query = "SELECT r.*, c.nom as chambre_nom, c.type as chambre_type,
                 u.nom as client_nom, u.prenom as client_prenom, u.email as client_email
                 FROM " . $this->table_name . " r
                 JOIN chambres c ON r.chambre_id = c.id
                 JOIN users u ON r.user_id = u.id
                 WHERE r.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->loadFromRow($row);
            return $this;
        }
        return false;
    }
    
    /**
     * Récupère une réservation par sa référence
     */
    public function getByReference($reference) {
        $query = "SELECT r.*, c.nom as chambre_nom, c.type as chambre_type,
                 u.nom as client_nom, u.prenom as client_prenom, u.email as client_email
                 FROM " . $this->table_name . " r
                 JOIN chambres c ON r.chambre_id = c.id
                 JOIN users u ON r.user_id = u.id
                 WHERE r.reference = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $reference);
        $stmt->execute();
        
        if ($row = $stmt->fetch()) {
            $this->loadFromRow($row);
            return $this;
        }
        return false;
    }
    
    /**
     * Récupère toutes les réservations (admin)
     */
    public function getAll($statut = null, $limit = 50, $offset = 0) {
        $query = "SELECT r.*, c.nom as chambre_nom, c.type as chambre_type,
                 u.nom as user_nom, u.prenom as user_prenom, u.email as user_email
                 FROM " . $this->table_name . " r
                 JOIN chambres c ON r.chambre_id = c.id
                 JOIN users u ON r.user_id = u.id";
        
        $params = [];
        
        if ($statut) {
            $query .= " WHERE r.statut = ?";
            $params[] = $statut;
        }
        
        // Injecter directement les entiers pour LIMIT et OFFSET
        $limit = (int) $limit;
        $offset = (int) $offset;
        $query .= " ORDER BY r.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->conn->prepare($query);
        
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre de réservations par statut
     * @param string|null $statut Si spécifié, retourne le count pour ce statut uniquement
     */
    public function countByStatut($statut = null) {
        if ($statut !== null) {
            $query = "SELECT COUNT(*) as nb_reservations FROM " . $this->table_name . " WHERE statut = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $statut);
            $stmt->execute();
            $result = $stmt->fetch();
            return (int) ($result['nb_reservations'] ?? 0);
        }
        
        $query = "SELECT statut, COUNT(*) as nb_reservations, SUM(prix_total) as ca_total
                 FROM " . $this->table_name . "
                 GROUP BY statut
                 ORDER BY nb_reservations DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Génère une référence de réservation unique
     */
    private function generateReference() {
        do {
            $reference = 'SEGURO-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $query = "SELECT id FROM " . $this->table_name . " WHERE reference = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $reference);
            $stmt->execute();
        } while ($stmt->rowCount() > 0);
        
        return $reference;
    }
    
    /**
     * Calcule le nombre de nuits entre deux dates
     */
    private function calculerNuits($date_arrivee, $date_depart) {
        $arrivee = new DateTime($date_arrivee);
        $depart = new DateTime($date_depart);
        return $depart->diff($arrivee)->days;
    }
    
    /**
     * Charge les données depuis une ligne de résultat
     */
    private function loadFromRow($row) {
        $this->id = $row['id'];
        $this->reference = $row['reference'];
        $this->user_id = $row['user_id'];
        $this->chambre_id = $row['chambre_id'];
        $this->date_arrivee = $row['date_arrivee'];
        $this->date_depart = $row['date_depart'];
        $this->nb_adultes = $row['nb_adultes'];
        $this->nb_enfants = $row['nb_enfants'];
        $this->prix_nuit = $row['prix_nuit'];
        $this->prix_options = $row['prix_options'];
        $this->prix_total = $row['prix_total'];
        $this->statut = $row['statut'];
        $this->demandes_speciales = $row['demandes_speciales'];
        $this->note_admin = $row['note_admin'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        $this->valide_par = $row['valide_par'];
        $this->valide_at = $row['valide_at'];
    }
    
    /**
     * Retourne les données de la réservation pour le logging
     */
    private function getReservationData() {
        return [
            'reference' => $this->reference,
            'chambre_id' => $this->chambre_id,
            'date_arrivee' => $this->date_arrivee,
            'date_depart' => $this->date_depart,
            'nb_adultes' => $this->nb_adultes,
            'nb_enfants' => $this->nb_enfants,
            'prix_total' => $this->prix_total,
            'statut' => $this->statut
        ];
    }
    
    /**
     * Enregistre une action dans les logs
     */
    private function logAction($action, $user_id, $avant = null, $apres = null) {
        try {
            $query = "INSERT INTO logs_actions (user_id, action, table_cible, cible_id, avant, apres, ip_address)
                     VALUES (?, ?, 'reservations', ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            // Permettre user_id null
            $stmt->bindParam(1, $user_id, PDO::PARAM_NULL);
            $stmt->bindParam(2, $action);
            $stmt->bindParam(3, $this->id);
            $stmt->bindParam(4, $avant);
            $stmt->bindParam(5, $apres);
            $stmt->bindParam(6, $ip);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur logAction Reservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si une réservation appartient à un utilisateur
     */
    public function belongsTo($user_id) {
        return $this->user_id === $user_id;
    }
    
    /**
     * Formate le prix en FCFA
     */
    public function getPrixTotalFormate() {
        return number_format($this->prix_total, 0, ',', ' ') . ' FCFA';
    }
    
    /**
     * Retourne le libellé du statut
     */
    public function getStatutLibelle() {
        $statuts = [
            'en_cours' => 'En cours de validation',
            'validee' => 'Validée',
            'modifiee' => 'Modifiée',
            'annulee' => 'Annulée',
            'terminee' => 'Terminée'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }
    
    /**
     * Compte le nombre total de réservations
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['total'];
    }
}
?>
