<?php
/**
 * ════════════════════════════════════════════════════════
 * DASHBOARD ADMIN — Hôtel SEGURO
 * ════════════════════════════════════════════════════════
 */

session_start();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    header('Location: ../pages/connexion-client.php');
    exit;
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/Reservation.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Chambre.php';

$database = new Database();
$db = $database->getConnection();
$reservation = new Reservation($db);
$user = new User($db);
$chambre = new Chambre($db);

// Récupérer les statistiques
$stats_reservations = $reservation->countByStatut();
$reservations_recentes = $reservation->getAll(null, 10);
$reservations_en_attente = $reservation->getAll('en_cours', 20);

$erreur = '';
$succes = '';

// Traitement des actions admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validation de réservation
    if (isset($_POST['action_valider'])) {
        $reservation_id = $_POST['reservation_id'] ?? '';
        
        if ($reservation->getById($reservation_id)) {
            if ($reservation->validate($_SESSION['user_id'])) {
                $succes = 'Réservation validée avec succès';
                // Rafraîchir les listes
                $reservations_recentes = $reservation->getAll(null, 10);
                $reservations_en_attente = $reservation->getAll('en_cours', 20);
                $stats_reservations = $reservation->countByStatut();
            } else {
                $erreur = 'Erreur lors de la validation de la réservation';
            }
        } else {
            $erreur = 'Réservation introuvable';
        }
    }
    
    // Annulation de réservation
    if (isset($_POST['action_annuler_admin'])) {
        $reservation_id = $_POST['reservation_id'] ?? '';
        
        if ($reservation->getById($reservation_id)) {
            if ($reservation->cancel()) {
                $succes = 'Réservation annulée avec succès';
                // Rafraîchir les listes
                $reservations_recentes = $reservation->getAll(null, 10);
                $reservations_en_attente = $reservation->getAll('en_cours', 20);
                $stats_reservations = $reservation->countByStatut();
            } else {
                $erreur = 'Erreur lors de l\'annulation de la réservation';
            }
        } else {
            $erreur = 'Réservation introuvable';
        }
    }
}

include(__DIR__ . '/../layouts/header.php');
?>

<style>
/* ════════════════════════════════════════════════════════
   DASHBOARD ADMIN — Styles
═══════════════════════════════════════════════════════ */
.admin-container {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
}

.admin-header {
    background: linear-gradient(135deg, var(--vert), var(--vert-clair));
    color: white;
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.admin-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.admin-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.5rem;
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
}

.admin-subtitle {
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--or);
}

