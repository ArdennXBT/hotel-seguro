<?php
/**
 * ════════════════════════════════════════════════════════
 * SYSTÈME DE RÉSERVATION — Hôtel SEGURO
 * ════════════════════════════════════════════════════════
 */

session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Chambre.php';
require_once __DIR__ . '/../includes/Reservation.php';

// Initialisation
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$chambre = new Chambre($db);
$reservation = new Reservation($db);

// Variables pour la vue
$chambres_disponibles = [];
$chambre_selectionnee = null;
$dates_selectionnees = [
    'arrivee' => $_GET['arrivee'] ?? '',
    'depart' => $_GET['depart'] ?? ''
];
$etape = 1; // 1: recherche, 2: sélection, 3: formulaire, 4: confirmation
$erreur = '';
$succes = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Étape 1: Recherche de disponibilités
    if (isset($_POST['action_recherche'])) {
        $date_arrivee = $_POST['date_arrivee'] ?? '';
        $date_depart = $_POST['date_depart'] ?? '';
        $type_chambre = $_POST['type_chambre'] ?? 'all';
        
        if (empty($date_arrivee) || empty($date_depart)) {
            $erreur = 'Veuillez sélectionner une date d\'arrivée et de départ';
        } elseif (strtotime($date_arrivee) >= strtotime($date_depart)) {
            $erreur = 'La date de départ doit être postérieure à la date d\'arrivée';
        } elseif (strtotime($date_arrivee) < strtotime(date('Y-m-d'))) {
            $erreur = 'La date d\'arrivée ne peut être dans le passé';
        } else {
            $chambres_disponibles = $chambre->getAvailableForDates($date_arrivee, $date_depart, $type_chambre);
            $dates_selectionnees = ['arrivee' => $date_arrivee, 'depart' => $date_depart];
            
            if (empty($chambres_disponibles)) {
                $erreur = 'Aucune chambre disponible pour ces dates. Veuillez essayer d\'autres dates.';
            } else {
                $etape = 2;
            }
        }
    }
    
    // Étape 2: Sélection d'une chambre
    if (isset($_POST['action_selection'])) {
        $chambre_id = $_POST['chambre_id'] ?? '';
        $date_arrivee = $_POST['date_arrivee'] ?? '';
        $date_depart = $_POST['date_depart'] ?? '';
        
        if ($chambre_selectionnee = $chambre->getById($chambre_id)) {
            $dates_selectionnees = ['arrivee' => $date_arrivee, 'depart' => $date_depart];
            $etape = 3;
        } else {
            $erreur = 'Chambre non trouvée';
        }
    }
    
    // Étape 3: Création de la réservation
    if (isset($_POST['action_reservation'])) {
        $chambre_id = $_POST['chambre_id'] ?? '';
        $date_arrivee = $_POST['date_arrivee'] ?? '';
        $date_depart = $_POST['date_depart'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $pays = trim($_POST['pays'] ?? '');
        $nb_adultes = intval($_POST['nb_adultes'] ?? 1);
        $nb_enfants = intval($_POST['nb_enfants'] ?? 0);
        $demandes_speciales = trim($_POST['demandes_speciales'] ?? '');
        
        // Validation
        if (empty($nom) || empty($prenom) || empty($email)) {
            $erreur = 'Les champs nom, prénom et email sont obligatoires';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = 'Adresse email invalide';
        } elseif ($nb_adultes < 1) {
            $erreur = 'Au moins un adulte est requis';
        } else {
            try {
                // Créer ou récupérer le compte client automatiquement
                $client = $user->createAutoAccount($nom, $prenom, $email, $telephone, $pays);
                
                if ($client) {
                    // Créer la réservation
                    $new_reservation = $reservation->create(
                        $client->id,
                        $chambre_id,
                        $date_arrivee,
                        $date_depart,
                        $nb_adultes,
                        $nb_enfants,
                        $demandes_speciales
                    );
                    
                    if ($new_reservation) {
                        // Stocker les infos en session pour la confirmation
                        $_SESSION['reservation_id'] = $new_reservation->id;
                        $_SESSION['user_info'] = [
                            'nom' => $client->nom,
                            'prenom' => $client->prenom,
                            'email' => $client->email,
                            'code_client' => $client->code_client
                        ];
                        
                        $etape = 4;
                        $succes = 'Votre réservation a été créée avec succès !';
                        
                        // Envoyer l'email de confirmation (simulation)
                        // mail($email, 'Réservation Hôtel Seguro', "Votre code client: {$client->code_client}");
                    } else {
                        $erreur = 'Erreur lors de la création de la réservation';
                    }
                } else {
                    $erreur = 'Erreur lors de la création de votre compte';
                }
            } catch (Exception $e) {
                $erreur = 'Erreur: ' . $e->getMessage();
            }
        }
    }
}

