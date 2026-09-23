<?php
/**
 * ADMIN CHAMBRES — Hôtel SEGURO
 */
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    header('Location: ../pages/connexion-client.php');
    exit;
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/Chambre.php';

$database = new Database();
$db = $database->getConnection();

$succes = '';
$erreur = '';

// ════════════════════════════════════════════════════════
// TRAITEMENT POST
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Ajouter une chambre ──
    if ($action === 'ajouter') {
        $nom              = trim($_POST['nom'] ?? '');
        $type             = trim($_POST['type'] ?? '');
        $superficie       = intval($_POST['superficie_m2'] ?? 0);
        $prix             = floatval($_POST['prix_nuit'] ?? 0);
        $capacite_max     = intval($_POST['capacite_max'] ?? 2);
        $capacite_enfants = intval($_POST['capacite_enfants'] ?? 0);
        $description      = trim($_POST['description'] ?? '');
        $image            = trim($_POST['image_principale'] ?? '');
        $etage            = intval($_POST['etage'] ?? 0);
        $numero           = intval($_POST['numero'] ?? 0);
        $disponible       = isset($_POST['disponible']) ? 1 : 0;

        // Amenities : checkboxes + texte libre
        $amenities_sel  = $_POST['amenities_sel'] ?? [];
        $amenities_libre = array_filter(array_map('trim', explode(',', $_POST['amenities_libre'] ?? '')));
        $amenities = array_values(array_unique(array_merge($amenities_sel, $amenities_libre)));

        if (!$nom || !$type || !$prix) {
            $erreur = 'Nom, type et prix sont obligatoires.';
        } else {
            try {
                $id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff),
                    mt_rand(0,0x0fff)|0x4000,mt_rand(0,0x3fff)|0x8000,
                    mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff));

                $stmt = $db->prepare("INSERT INTO chambres
                    (id,nom,type,superficie_m2,prix_nuit,capacite_max,capacite_enfants,description,amenities,image_principale,etage,numero,disponible)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $id, $nom, $type, $superficie, $prix,
                    $capacite_max, $capacite_enfants, $description,
                    json_encode($amenities, JSON_UNESCAPED_UNICODE),
                    $image, $etage, $numero, $disponible
                ]);
                $succes = "Chambre \"$nom\" ajoutée avec succès.";
            } catch (PDOException $e) {
                $erreur = 'Erreur SQL : ' . $e->getMessage();
            }
        }
    }

    // ── Modifier une chambre ──
    if ($action === 'modifier') {
        $id               = trim($_POST['id'] ?? '');
        $nom              = trim($_POST['nom'] ?? '');
        $type             = trim($_POST['type'] ?? '');
        $superficie       = intval($_POST['superficie_m2'] ?? 0);
        $prix             = floatval($_POST['prix_nuit'] ?? 0);
        $capacite_max     = intval($_POST['capacite_max'] ?? 2);
        $capacite_enfants = intval($_POST['capacite_enfants'] ?? 0);
        $description      = trim($_POST['description'] ?? '');
        $image            = trim($_POST['image_principale'] ?? '');
        $etage            = intval($_POST['etage'] ?? 0);
        $numero           = intval($_POST['numero'] ?? 0);
        $disponible       = isset($_POST['disponible']) ? 1 : 0;

        $amenities_sel   = $_POST['amenities_sel'] ?? [];
        $amenities_libre = array_filter(array_map('trim', explode(',', $_POST['amenities_libre'] ?? '')));
        $amenities = array_values(array_unique(array_merge($amenities_sel, $amenities_libre)));

        if (!$id || !$nom || !$type || !$prix) {
            $erreur = 'Champs obligatoires manquants.';
        } else {
            try {
                $stmt = $db->prepare("UPDATE chambres SET
                    nom=?,type=?,superficie_m2=?,prix_nuit=?,capacite_max=?,
                    capacite_enfants=?,description=?,amenities=?,image_principale=?,
                    etage=?,numero=?,disponible=? WHERE id=?");
                $stmt->execute([
                    $nom,$type,$superficie,$prix,$capacite_max,
                    $capacite_enfants,$description,
                    json_encode($amenities, JSON_UNESCAPED_UNICODE),
                    $image,$etage,$numero,$disponible,$id
                ]);
                $succes = "Chambre \"$nom\" modifiée avec succès.";
            } catch (PDOException $e) {
                $erreur = 'Erreur SQL : ' . $e->getMessage();
            }
        }
    }

    // ── Supprimer une chambre ──
    if ($action === 'supprimer') {
        $id = trim($_POST['id'] ?? '');
        if ($id) {
            try {
                $stmt = $db->prepare("DELETE FROM chambres WHERE id=?");
                $stmt->execute([$id]);
                $succes = 'Chambre supprimée.';
            } catch (PDOException $e) {
                $erreur = 'Impossible de supprimer (réservations liées?) : ' . $e->getMessage();
            }
        }
    }

    // ── Toggle disponibilité (AJAX) ──
    if ($action === 'toggle_dispo') {
        header('Content-Type: application/json');
        $id    = trim($_POST['id'] ?? '');
        $dispo = intval($_POST['disponible'] ?? 0);
        try {
            $db->prepare("UPDATE chambres SET disponible=? WHERE id=?")->execute([$dispo, $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// ── Récupérer toutes les chambres ──
$chambres = $db->query("SELECT * FROM chambres ORDER BY type, prix_nuit ASC")->fetchAll();
$total    = count($chambres);

$AMENITIES_STANDARDS = [
    'Wifi fibre','Smart TV','Climatisation','Minibar','Coffre-fort',
    'Balcon','Terrasse','Vue mer','Vue jardin','Baignoire',
    'Douche italienne','Butler 24h','Piscine privée','Cuisine équipée',
    'Salon séparé','Dressing','Room service',
];
$TYPES = ['standard'=>'Standard','superieure'=>'Supérieure','suite'=>'Suite','villa'=>'Villa'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chambres — Admin Hôtel Seguro</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root{--vert:#1a3a2a;--vert-clair:#2d5c40;--or:#c9a84c;--blanc:#faf8f3;--gris:#f5f5f5;--gris-fonce:#666;--danger:#dc3545;--success:#28a745;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Jost',sans-serif;background:var(--gris);color:var(--vert);display:flex;min-height:100vh;}
/* Sidebar */
.sidebar{width:260px;background:var(--vert);color:var(--blanc);position:fixed;height:100vh;display:flex;flex-direction:column;z-index:1000;}
.sidebar-header{padding:30px 24px;border-bottom:1px solid rgba(201,168,76,.2);}
.sidebar-logo{display:flex;align-items:center;gap:12px;}
.sidebar-logo i{font-size:1.5rem;color:var(--or);}
.sidebar-logo span{font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:var(--or);}
.sidebar-nav{flex:1;padding:20px 0;overflow-y:auto;}
.nav-section{padding:0 20px 16px;font-size:.7rem;text-transform:uppercase;letter-spacing:.15em;color:rgba(250,248,243,.5);}
.nav-item{display:flex;align-items:center;gap:14px;padding:14px 24px;color:rgba(250,248,243,.8);text-decoration:none;transition:all .3s;border-left:3px solid transparent;}
.nav-item:hover,.nav-item.active{background:rgba(201,168,76,.1);color:var(--or);border-left-color:var(--or);}
.nav-item i{width:20px;text-align:center;}
.nav-item span{font-size:.9rem;font-weight:300;}
.sidebar-footer{padding:20px 24px;border-top:1px solid rgba(201,168,76,.2);}
.admin-info{display:flex;align-items:center;gap:12px;}
.admin-avatar{width:40px;height:40px;border-radius:50%;background:var(--or);display:flex;align-items:center;justify-content:center;color:var(--vert);font-weight:600;}
.admin-details h4{font-size:.9rem;font-weight:400;color:var(--blanc);}
.admin-details p{font-size:.75rem;color:rgba(250,248,243,.6);}
.logout-btn{display:flex;align-items:center;gap:8px;margin-top:12px;padding:8px 0;color:rgba(250,248,243,.6);text-decoration:none;font-size:.8rem;transition:color .3s;}
.logout-btn:hover{color:var(--or);}
/* Main */
.main-content{flex:1;margin-left:260px;padding:30px;}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e0e0e0;}
.page-title h1{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:400;}
.page-title p{font-size:.85rem;color:var(--gris-fonce);margin-top:4px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:4px;font-size:.85rem;transition:all .3s;cursor:pointer;border:none;font-family:'Jost',sans-serif;text-decoration:none;}
.btn-primary{background:var(--or);color:var(--vert);}
.btn-primary:hover{background:#b8962a;}
.btn-danger{background:var(--danger);color:#fff;}
.btn-sm{padding:5px 10px;font-size:.75rem;}
.btn-success{background:var(--success);color:#fff;}
.alert{padding:14px 20px;border-radius:6px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:.9rem;}
.alert-success{background:rgba(40,167,69,.1);color:var(--success);border:1px solid rgba(40,167,69,.2);}
.alert-danger{background:rgba(220,53,69,.1);color:var(--danger);border:1px solid rgba(220,53,69,.2);}
/* Grille chambres */
.chambres-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;}
.ch-card{background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);transition:box-shadow .3s;}
.ch-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.12);}
.ch-img{height:200px;overflow:hidden;position:relative;}
.ch-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
.ch-card:hover .ch-img img{transform:scale(1.04);}
.ch-img-placeholder{width:100%;height:100%;background:linear-gradient(135deg,#e8f0ea,#d4e4d8);display:flex;align-items:center;justify-content:center;font-size:3rem;color:rgba(26,58,42,.2);}
.ch-badge{position:absolute;top:12px;left:12px;background:rgba(26,58,42,.8);color:var(--or);font-size:.6rem;letter-spacing:.2em;text-transform:uppercase;padding:4px 12px;backdrop-filter:blur(6px);}
.ch-dispo{position:absolute;top:12px;right:12px;width:10px;height:10px;border-radius:50%;cursor:pointer;}
.ch-dispo.on{background:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.3);}
.ch-dispo.off{background:#dc3545;box-shadow:0 0 0 3px rgba(220,53,69,.3);}
.ch-body{padding:20px;}
.ch-num{font-size:.65rem;letter-spacing:.25em;color:rgba(201,168,76,.5);text-transform:uppercase;margin-bottom:4px;}
.ch-name{font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:var(--vert);margin-bottom:6px;}
.ch-desc{font-size:.78rem;color:#888;line-height:1.6;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ch-amenities{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px;}
.ch-amenity{font-size:.6rem;letter-spacing:.15em;text-transform:uppercase;color:#999;border:1px solid rgba(201,168,76,.2);padding:3px 8px;border-radius:2px;}
.ch-footer{display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f0f0f0;}
.ch-price{font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:var(--or);}
.ch-price small{font-family:'Jost',sans-serif;font-size:.65rem;color:#bbb;font-weight:300;}
.ch-actions{display:flex;gap:6px;}
.ch-meta{display:flex;gap:12px;font-size:.72rem;color:#aaa;margin-bottom:10px;}
.ch-meta span{display:flex;align-items:center;gap:4px;}
/* Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9000;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#fff;border-radius:16px;width:700px;max-width:100%;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.25);}
.modal-header{padding:28px 32px 20px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:2;}
.modal-title{font-family:'Cormorant Garamond',serif;font-size:1.5rem;color:var(--vert);}
.modal-close{background:none;border:none;font-size:20px;cursor:pointer;color:#aaa;}
.modal-close:hover{color:#333;}
.modal-body{padding:24px 32px 32px;}
.form-grid{display:grid;gap:16px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-group label{font-size:.78rem;font-weight:500;color:#555;}
.form-group input,.form-group select,.form-group textarea{padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-family:'Jost',sans-serif;font-size:.88rem;transition:border .2s;}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--or);}
.form-group textarea{resize:vertical;min-height:80px;}
.amenities-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px;}
.amenity-check{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#555;cursor:pointer;}
.amenity-check input{accent-color:var(--or);width:14px;height:14px;}
.form-section{font-size:.7rem;text-transform:uppercase;letter-spacing:.15em;color:var(--or);font-weight:600;margin:16px 0 8px;padding-bottom:6px;border-bottom:1px solid rgba(201,168,76,.15);}
.toggle-dispo{display:flex;align-items:center;gap:10px;margin-top:6px;}
.toggle-switch{position:relative;width:44px;height:24px;}
.toggle-switch input{opacity:0;width:0;height:0;}
.toggle-slider{position:absolute;inset:0;background:#ccc;border-radius:24px;cursor:pointer;transition:.3s;}
.toggle-slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;}
.toggle-switch input:checked + .toggle-slider{background:var(--success);}
.toggle-switch input:checked + .toggle-slider:before{transform:translateX(20px);}
.form-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #f0f0f0;}
.img-preview{width:100%;height:140px;object-fit:cover;border-radius:6px;margin-top:8px;display:none;}
.stats-bar{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;}
.stat-mini{background:#fff;border-radius:6px;padding:14px 16px;box-shadow:0 2px 8px rgba(0,0,0,.05);text-align:center;}
.stat-mini-val{font-size:1.4rem;font-weight:500;color:var(--vert);}
.stat-mini-label{font-size:.7rem;color:#aaa;margin-top:2px;text-transform:uppercase;letter-spacing:.08em;}
@media(max-width:768px){.sidebar{width:70px;}.sidebar-logo span,.nav-item span,.nav-section,.admin-details{display:none;}.main-content{margin-left:70px;}.chambres-grid{grid-template-columns:1fr;}.stats-bar{grid-template-columns:repeat(2,1fr);}.form-row,.form-row-3{grid-template-columns:1fr;}.amenities-grid{grid-template-columns:repeat(2,1fr);}}
</style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo"><i class="fas fa-crown"></i><span>Hôtel Seguro</span></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
        <a href="../pages/calendrier-disponibilites.php" class="nav-item"><i class="fas fa-calendar-alt"></i><span>Calendrier</span></a>
        <div class="nav-section" style="margin-top:20px;">Gestion</div>
        <a href="reservations.php" class="nav-item"><i class="fas fa-book"></i><span>Réservations</span></a>
        <a href="clients.php" class="nav-item"><i class="fas fa-users"></i><span>Clients</span></a>
        <a href="chambres.php" class="nav-item active"><i class="fas fa-bed"></i><span>Chambres</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['user_prenom']??'A',0,1)) ?></div>
            <div class="admin-details">
                <h4><?= htmlspecialchars(($_SESSION['user_prenom']??'').' '.($_SESSION['user_nom']??'')) ?></h4>
                <p>Administrateur</p>
            </div>
        </div>
        <a href="../pages/deconnexion.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</aside>

<main class="main-content">

    <div class="top-bar">
        <div class="page-title">
            <h1>Chambres & Suites</h1>
            <p>Gérer l'inventaire des hébergements</p>
        </div>
        <button class="btn btn-primary" onclick="ouvrirAjout()">
            <i class="fas fa-plus"></i> Ajouter une chambre
        </button>
    </div>

    <?php if ($succes): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i><?= $succes ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i><?= $erreur ?></div><?php endif; ?>

    <!-- Stats rapides -->
    <?php
    $counts = ['total'=>0,'standard'=>0,'superieure'=>0,'suite'=>0,'villa'=>0];
    foreach ($chambres as $c) { $counts['total']++; $counts[$c['type']]++; }
    ?>
    <div class="stats-bar">
        <div class="stat-mini"><div class="stat-mini-val"><?= $counts['total'] ?></div><div class="stat-mini-label">Total</div></div>
        <div class="stat-mini"><div class="stat-mini-val"><?= $counts['standard'] ?></div><div class="stat-mini-label">Standard</div></div>
        <div class="stat-mini"><div class="stat-mini-val"><?= $counts['superieure'] ?></div><div class="stat-mini-label">Supérieure</div></div>
        <div class="stat-mini"><div class="stat-mini-val"><?= $counts['suite'] ?></div><div class="stat-mini-label">Suite</div></div>
        <div class="stat-mini"><div class="stat-mini-val"><?= $counts['villa'] ?></div><div class="stat-mini-label">Villa</div></div>
    </div>

    <!-- Grille chambres -->
    <div class="chambres-grid">
    <?php foreach ($chambres as $c):
        $amenities = json_decode($c['amenities'] ?? '[]', true) ?: [];
    ?>
    <div class="ch-card">
        <div class="ch-img">
            <?php if ($c['image_principale']): ?>
            <img src="<?= htmlspecialchars($c['image_principale']) ?>" alt="<?= htmlspecialchars($c['nom']) ?>">
            <?php else: ?>
            <div class="ch-img-placeholder"><i class="fas fa-bed"></i></div>
            <?php endif; ?>
            <span class="ch-badge"><?= $TYPES[$c['type']] ?? $c['type'] ?></span>
            <span class="ch-dispo <?= $c['disponible']?'on':'off' ?>"
                  id="dispo-<?= $c['id'] ?>"
                  title="<?= $c['disponible']?'Disponible — cliquer pour désactiver':'Indisponible — cliquer pour activer' ?>"
                  onclick="toggleDispo('<?= $c['id'] ?>',<?= $c['disponible']?0:1 ?>)"></span>
        </div>
        <div class="ch-body">
            <div class="ch-num">N° <?= $c['numero'] ?> · Étage <?= $c['etage'] ?></div>
            <div class="ch-name"><?= htmlspecialchars($c['nom']) ?></div>
            <div class="ch-meta">
                <span><i class="fas fa-expand-arrows-alt"></i> <?= $c['superficie_m2'] ?> m²</span>
                <span><i class="fas fa-user-friends"></i> <?= $c['capacite_max'] ?> pers.</span>
                <?php if ($c['capacite_enfants']): ?>
                <span><i class="fas fa-child"></i> +<?= $c['capacite_enfants'] ?> enfant(s)</span>
                <?php endif; ?>
            </div>
            <?php if ($c['description']): ?>
            <div class="ch-desc"><?= htmlspecialchars($c['description']) ?></div>
            <?php endif; ?>
            <?php if ($amenities): ?>
            <div class="ch-amenities">
                <?php foreach (array_slice($amenities, 0, 5) as $a): ?>
                <span class="ch-amenity"><?= htmlspecialchars($a) ?></span>
                <?php endforeach; ?>
                <?php if (count($amenities) > 5): ?>
                <span class="ch-amenity">+<?= count($amenities)-5 ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="ch-footer">
                <div class="ch-price">
                    <?= number_format($c['prix_nuit'],0,',',' ') ?> FCFA
                    <small>/ nuit</small>
                </div>
                <div class="ch-actions">
                    <button class="btn btn-primary btn-sm" title="Modifier"
                        onclick='ouvrirModif(<?= json_encode($c, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" title="Supprimer"
                        onclick="confirmerSuppression('<?= $c['id'] ?>','<?= htmlspecialchars($c['nom'],ENT_QUOTES) ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php if (empty($chambres)): ?>
    <div style="text-align:center;padding:80px;color:#aaa;">
        <i class="fas fa-bed" style="font-size:3rem;margin-bottom:16px;display:block;"></i>
        Aucune chambre. Cliquez sur "Ajouter une chambre" pour commencer.
    </div>
    <?php endif; ?>

</main>

<!-- ══ MODAL AJOUTER / MODIFIER ════════════════════════════ -->
<div id="modalChambre" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-header">
        <div class="modal-title" id="modalTitre">Ajouter une chambre</div>
        <button class="modal-close" onclick="fermerModal()">✕</button>
    </div>
    <div class="modal-body">
      <form id="formChambre" method="POST" class="form-grid">
        <input type="hidden" name="action" id="fAction" value="ajouter">
        <input type="hidden" name="id"     id="fId">

        <div class="form-section">Informations générales</div>
        <div class="form-row">
            <div class="form-group">
                <label>Nom de la chambre *</label>
                <input type="text" name="nom" id="fNom" placeholder="Ex : Suite Royale Panoramique" required>
            </div>
            <div class="form-group">
                <label>Type *</label>
                <select name="type" id="fType" required>
                    <option value="">— Choisir —</option>
                    <option value="standard">Standard</option>
                    <option value="superieure">Supérieure</option>
                    <option value="suite">Suite</option>
                    <option value="villa">Villa</option>
                </select>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Prix / nuit (FCFA) *</label>
                <input type="number" name="prix_nuit" id="fPrix" min="0" step="1000" placeholder="55000" required>
            </div>
            <div class="form-group">
                <label>Superficie (m²)</label>
                <input type="number" name="superficie_m2" id="fSuperficie" min="0" placeholder="28">
            </div>
            <div class="form-group">
                <label>N° de chambre</label>
                <input type="number" name="numero" id="fNumero" min="0" placeholder="101">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Capacité adultes</label>
                <input type="number" name="capacite_max" id="fCapMax" min="1" max="10" value="2">
            </div>
            <div class="form-group">
                <label>Capacité enfants</label>
                <input type="number" name="capacite_enfants" id="fCapEnf" min="0" max="10" value="0">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Étage</label>
                <input type="number" name="etage" id="fEtage" min="0" max="20" value="1">
            </div>
            <div class="form-group">
                <label>Disponible</label>
                <div class="toggle-dispo">
                    <label class="toggle-switch">
                        <input type="checkbox" name="disponible" id="fDispo" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span id="fDispoLabel" style="font-size:.85rem;color:#666;">Disponible</span>
                </div>
            </div>
        </div>

        <div class="form-section">Description</div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="fDesc" placeholder="Décrivez la chambre en quelques phrases…"></textarea>
        </div>

        <div class="form-section">Image</div>
        <div class="form-group">
            <label>URL de l'image principale</label>
            <input type="url" name="image_principale" id="fImage"
                   placeholder="https://images.unsplash.com/..."
                   oninput="previewImage(this.value)">
            <img id="imgPreview" class="img-preview" alt="Aperçu">
        </div>

        <div class="form-section">Équipements</div>
        <div class="amenities-grid" id="amenitiesGrid">
            <?php foreach ($AMENITIES_STANDARDS as $a): ?>
            <label class="amenity-check">
                <input type="checkbox" name="amenities_sel[]" value="<?= htmlspecialchars($a) ?>">
                <?= htmlspecialchars($a) ?>
            </label>
            <?php endforeach; ?>
        </div>
        <div class="form-group" style="margin-top:10px;">
            <label>Équipements supplémentaires <small style="color:#aaa;">(séparés par des virgules)</small></label>
            <input type="text" name="amenities_libre" id="fAmenLibre"
                   placeholder="Ex : Hammam privé, Piscine à débordement">
        </div>

        <div class="form-actions">
            <button type="button" class="btn" style="border:1px solid #ddd;" onclick="fermerModal()">Annuler</button>
            <button type="submit" class="btn btn-primary" id="fSubmitBtn">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Formulaire de suppression -->
<form id="formSuppr" method="POST" style="display:none;">
    <input type="hidden" name="action" value="supprimer">
    <input type="hidden" name="id" id="supprId">
</form>

<script>
var AMENITIES = <?= json_encode($AMENITIES_STANDARDS) ?>;

function fermerModal() {
    document.getElementById('modalChambre').classList.remove('open');
}
document.getElementById('modalChambre').addEventListener('click', function(e){
    if(e.target===this) fermerModal();
});

function ouvrirAjout() {
    document.getElementById('modalTitre').textContent = 'Ajouter une chambre';
    document.getElementById('fAction').value = 'ajouter';
    document.getElementById('fId').value = '';
    document.getElementById('formChambre').reset();
    document.getElementById('imgPreview').style.display = 'none';
    document.getElementById('fDispoLabel').textContent = 'Disponible';
    document.getElementById('fSubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Ajouter';
    // Décocher toutes les amenities
    document.querySelectorAll('#amenitiesGrid input').forEach(cb => cb.checked = false);
    document.getElementById('modalChambre').classList.add('open');
}

function ouvrirModif(c) {
    document.getElementById('modalTitre').textContent = 'Modifier — ' + c.nom;
    document.getElementById('fAction').value = 'modifier';
    document.getElementById('fId').value        = c.id;
    document.getElementById('fNom').value       = c.nom || '';
    document.getElementById('fType').value      = c.type || '';
    document.getElementById('fPrix').value      = c.prix_nuit || '';
    document.getElementById('fSuperficie').value= c.superficie_m2 || '';
    document.getElementById('fNumero').value    = c.numero || '';
    document.getElementById('fCapMax').value    = c.capacite_max || 2;
    document.getElementById('fCapEnf').value    = c.capacite_enfants || 0;
    document.getElementById('fEtage').value     = c.etage || 0;
    document.getElementById('fDesc').value      = c.description || '';
    document.getElementById('fImage').value     = c.image_principale || '';
    document.getElementById('fAmenLibre').value = '';
    document.getElementById('fDispo').checked   = c.disponible == 1;
    document.getElementById('fDispoLabel').textContent = c.disponible == 1 ? 'Disponible' : 'Indisponible';
    document.getElementById('fSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Enregistrer';

    // Aperçu image
    previewImage(c.image_principale || '');

    // Amenities
    var amenities = [];
    try { amenities = JSON.parse(c.amenities || '[]'); } catch(e){}
    document.querySelectorAll('#amenitiesGrid input').forEach(function(cb) {
        cb.checked = amenities.indexOf(cb.value) !== -1;
    });
    // Les amenities non standard → champ libre
    var libres = amenities.filter(function(a){ return AMENITIES.indexOf(a) === -1; });
    document.getElementById('fAmenLibre').value = libres.join(', ');

    document.getElementById('modalChambre').classList.add('open');
}

function previewImage(url) {
    var img = document.getElementById('imgPreview');
    if (url && url.startsWith('http')) {
        img.src = url;
        img.style.display = 'block';
        img.onerror = function(){ img.style.display='none'; };
    } else {
        img.style.display = 'none';
    }
}

document.getElementById('fDispo').addEventListener('change', function(){
    document.getElementById('fDispoLabel').textContent = this.checked ? 'Disponible' : 'Indisponible';
});

function confirmerSuppression(id, nom) {
    if (!confirm('Supprimer la chambre "' + nom + '" ?\nCette action est irréversible.')) return;
    document.getElementById('supprId').value = id;
    document.getElementById('formSuppr').submit();
}

function toggleDispo(id, newVal) {
    var data = new FormData();
    data.append('action', 'toggle_dispo');
    data.append('id', id);
    data.append('disponible', newVal);

    fetch(window.location.pathname, {method:'POST', body:data})
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.success) {
                var el = document.getElementById('dispo-' + id);
                el.classList.toggle('on', newVal == 1);
                el.classList.toggle('off', newVal == 0);
                el.title = newVal == 1 ? 'Disponible — cliquer pour désactiver' : 'Indisponible — cliquer pour activer';
                el.setAttribute('onclick', "toggleDispo('" + id + "'," + (newVal==1?0:1) + ")");
            }
        });
}
</script>
</body>
</html>