.stat-card.en-cours::before { background: #ffc107; }
.stat-card.validee::before { background: #28a745; }
.stat-card.modifiee::before { background: #17a2b8; }
.stat-card.annulee::before { background: #dc3545; }
.stat-card.terminee::before { background: #6c757d; }

.stat-number {
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--vert);
    margin-bottom: 8px;
}

.stat-label {
    font-family: 'Jost', sans-serif;
    font-size: 1rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
}

.stat-ca {
    font-size: 1.2rem;
    color: var(--or);
    font-weight: 500;
}

.section-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: var(--vert);
    margin-bottom: 24px;
    position: relative;
    padding-bottom: 12px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 2px;
    background: var(--or);
}

.reservations-section {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 40px;
}

.reservations-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 24px;
}

.reservations-table th {
    font-family: 'Jost', sans-serif;
    font-weight: 600;
    color: var(--vert);
    text-align: left;
    padding: 12px 16px;
    border-bottom: 2px solid var(--or);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.reservations-table td {
    padding: 16px;
    border-bottom: 1px solid rgba(201,168,76,0.1);
    vertical-align: middle;
}

.reservations-table tr:hover {
    background: rgba(201,168,76,0.02);
}

.reservation-ref {
    font-family: 'Jost', sans-serif;
    font-weight: 600;
    color: var(--vert);
}

.reservation-client {
    color: #666;
    font-size: 0.9rem;
}

.reservation-chambre {
    color: var(--vert);
    font-weight: 500;
}

.reservation-dates {
    font-size: 0.85rem;
    color: #666;
}

.statut-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
}

.statut-en_cours {
    background: rgba(255,193,7,0.1);
    color: #ffc107;
}

.statut-validee {
    background: rgba(40,167,69,0.1);
    color: #28a745;
}

.statut-modifiee {
    background: rgba(23,162,184,0.1);
    color: #17a2b8;
}

.statut-annulee {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
}

.statut-terminee {
    background: rgba(108,117,125,0.1);
    color: #6c757d;
}

.reservation-prix {
    font-weight: 600;
    color: var(--or);
}

.reservation-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-admin {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-family: 'Jost', sans-serif;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-valider {
    background: var(--vert);
    color: white;
}

.btn-valider:hover {
    background: var(--vert-clair);
    transform: translateY(-1px);
}

.btn-annuler {
    background: #dc3545;
    color: white;
}

.btn-annuler:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.btn-detail {
    background: transparent;
    color: var(--vert);
    border: 1px solid var(--vert);
}

.btn-detail:hover {
    background: var(--vert);
    color: white;
}

.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-family: 'Jost', sans-serif;
}

.alert-success {
    background: rgba(40,167,69,0.1);
    color: #28a745;
    border: 1px solid rgba(40,167,69,0.2);
}

.alert-error {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,0.2);
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.admin-nav {
    display: flex;
    gap: 20px;
    margin-bottom: 32px;
    border-bottom: 1px solid rgba(201,168,76,0.1);
    padding-bottom: 16px;
}

.nav-link {
    color: #666;
    text-decoration: none;
    font-family: 'Jost', sans-serif;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s;
}

.nav-link:hover,
.nav-link.active {
    color: var(--vert);
    background: rgba(26,58,42,0.05);
}

@media (max-width: 768px) {
    .admin-container {
        margin: 20px auto;
        padding: 0 16px;
    }
    
    .admin-header {
        padding: 32px 24px;
    }
    
    .admin-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .reservations-section {
        padding: 24px 16px;
    }
    
    .reservations-table {
        font-size: 0.85rem;
    }
    
    .reservations-table th,
    .reservations-table td {
        padding: 8px;
    }
    
    .reservation-actions {
        flex-direction: column;
    }
    
    .admin-nav {
        flex-wrap: wrap;
    }
}
</style>

<div class="admin-container">
    
    <!-- En-tête admin -->
    <div class="admin-header">
        <h1 class="admin-title">Tableau de bord administrateur</h1>
        <p class="admin-subtitle">Gestion des réservations Hôtel Seguro</p>
    </div>

    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="admin-nav">
        <a href="#stats" class="nav-link active">Statistiques</a>
        <a href="#en-attente" class="nav-link">Réservations en attente</a>
        <a href="#recentes" class="nav-link">Réservations récentes</a>
        <a href="../pages/calendrier-disponibilites.php" class="nav-link">Calendrier</a>
        <a href="../pages/mon-compte.php" class="nav-link">Mon compte</a>
        <a href="../pages/connexion-client.php?logout=1" class="nav-link" style="color: #dc3545;">Déconnexion</a>
    </div>

    <!-- Section Statistiques -->
    <section id="stats">
        <h2 class="section-title">Statistiques des réservations</h2>
        
        <div class="stats-grid">
            <?php foreach ($stats_reservations as $stat): ?>
                <div class="stat-card <?= $stat['statut'] ?>">
                    <div class="stat-number"><?= $stat['nb_reservations'] ?></div>
                    <div class="stat-label">
                        <?php
                        $libelles = [
                            'en_cours' => 'En cours',
                            'validee' => 'Validées',
                            'modifiee' => 'Modifiées',
                            'annulee' => 'Annulées',
                            'terminee' => 'Terminées'
                        ];
                        echo $libelles[$stat['statut']] ?? ucfirst($stat['statut']);
                        ?>
                    </div>
                    <?php if ($stat['ca_total'] > 0): ?>
                        <div class="stat-ca"><?= number_format($stat['ca_total'], 0, ',', ' ') ?> FCFA</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section Réservations en attente -->
    <section id="en-attente" class="reservations-section">
        <h2 class="section-title">Réservations en attente de validation</h2>
        
        <?php if (empty($reservations_en_attente)): ?>
            <div class="empty-state">
                Aucune réservation en attente de validation
            </div>
        <?php else: ?>
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Chambre</th>
                        <th>Dates</th>
                        <th>Personnes</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations_en_attente as $res): ?>
                        <tr>
                            <td>
                                <div class="reservation-ref"><?= htmlspecialchars($res['reference']) ?></div>
                                <div class="reservation-client">
                                    Créée le <?= date('d/m/Y', strtotime($res['created_at'])) ?>
                                </div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($res['client_nom']) ?> <?= htmlspecialchars($res['client_prenom']) ?></div>
                                <div class="reservation-client"><?= htmlspecialchars($res['client_email']) ?></div>
                            </td>
                            <td>
                                <div class="reservation-chambre"><?= htmlspecialchars($res['chambre_nom']) ?></div>
                                <div class="reservation-client"><?= ucfirst($res['chambre_type']) ?></div>
                            </td>
                            <td>
                                <div class="reservation-dates">
                                    <?= date('d/m/Y', strtotime($res['date_arrivee'])) ?><br>
                                    au <?= date('d/m/Y', strtotime($res['date_depart'])) ?>
                                </div>
                            </td>
                            <td>
                                <?= $res['nb_adultes'] ?> adulte(s)
                                <?php if ($res['nb_enfants'] > 0): ?>
                                    + <?= $res['nb_enfants'] ?> enfant(s)
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="reservation-prix"><?= number_format($res['prix_total'], 0, ',', ' ') ?> FCFA</div>
                            </td>
                            <td>
                                <span class="statut-badge statut-<?= $res['statut'] ?>">
                                    <?= $reservation->getStatutLibelle($res['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="reservation-actions">
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                        <button type="submit" name="action_valider" class="btn-admin btn-valider"
                                                onclick="return confirm('Valider cette réservation ?')">
                                            Valider
                                        </button>
                                    </form>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                        <button type="submit" name="action_annuler_admin" class="btn-admin btn-annuler"
                                                onclick="return confirm('Annuler cette réservation ?')">
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <!-- Section Réservations récentes -->
    <section id="recentes" class="reservations-section">
        <h2 class="section-title">Réservations récentes</h2>
        
        <?php if (empty($reservations_recentes)): ?>
            <div class="empty-state">
                Aucune réservation récente
            </div>
        <?php else: ?>
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Chambre</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations_recentes as $res): ?>
                        <tr>
                            <td>
                                <div class="reservation-ref"><?= htmlspecialchars($res['reference']) ?></div>
                                <div class="reservation-client">
                                    <?= date('d/m/Y H:i', strtotime($res['created_at'])) ?>
                                </div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($res['client_nom']) ?> <?= htmlspecialchars($res['client_prenom']) ?></div>
                                <div class="reservation-client"><?= htmlspecialchars($res['client_email']) ?></div>
                            </td>
                            <td>
                                <div class="reservation-chambre"><?= htmlspecialchars($res['chambre_nom']) ?></div>
                                <div class="reservation-client"><?= ucfirst($res['chambre_type']) ?></div>
                            </td>
                            <td>
                                <div class="reservation-dates">
                                    <?= date('d/m/Y', strtotime($res['date_arrivee'])) ?> → 
                                    <?= date('d/m/Y', strtotime($res['date_depart'])) ?>
                                </div>
                            </td>
                            <td>
                                <div class="reservation-prix"><?= number_format($res['prix_total'], 0, ',', ' ') ?> FCFA</div>
                            </td>
                            <td>
                                <span class="statut-badge statut-<?= $res['statut'] ?>">
                                    <?= $reservation->getStatutLibelle($res['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="reservation-actions">
                                    <?php if ($res['statut'] === 'en_cours'): ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                            <button type="submit" name="action_valider" class="btn-admin btn-valider">
                                                Valider
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if (!in_array($res['statut'], ['terminee', 'annulee'])): ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                            <button type="submit" name="action_annuler_admin" class="btn-admin btn-annuler">
                                                Annuler
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <button class="btn-admin btn-detail" onclick="showDetails(<?= htmlspecialchars(json_encode($res)) ?>)">
                                        Détails
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>

<script>
function showDetails(reservation) {
    const details = `
Réservation: ${reservation.reference}
Client: ${reservation.client_nom} ${reservation.client_prenom}
Email: ${reservation.client_email}
Chambre: ${reservation.chambre_nom} (${reservation.chambre_type})
Dates: ${new Date(reservation.date_arrivee).toLocaleDateString()} au ${new Date(reservation.date_depart).toLocaleDateString()}
Personnes: ${reservation.nb_adultes} adulte(s)${reservation.nb_enfants > 0 ? ' + ' + reservation.nb_enfants + ' enfant(s)' : ''}
Total: ${new Intl.NumberFormat('fr-FR').format(reservation.prix_total)} FCFA
Statut: ${reservation.statut}
${reservation.demandes_speciales ? 'Demandes: ' + reservation.demandes_speciales : ''}
    `;
    
    alert(details);
}

// Smooth scroll pour la navigation
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Mettre à jour le lien actif
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        }
    });
});
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>