// Récupérer les chambres pour les filtres
$types_chambres = $chambre->countByType();
$chambres_all = $chambre->getAllAvailable();

include(__DIR__ . '/../layouts/header.php');
?>

<style>
/* Variables CSS */
:root {
    --vert: #1a3a2a;
    --vert-clair: #2d5f47;
    --or: #c9a84c;
    --or-pale: #d4b873;
    --noir: #000000;
}

/* ════════════════════════════════════════════════════════
   SYSTÈME DE RÉSERVATION — Styles
═══════════════════════════════════════════════════════ */
.reservation-system {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.etape-indicator {
    display: flex;
    justify-content: center;
    margin-bottom: 60px;
    position: relative;
}

.etape {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 30px;
    position: relative;
}

.etape:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -15px;
    top: 20px;
    width: 30px;
    height: 2px;
    background: rgba(201,168,76,0.2);
}

.etape.active .etape-number {
    background: var(--or);
    color: white;
}

.etape.completed .etape-number {
    background: var(--vert);
    color: white;
}

.etape-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s;
    font-family: 'Jost', sans-serif;
    font-size: 0.9rem;
}

.etape-text {
    font-family: 'Jost', sans-serif;
    font-size: 0.8rem;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.etape.active .etape-text,
.etape.completed .etape-text {
    color: var(--vert);
}

.form-section {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    margin-bottom: 40px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    font-family: 'Jost', sans-serif;
    font-weight: 500;
    font-size: 0.9rem;
    color: var(--vert);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(201,168,76,0.2);
    border-radius: 8px;
    font-family: 'Jost', sans-serif;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--or);
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
}

.btn-primary {
    background: var(--vert);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 8px;
    font-family: 'Jost', sans-serif;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: var(--vert-clair);
    transform: translateY(-2px);
}

/* Style identique au bouton Réserver du header */
.btn-reserver {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    padding: 12px 28px;
    text-decoration: none;
    border: 1px solid var(--or);
    color: var(--or);
    background: transparent;
    display: inline-block;
    transition: background 0.3s, color 0.3s;
    cursor: pointer;
}
.btn-reserver:hover {
    background: var(--or);
    color: #fff;
}
.btn-reserver.plein {
    background: var(--vert);
    border-color: var(--vert);
    color: var(--or);
}
.btn-reserver.plein:hover {
    background: var(--vert-clair);
    border-color: var(--vert-clair);
}

.chambre-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 24px;
    margin-top: 32px;
}

.chambre-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
    cursor: pointer;
}

.chambre-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}

.chambre-card.selected {
    border: 2px solid var(--or);
}

.chambre-image {
    height: 200px;
    background: linear-gradient(45deg, var(--vert-clair), var(--or-pale));
    position: relative;
    overflow: hidden;
}

.chambre-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.chambre-info {
    padding: 24px;
}

.chambre-nom {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.4rem;
    color: var(--vert);
    margin-bottom: 8px;
}

.chambre-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
}

.chambre-prix {
    font-size: 1.2rem;
    color: var(--or);
    font-weight: 600;
}

.alert {
    padding: 16px 24px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-family: 'Jost', sans-serif;
}

.alert-success {
    background: rgba(26,58,42,0.1);
    color: var(--vert);
    border: 1px solid rgba(26,58,42,0.2);
}

.alert-error {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,0.2);
}

.confirmation-box {
    background: linear-gradient(135deg, rgba(26,58,42,0.05), rgba(201,168,76,0.05));
    border-radius: 12px;
    padding: 40px;
    text-align: center;
}

.confirmation-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: var(--vert);
    margin-bottom: 16px;
}

.confirmation-ref {
    font-size: 1.1rem;
    color: var(--or);
    font-weight: 600;
    margin-bottom: 24px;
}

.confirmation-code {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin: 24px 0;
    border: 1px solid rgba(201,168,76,0.2);
}

.code-label {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 8px;
}

.code-value {
    font-size: 1.4rem;
    color: var(--vert);
    font-weight: 600;
    letter-spacing: 0.1em;
}

