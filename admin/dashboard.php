<?php
/**
 * ════════════════════════════════════════════════════════
 * DASHBOARD ADMIN — Hôtel SEGURO (Version Sidebar)
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
$stats = [
    'total' => $reservation->count(),
    'en_cours' => $reservation->countByStatut('en_cours'),
    'validee' => $reservation->countByStatut('validee'),
    'annulee' => $reservation->countByStatut('annulee'),
];

$reservations_recentes = $reservation->getAll(null, 10);
$reservations_en_attente = $reservation->getAll('en_cours', 20);

$erreur = '';
$succes = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_valider'])) {
        $reservation_id = $_POST['reservation_id'] ?? '';
        if ($reservation->getById($reservation_id) && $reservation->validate($_SESSION['user_id'])) {
            $succes = 'Réservation validée avec succès';
            $reservations_en_attente = $reservation->getAll('en_cours', 20);
        } else {
            $erreur = 'Erreur lors de la validation';
        }
    }
    
    if (isset($_POST['action_annuler'])) {
        $reservation_id = $_POST['reservation_id'] ?? '';
        if ($reservation->getById($reservation_id) && $reservation->cancel()) {
            $succes = 'Réservation annulée avec succès';
            $reservations_en_attente = $reservation->getAll('en_cours', 20);
        } else {
            $erreur = 'Erreur lors de l\'annulation';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Hôtel Seguro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --vert: #1a3a2a;
            --vert-clair: #2d5c40;
            --vert-fonce: #0f2418;
            --or: #c9a84c;
            --or-clair: #e0c068;
            --or-pale: #f5e9c4;
            --blanc: #faf8f3;
            --gris: #f5f5f5;
            --gris-fonce: #666;
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--gris);
            color: var(--vert);
            display: flex;
            min-height: 100vh;
        }
        
        /* ══════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════ */
        .sidebar {
            width: 260px;
            background: var(--vert);
            color: var(--blanc);
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 30px 24px;
            border-bottom: 1px solid rgba(201,168,76,0.2);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-logo i {
            font-size: 1.5rem;
            color: var(--or);
        }
        
        .sidebar-logo span {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            color: var(--or);
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .nav-section {
            padding: 0 20px 16px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(250,248,243,0.5);
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 24px;
            color: rgba(250,248,243,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover,
        .nav-item.active {
            background: rgba(201,168,76,0.1);
            color: var(--or);
            border-left-color: var(--or);
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        
        .nav-item span {
            font-size: 0.9rem;
            font-weight: 300;
        }
        
        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(201,168,76,0.2);
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--or);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vert);
            font-weight: 600;
        }
        
        .admin-details h4 {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--blanc);
        }
        
        .admin-details p {
            font-size: 0.75rem;
            color: rgba(250,248,243,0.6);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px 0;
            color: rgba(250,248,243,0.6);
            text-decoration: none;
            font-size: 0.8rem;
            transition: color 0.3s;
        }
        
        .logout-btn:hover {
            color: var(--or);
        }
        
        /* ══════════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════════ */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 400;
            color: var(--vert);
        }
        
        .page-title p {
            font-size: 0.85rem;
            color: var(--gris-fonce);
            margin-top: 4px;
        }
        
        .top-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: var(--or);
            color: var(--vert);
        }
        
        .btn-primary:hover {
            background: var(--or-clair);
        }
        
        /* ══════════════════════════════════════════
           STATS CARDS
        ══════════════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .stat-icon.blue { background: rgba(26,58,42,0.1); color: var(--vert); }
        .stat-icon.orange { background: rgba(201,168,76,0.15); color: var(--or); }
        .stat-icon.green { background: rgba(40,167,69,0.1); color: var(--success); }
        .stat-icon.red { background: rgba(220,53,69,0.1); color: var(--danger); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 500;
            color: var(--vert);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--gris-fonce);
            margin-top: 4px;
        }
        
        /* ══════════════════════════════════════════
           CONTENT GRID
        ══════════════════════════════════════════ */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--vert);
        }
        
        .card-body {
            padding: 0;
        }
        
        /* ══════════════════════════════════════════
           TABLE
        ══════════════════════════════════════════ */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            text-align: left;
            padding: 16px 24px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gris-fonce);
            font-weight: 500;
            background: #fafafa;
            border-bottom: 1px solid #f0f0f0;
        }
        
        td {
            padding: 16px 24px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f8f8f8;
        }
        
        tr:hover td {
            background: #fafafa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-warning {
            background: rgba(255,193,7,0.15);
            color: #856404;
        }
        
        .badge-success {
            background: rgba(40,167,69,0.15);
            color: #155724;
        }
        
        .badge-danger {
            background: rgba(220,53,69,0.15);
            color: #721c24;
        }
        
        .badge-info {
            background: rgba(23,162,184,0.15);
            color: #0c5460;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        /* ══════════════════════════════════════════
           ALERTS
        ══════════════════════════════════════════ */
        .alert {
            padding: 16px 24px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: rgba(40,167,69,0.1);
            color: var(--success);
            border: 1px solid rgba(40,167,69,0.2);
        }
        
        .alert-danger {
            background: rgba(220,53,69,0.1);
            color: var(--danger);
            border: 1px solid rgba(220,53,69,0.2);
        }
        
        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-logo span,
            .nav-item span,
            .nav-section,
            .admin-details {
                display: none;
            }
            .main-content {
                margin-left: 70px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- ══════════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════════ -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-crown"></i>
                <span>Hôtel Seguro</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">Principal</div>
            <a href="#" class="nav-item active">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="../pages/calendrier-disponibilites.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendrier</span>
            </a>
            
            <div class="nav-section" style="margin-top: 20px;">Gestion</div>
            <a href="reservations.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Réservations</span>
            </a>
            <a href="clients.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Clients</span>
            </a>
            <a href="chambres.php" class="nav-item">
                <i class="fas fa-bed"></i>
                <span>Chambres</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="admin-info">
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="admin-details">
                    <h4><?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></h4>
                    <p>Administrateur</p>
                </div>
            </div>
            <a href="../pages/deconnexion.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>
    
    <!-- ══════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════ -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <h1>Tableau de bord</h1>
                <p>Bienvenue, voici l'aperçu de votre hôtel aujourd'hui</p>
            </div>
            <div class="top-actions">
                <a href="../pages/reservation-system.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nouvelle réservation
                </a>
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if ($succes): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $succes; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($erreur): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $erreur; ?>
        </div>
        <?php endif; ?>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Réservations</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['en_cours']; ?></div>
                <div class="stat-label">En attente</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['validee']; ?></div>
                <div class="stat-label">Validées</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $stats['annulee']; ?></div>
                <div class="stat-label">Annulées</div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Réservations en attente -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock" style="color: var(--or); margin-right: 8px;"></i>
                        Réservations en attente
                    </h3>
                    <span class="badge badge-warning"><?php echo count($reservations_en_attente); ?> en cours</span>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Chambre</th>
                                    <th>Dates</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations_en_attente as $res): ?>
                                <tr>
                                    <td><strong><?php echo $res['reference']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($res['user_nom'] . ' ' . $res['user_prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($res['chambre_nom']); ?></td>
                                    <td>
                                        <?php 
                                        echo date('d/m', strtotime($res['date_arrivee'])) . ' - ' . 
                                             date('d/m', strtotime($res['date_depart'])); 
                                        ?>
                                    </td>
                                    <td style="color: var(--or); font-weight: 500;">
                                        <?php echo number_format($res['prix_total'], 0, ',', ' '); ?> FCFA
                                    </td>
                                    <td>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                            <div class="actions">
                                                <button type="submit" name="action_valider" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="submit" name="action_annuler" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Widgets -->
            <div>
                <!-- Quick Actions -->
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header">
                        <h3 class="card-title">Actions rapides</h3>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <a href="../pages/calendrier-disponibilites.php" class="nav-item" style="padding: 12px 0; border-left: none;">
                            <i class="fas fa-calendar-check"></i>
                            <span>Voir les disponibilités</span>
                        </a>
                        <a href="../pages/chambres.php" class="nav-item" style="padding: 12px 0; border-left: none;">
                            <i class="fas fa-bed"></i>
                            <span>Gérer les chambres</span>
                        </a>
                        <a href="../pages/mon-compte.php" class="nav-item" style="padding: 12px 0; border-left: none;">
                            <i class="fas fa-user"></i>
                            <span>Mon profil</span>
                        </a>
                    </div>
                </div>
                
                <!-- Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informations</h3>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <i class="fas fa-envelope" style="color: var(--or);"></i>
                            <div>
                                <p style="font-size: 0.8rem; color: #888;">Contact réservations</p>
                                <p style="font-size: 0.9rem; color: var(--vert);">reservations@hotelseguro.com</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px 0;">
                            <i class="fas fa-phone" style="color: var(--or);"></i>
                            <div>
                                <p style="font-size: 0.8rem; color: #888;">Téléphone</p>
                                <p style="font-size: 0.9rem; color: var(--vert);">+228 90 00 00 00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php
// Fonctions manquantes à ajouter dans les classes si nécessaires
if (!method_exists($reservation, 'count')) {
    function countReservations($db) {
        $query = "SELECT COUNT(*) FROM reservations";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