@media (max-width: 768px) {
    .reservation-system {
        padding: 20px 16px;
    }
    
    .form-section {
        padding: 24px;
    }
    
    .etape-indicator {
        flex-direction: column;
        gap: 16px;
    }
    
    .etape:not(:last-child)::after {
        display: none;
    }
    
    .chambre-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="reservation-system " style="margin-top: 100px;">
    
    <!-- Indicateur d'étapes -->
    <div class="etape-indicator">
        <div class="etape <?= $etape >= 1 ? 'active' : '' ?>">
            <div class="etape-number">1</div>
            <div class="etape-text">Dates</div>
        </div>
        <div class="etape <?= $etape >= 2 ? 'active' : '' ?>">
            <div class="etape-number">2</div>
            <div class="etape-text">Chambre</div>
        </div>
        <div class="etape <?= $etape >= 3 ? 'active' : '' ?>">
            <div class="etape-number">3</div>
            <div class="etape-text">Informations</div>
        </div>
        <div class="etape <?= $etape >= 4 ? 'active' : '' ?>">
            <div class="etape-number">4</div>
            <div class="etape-text">Confirmation</div>
        </div>
    </div>

    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <!-- ÉTAPE 1: RECHERCHE DES DATES -->
    <?php if ($etape === 1): ?>
        <div class="form-section">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: var(--vert); margin-bottom: 32px; text-align: center;">
                Quand souhaitez-vous séjourner ?
            </h2>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Date d'arrivée</label>
                            <input type="date" name="date_arrivee" class="form-control" 
                                   value="<?= htmlspecialchars($dates_selectionnees['arrivee']) ?>"
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Date de départ</label>
                            <input type="date" name="date_depart" class="form-control" 
                                   value="<?= htmlspecialchars($dates_selectionnees['depart']) ?>"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Type de chambre</label>
                    <select name="type_chambre" class="form-control">
                        <option value="all">Tous les types</option>
                        <?php foreach ($types_chambres as $type): ?>
                            <option value="<?= $type['type'] ?>">
                                <?= ucfirst($type['type']) ?> (<?= $type['nb_chambres'] ?> chambres)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="text-align: center; margin-top: 32px;">
                    <button type="submit" name="action_recherche" class="btn-primary">
                        Vérifier la disponibilité
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- ÉTAPE 2: SÉLECTION DE LA CHAMBRE -->
    <?php if ($etape === 2): ?>
        <div class="form-section">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: var(--vert); margin-bottom: 16px; text-align: center;">
                Chambres disponibles
            </h2>
            <p style="text-align: center; color: #666; margin-bottom: 32px;">
                Du <?= date('d/m/Y', strtotime($dates_selectionnees['arrivee'])) ?> 
                au <?= date('d/m/Y', strtotime($dates_selectionnees['depart'])) ?>
            </p>
            
            <form method="post">
                <input type="hidden" name="date_arrivee" value="<?= htmlspecialchars($dates_selectionnees['arrivee']) ?>">
                <input type="hidden" name="date_depart" value="<?= htmlspecialchars($dates_selectionnees['depart']) ?>">
                
                <div class="chambre-grid">
                    <?php foreach ($chambres_disponibles as $ch): ?>
                        <div class="chambre-card" onclick="selectChambre('<?= $ch['id'] ?>', this)">
                            <div class="chambre-image">
                                <?php if ($ch['image_principale']): ?>
                                    <img src="<?= htmlspecialchars($ch['image_principale']) ?>" alt="<?= htmlspecialchars($ch['nom']) ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80" alt="<?= htmlspecialchars($ch['nom']) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="chambre-info">
                                <div class="chambre-nom"><?= htmlspecialchars($ch['nom']) ?></div>
                                <div style="color: #666; font-size: 0.9rem; margin-bottom: 8px;">
                                    <?= ucfirst($ch['type']) ?> • <?= $ch['superficie_m2'] ?> m² • 
                                    Max <?= $ch['capacite_max'] ?> personnes
                                </div>
                                <div class="chambre-details">
                                    <div class="chambre-prix"><?= number_format($ch['prix_nuit'], 0, ',', ' ') ?> FCFA/nuit</div>
                                    <button type="button" class="btn-primary" style="padding: 8px 20px; font-size: 0.8rem;"
                                            onclick="selectChambre('<?= $ch['id'] ?>', this)">
                                        Choisir
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <input type="hidden" name="chambre_id" id="chambre_id_selected">
                <div style="text-align: center; margin-top: 32px;">
                    <button type="submit" name="action_selection" class="btn-primary" id="btn_continue" disabled>
                        Continuer
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- ÉTAPE 3: FORMULAIRE CLIENT -->
    <?php if ($etape === 3): ?>
        <div class="form-section">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: var(--vert); margin-bottom: 32px; text-align: center;">
                Vos informations
            </h2>
            
            <form method="post">
                <input type="hidden" name="chambre_id" value="<?= htmlspecialchars($chambre_selectionnee->id) ?>">
                <input type="hidden" name="date_arrivee" value="<?= htmlspecialchars($dates_selectionnees['arrivee']) ?>">
                <input type="hidden" name="date_depart" value="<?= htmlspecialchars($dates_selectionnees['depart']) ?>">
                
                <!-- Récapitulatif de la réservation -->
                <div style="background: rgba(201,168,76,0.05); border-radius: 8px; padding: 20px; margin-bottom: 32px;">
                    <h4 style="color: var(--vert); margin-bottom: 12px;">Récapitulatif de votre séjour</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.9rem;">
                        <div><strong>Chambre:</strong> <?= htmlspecialchars($chambre_selectionnee->nom) ?></div>
                        <div><strong>Dates:</strong> Du <?= date('d/m/Y', strtotime($dates_selectionnees['arrivee'])) ?> au <?= date('d/m/Y', strtotime($dates_selectionnees['depart'])) ?></div>
                        <div><strong>Prix:</strong> <?= number_format($chambre_selectionnee->prix_nuit, 0, ',', ' ') ?> FCFA/nuit</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                    <small style="color: #666;">Un compte sera créé automatiquement avec ces informations</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pays</label>
                    <input type="text" name="pays" class="form-control">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nombre d'adultes *</label>
                            <select name="nb_adultes" class="form-control" required>
                                <?php for ($i = 1; $i <= $chambre_selectionnee->capacite_max; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == 1 ? 'selected' : '' ?>><?= $i ?> <?= $i > 1 ? 'adultes' : 'adulte' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nombre d'enfants</label>
                            <select name="nb_enfants" class="form-control">
                                <?php for ($i = 0; $i <= $chambre_selectionnee->capacite_enfants; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> <?= $i > 1 ? 'enfants' : 'enfant' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Demandes spéciales</label>
                    <textarea name="demandes_speciales" class="form-control" rows="3" 
                              placeholder="Allergies, préférences, besoins particuliers..."></textarea>
                </div>
                
                <div style="text-align: center; margin-top: 32px;">
                    <button type="submit" name="action_reservation" class="btn-primary">
                        Confirmer la réservation
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- ÉTAPE 4: CONFIRMATION -->
    <?php if ($etape === 4): ?>
        <div class="confirmation-box">
            <div class="confirmation-title">Réservation confirmée !</div>
            <div class="confirmation-ref">Référence: <?= htmlspecialchars($reservation->reference) ?></div>
            
            <p style="color: #666; margin-bottom: 24px;">
                Votre réservation a été enregistrée et est en attente de validation par notre équipe.
                Vous recevrez un email de confirmation shortly.
            </p>
            
            <div class="confirmation-code">
                <div class="code-label">VOS IDENTIFIANTS DE CONNEXION</div>
                <div class="code-value"><?= htmlspecialchars($_SESSION['user_info']['code_client']) ?></div>
                <div style="font-size: 0.9rem; color: #666; margin-top: 8px;">
                    Conservez ce code précieusement pour vous connecter et gérer votre réservation
                </div>
            </div>
            
            <div style="background: white; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: left;">
                <h4 style="color: var(--vert); margin-bottom: 12px;">Prochaines étapes:</h4>
                <ol style="color: #666; line-height: 1.8;">
                    <li>Notre équipe va valider votre réservation sous 24h</li>
                    <li>Vous recevrez un email de confirmation avec les détails</li>
                    <li>Vous pourrez vous connecter avec votre email et votre code client pour modifier votre réservation</li>
                    <li>Le paiement se fera directement à l'hôtel lors de votre arrivée</li>
                </ol>
            </div>
            
            <div style="margin-top: 32px;">
                <a href="connexion-client.php" class="btn-reserver plein" style="margin-right: 16px;">
                    Me connecter
                </a>
                <a href="chambres.php" class="btn-reserver plein">
                    Autres réservations
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function selectChambre(chambreId, el) {
    // Désélectionner toutes les cartes
    document.querySelectorAll('.chambre-card').forEach(function(card) {
        card.classList.remove('selected');
    });

    // Remonter jusqu'à la carte parente
    var card = el.closest('.chambre-card') || el;
    card.classList.add('selected');

    // Mettre à jour le champ hidden
    document.getElementById('chambre_id_selected').value = chambreId;

    // Activer le bouton continuer
    document.getElementById('btn_continue').disabled = false;
}
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>