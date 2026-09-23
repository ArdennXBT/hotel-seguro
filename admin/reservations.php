<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    header('Location: ../pages/connexion-client.php');
    exit;
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/Reservation.php';
require_once __DIR__ . '/../includes/User.php';

$database = new Database();
$db       = $database->getConnection();
$reservation = new Reservation($db);

$erreur = '';
$succes = '';

// ════════════════════════════════════════════════════════
// TRAITEMENT POST
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AJAX : note admin
    if (($_POST['action'] ?? '') === 'note') {
        header('Content-Type: application/json');
        $id   = trim($_POST['id']   ?? '');
        $note = trim($_POST['note'] ?? '');
        try {
            $db->prepare("UPDATE reservations SET note_admin=? WHERE id=?")->execute([$note, $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // AJAX : changer statut depuis le modal
    if (($_POST['action'] ?? '') === 'changer_statut') {
        header('Content-Type: application/json');
        $id     = trim($_POST['id']     ?? '');
        $statut = trim($_POST['statut'] ?? '');
        $allowed = ['validee', 'annulee', 'en_cours', 'terminee'];
        if (!$id || !in_array($statut, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Paramètres invalides.']);
            exit;
        }
        try {
            $extra = $statut === 'validee'
                ? ", valide_par='" . ($_SESSION['user_id'] ?? '') . "', valide_at=NOW()"
                : '';
            $db->prepare("UPDATE reservations SET statut=? {$extra}, updated_at=NOW() WHERE id=?")
               ->execute([$statut, $id]);
            echo json_encode(['success' => true, 'statut' => $statut]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Formulaire classique (tableau)
    $rid = trim($_POST['reservation_id'] ?? '');
    if ($rid) {
        if (isset($_POST['action_valider'])) {
            try {
                $s = $db->prepare("UPDATE reservations SET statut='validee', valide_par=?, valide_at=NOW(), updated_at=NOW() WHERE id=? AND statut IN ('en_cours','modifiee')");
                $s->execute([$_SESSION['user_id'] ?? null, $rid]);
                $succes = $s->rowCount() > 0 ? 'Réservation validée.' : 'Déjà traitée ou introuvable.';
            } catch (PDOException $e) { $erreur = $e->getMessage(); }
        }
        if (isset($_POST['action_annuler'])) {
            try {
                $s = $db->prepare("UPDATE reservations SET statut='annulee', updated_at=NOW() WHERE id=? AND statut NOT IN ('terminee','annulee')");
                $s->execute([$rid]);
                $succes = $s->rowCount() > 0 ? 'Réservation annulée.' : 'Déjà annulée ou introuvable.';
            } catch (PDOException $e) { $erreur = $e->getMessage(); }
        }
    }
}

// ════════════════════════════════════════════════════════
// DONNÉES
// ════════════════════════════════════════════════════════
$statut_filter      = $_GET['statut'] ?? '';
$reservations       = $reservation->getAll($statut_filter ?: null, 500);
$page               = max(1, intval($_GET['page'] ?? 1));
$per_page           = 20;
$total_reservations = count($reservations);
$total_pages        = ceil($total_reservations / $per_page);
$reservations       = array_slice($reservations, ($page - 1) * $per_page, $per_page);

$stats = [
    'total'    => $reservation->count(),
    'en_cours' => $reservation->countByStatut('en_cours'),
    'validee'  => $reservation->countByStatut('validee'),
    'annulee'  => $reservation->countByStatut('annulee'),
];

$statuts_labels = [
    'en_cours' => 'En cours', 'validee'  => 'Validée',
    'modifiee' => 'Modifiée', 'annulee'  => 'Annulée', 'terminee' => 'Terminée',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations — Hôtel Seguro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--vert:#1a3a2a;--vert-clair:#2d5c40;--or:#c9a84c;--blanc:#faf8f3;--gris:#f5f5f5;--gris-fonce:#666;--danger:#dc3545;--success:#28a745;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Jost',sans-serif;background:var(--gris);color:var(--vert);display:flex;min-height:100vh;}
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
        .main-content{flex:1;margin-left:260px;padding:30px;}
        .top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e0e0e0;}
        .page-title h1{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:400;}
        .page-title p{font-size:.85rem;color:var(--gris-fonce);margin-top:4px;}
        .filter-select{padding:10px 16px;border:1px solid #e0e0e0;border-radius:4px;font-size:.9rem;background:#fff;cursor:pointer;}
        .alert{padding:14px 20px;border-radius:6px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:.9rem;}
        .alert-success{background:rgba(40,167,69,.1);color:var(--success);border:1px solid rgba(40,167,69,.2);}
        .alert-danger{background:rgba(220,53,69,.1);color:var(--danger);border:1px solid rgba(220,53,69,.2);}
        .stats-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
        .stat-filter{background:#fff;padding:16px 20px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.06);text-align:center;cursor:pointer;transition:all .3s;border:2px solid transparent;}
        .stat-filter:hover,.stat-filter.active{border-color:var(--or);transform:translateY(-2px);}
        .stat-filter-value{font-size:1.5rem;font-weight:600;color:var(--vert);}
        .stat-filter-label{font-size:.8rem;color:var(--gris-fonce);margin-top:4px;}
        .card{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;}
        .card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;}
        .card-title{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:600;color:var(--vert);}
        table{width:100%;border-collapse:collapse;}
        th{text-align:left;padding:14px 20px;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--gris-fonce);font-weight:500;background:#fafafa;border-bottom:1px solid #f0f0f0;}
        td{padding:14px 20px;font-size:.88rem;border-bottom:1px solid #f8f8f8;vertical-align:middle;}
        tr:hover td{background:#fafafa;}
        .badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:500;}
        .badge-warning{background:rgba(255,193,7,.15);color:#856404;}
        .badge-success{background:rgba(40,167,69,.15);color:#155724;}
        .badge-danger{background:rgba(220,53,69,.15);color:#721c24;}
        .badge-info{background:rgba(23,162,184,.15);color:#0c5460;}
        .badge-modif{background:rgba(102,16,242,.12);color:#4a0080;}
        .actions{display:flex;gap:6px;flex-wrap:wrap;}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:4px;font-size:.82rem;transition:all .25s;cursor:pointer;border:none;font-family:'Jost',sans-serif;}
        .btn-primary{background:var(--or);color:var(--vert);}
        .btn-primary:hover{background:#b8962a;}
        .btn-success{background:var(--success);color:#fff;}
        .btn-success:hover{background:#218838;}
        .btn-danger{background:var(--danger);color:#fff;}
        .btn-danger:hover{background:#c82333;}
        .btn-sm{padding:5px 10px;font-size:.75rem;}
        .pagination{display:flex;justify-content:center;gap:8px;padding:20px;}
        .pagination a{padding:8px 16px;border:1px solid #e0e0e0;border-radius:4px;text-decoration:none;color:var(--vert);transition:all .3s;}
        .pagination a:hover,.pagination a.active{background:var(--or);color:var(--vert);border-color:var(--or);}
        /* Modal */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal-box{background:#fff;border-radius:16px;padding:36px;width:600px;max-width:95vw;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.25);max-height:90vh;overflow-y:auto;}
        .modal-close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#aaa;}
        .modal-close:hover{color:#333;}
        .modal-title{font-family:'Cormorant Garamond',serif;color:var(--vert);font-size:1.6rem;font-weight:600;margin-bottom:4px;}
        .modal-ref{font-size:.85rem;color:var(--or);font-weight:600;margin-bottom:6px;letter-spacing:.05em;}
        .modal-statut-badge{margin-bottom:24px;}
        .section-title{font-size:.72rem;text-transform:uppercase;letter-spacing:.12em;color:#bbb;font-weight:600;margin:20px 0 10px;}
        .detail-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f4f4f4;font-size:.9rem;}
        .detail-row:last-child{border-bottom:none;}
        .detail-label{color:#999;}
        .detail-val{font-weight:500;color:#222;}
        .note-area{width:100%;min-height:72px;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font-family:'Jost',sans-serif;font-size:.88rem;resize:vertical;margin-top:8px;}
        .note-area:focus{outline:none;border-color:var(--or);}
        .modal-footer{display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0;}
        .modal-footer-left{display:flex;gap:8px;}
        .note-ok{font-size:.8rem;color:var(--success);display:none;}
        @media(max-width:768px){.sidebar{width:70px;}.sidebar-logo span,.nav-item span,.nav-section,.admin-details{display:none;}.main-content{margin-left:70px;}.stats-bar{grid-template-columns:repeat(2,1fr);}}
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
        <a href="reservations.php" class="nav-item active"><i class="fas fa-book"></i><span>Réservations</span></a>
        <a href="clients.php" class="nav-item"><i class="fas fa-users"></i><span>Clients</span></a>
        <a href="chambres.php" class="nav-item"><i class="fas fa-bed"></i><span>Chambres</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'A', 0, 1)); ?></div>
            <div class="admin-details">
                <h4><?php echo htmlspecialchars(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')); ?></h4>
                <p>Administrateur</p>
            </div>
        </div>
        <a href="../pages/deconnexion.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="page-title"><h1>Réservations</h1><p>Gestion de toutes les réservations</p></div>
        <form method="GET">
            <select name="statut" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="en_cours" <?= $statut_filter==='en_cours'?'selected':'' ?>>En cours</option>
                <option value="validee"  <?= $statut_filter==='validee' ?'selected':'' ?>>Validées</option>
                <option value="annulee"  <?= $statut_filter==='annulee' ?'selected':'' ?>>Annulées</option>
            </select>
        </form>
    </div>

    <?php if ($succes): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i><?= $succes ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i><?= $erreur ?></div><?php endif; ?>

    <div class="stats-bar">
        <?php foreach ([
            [''         , $stats['total'],    'Total'],
            ['en_cours' , $stats['en_cours'], 'En cours'],
            ['validee'  , $stats['validee'],  'Validées'],
            ['annulee'  , $stats['annulee'],  'Annulées'],
        ] as [$val, $nb, $lbl]): ?>
        <div class="stat-filter <?= $statut_filter===$val?'active':'' ?>"
             onclick="location.href='reservations.php<?= $val ? '?statut='.$val : '' ?>'">
            <div class="stat-filter-value"><?= $nb ?></div>
            <div class="stat-filter-label"><?= $lbl ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-book" style="color:var(--or);margin-right:8px;"></i>Liste des réservations</h3>
            <span class="badge badge-info"><?= $total_reservations ?> réservations</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead><tr>
                    <th>Référence</th><th>Client</th><th>Chambre</th>
                    <th>Dates</th><th>Montant</th><th>Statut</th><th>Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($reservations as $res):
                    $bc = match($res['statut']) {
                        'en_cours'=>'badge-warning','validee'=>'badge-success',
                        'annulee'=>'badge-danger','modifiee'=>'badge-modif',default=>'badge-info'
                    };
                ?>
                <tr id="row-<?= $res['id'] ?>">
                    <td><strong><?= htmlspecialchars($res['reference']) ?></strong></td>
                    <td><?= htmlspecialchars(($res['user_nom']??'').' '.($res['user_prenom']??'')) ?></td>
                    <td><?= htmlspecialchars($res['chambre_nom']??'—') ?></td>
                    <td><?= date('d/m/Y',strtotime($res['date_arrivee'])).' → '.date('d/m/Y',strtotime($res['date_depart'])) ?></td>
                    <td style="color:var(--or);font-weight:500;"><?= number_format($res['prix_total'],0,',',' ') ?> FCFA</td>
                    <td><span class="badge <?= $bc ?>" id="badge-<?= $res['id'] ?>"><?= $statuts_labels[$res['statut']]??$res['statut'] ?></span></td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn btn-primary btn-sm" title="Voir / Agir"
                                onclick='ouvrirModal(<?= json_encode($res, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>
                                <i class="fas fa-eye"></i> Voir
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i=1;$i<=$total_pages;$i++): ?>
            <a href="?page=<?= $i ?><?= $statut_filter?'&statut='.urlencode($statut_filter):'' ?>"
               class="<?= $i==$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- ══ MODAL ══════════════════════════════════════════════ -->
<div id="modalVoir" class="modal-overlay">
  <div class="modal-box">
    <button class="modal-close" onclick="fermerModal()">✕</button>

    <div class="modal-title" id="mRef"></div>
    <div class="modal-ref"   id="mRefCode"></div>
    <div class="modal-statut-badge"><span class="badge" id="mBadge"></span></div>

    <div class="section-title">🏨 Séjour</div>
    <div class="detail-row"><span class="detail-label">Chambre</span><span class="detail-val" id="mChambre"></span></div>
    <div class="detail-row"><span class="detail-label">Arrivée</span><span class="detail-val" id="mArrivee"></span></div>
    <div class="detail-row"><span class="detail-label">Départ</span><span class="detail-val" id="mDepart"></span></div>
    <div class="detail-row"><span class="detail-label">Adultes / Enfants</span><span class="detail-val" id="mPers"></span></div>

    <div class="section-title">💰 Tarifs</div>
    <div class="detail-row"><span class="detail-label">Prix / nuit</span><span class="detail-val" id="mNuit"></span></div>
    <div class="detail-row"><span class="detail-label">Options</span><span class="detail-val" id="mOpts"></span></div>
    <div class="detail-row"><span class="detail-label">Total</span><span class="detail-val" id="mTotal" style="color:var(--or);font-weight:600;font-size:1rem;"></span></div>

    <div class="section-title">👤 Client</div>
    <div class="detail-row"><span class="detail-label">Nom</span><span class="detail-val" id="mClient"></span></div>
    <div class="detail-row"><span class="detail-label">Email</span><span class="detail-val" id="mEmail"></span></div>

    <div class="section-title">📝 Demandes spéciales</div>
    <div id="mDemandes" style="font-size:.88rem;color:#666;padding:4px 0 8px;"></div>

    <div class="section-title">🔒 Note interne admin</div>
    <textarea id="mNote" class="note-area" placeholder="Ajouter une note…"></textarea>
    <div style="display:flex;justify-content:flex-end;margin-top:6px;gap:10px;align-items:center;">
        <span id="noteOk" class="note-ok">✓ Enregistré</span>
        <button class="btn btn-primary btn-sm" onclick="sauvegarderNote()">Enregistrer la note</button>
    </div>

    <!-- ACTIONS — toujours visibles, adaptées au statut -->
    <div class="modal-footer">
        <div class="modal-footer-left" id="mBtns"></div>
        <button class="btn" style="border:1px solid #ddd;" onclick="fermerModal()">Fermer</button>
    </div>
  </div>
</div>

<script>
var RES = null;
var STATUTS = {en_cours:'En cours',validee:'Validée',modifiee:'Modifiée',annulee:'Annulée',terminee:'Terminée'};
var BADGE_CSS = {en_cours:'badge-warning',validee:'badge-success',annulee:'badge-danger',modifiee:'badge-modif',terminee:'badge-info'};

function fmt(n){ return parseFloat(n||0).toLocaleString('fr-FR')+' FCFA'; }
function fdate(s){ if(!s)return'—'; var p=s.substring(0,10).split('-'); return p[2]+'/'+p[1]+'/'+p[0]; }

function ouvrirModal(r) {
    RES = r;
    document.getElementById('mRef').textContent     = 'Réservation';
    document.getElementById('mRefCode').textContent = r.reference || '';
    var badge = document.getElementById('mBadge');
    badge.textContent = STATUTS[r.statut] || r.statut;
    badge.className   = 'badge ' + (BADGE_CSS[r.statut]||'badge-info');

    document.getElementById('mChambre').textContent = r.chambre_nom || '—';
    document.getElementById('mArrivee').textContent = fdate(r.date_arrivee);
    document.getElementById('mDepart').textContent  = fdate(r.date_depart);
    document.getElementById('mPers').textContent    = (r.nb_adultes||0)+' adulte(s) / '+(r.nb_enfants||0)+' enfant(s)';
    document.getElementById('mNuit').textContent    = fmt(r.prix_nuit);
    document.getElementById('mOpts').textContent    = fmt(r.prix_options);
    document.getElementById('mTotal').textContent   = fmt(r.prix_total);
    document.getElementById('mClient').textContent  = (r.user_nom||'') + ' ' + (r.user_prenom||'');
    document.getElementById('mEmail').textContent   = r.user_email || '—';
    document.getElementById('mDemandes').textContent = r.demandes_speciales || 'Aucune';
    document.getElementById('mNote').value          = r.note_admin || '';
    document.getElementById('noteOk').style.display = 'none';

    // ── Boutons d'action selon statut ──────────────────
    var btns = document.getElementById('mBtns');
    btns.innerHTML = '';

    if (r.statut === 'en_cours' || r.statut === 'modifiee') {
        btns.innerHTML +=
            '<button class="btn btn-success" onclick="changerStatut(\'validee\')">'
          + '<i class="fas fa-check"></i> Valider</button>'
          + '<button class="btn btn-danger" onclick="changerStatut(\'annulee\')">'
          + '<i class="fas fa-times"></i> Annuler</button>';
    }
    if (r.statut === 'validee') {
        btns.innerHTML +=
            '<button class="btn btn-danger" onclick="changerStatut(\'annulee\')">'
          + '<i class="fas fa-times"></i> Annuler cette réservation</button>';
    }
    if (r.statut === 'annulee') {
        btns.innerHTML +=
            '<button class="btn btn-success" onclick="changerStatut(\'validee\')">'
          + '<i class="fas fa-undo"></i> Remettre en valide</button>';
    }

    document.getElementById('modalVoir').classList.add('open');
}

function fermerModal() {
    document.getElementById('modalVoir').classList.remove('open');
    RES = null;
}

document.getElementById('modalVoir').addEventListener('click', function(e){
    if(e.target===this) fermerModal();
});

function changerStatut(nouveauStatut) {
    if (!RES) return;
    var msg = nouveauStatut === 'validee' ? 'Valider' : (nouveauStatut === 'annulee' ? 'Annuler' : 'Modifier');
    if (!confirm(msg + ' cette réservation ?')) return;

    var data = new FormData();
    data.append('action', 'changer_statut');
    data.append('id',     RES.id);
    data.append('statut', nouveauStatut);

    fetch(window.location.pathname, {method:'POST', body:data})
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.success) {
                // Mettre à jour le badge dans le tableau
                var b = document.getElementById('badge-' + RES.id);
                if (b) {
                    b.textContent = {en_cours:'En cours',validee:'Validée',annulee:'Annulée'}[nouveauStatut] || nouveauStatut;
                    b.className = 'badge ' + ({en_cours:'badge-warning',validee:'badge-success',annulee:'badge-danger'}[nouveauStatut]||'badge-info');
                }
                // Mettre à jour le modal
                RES.statut = nouveauStatut;
                ouvrirModal(RES);
            } else {
                alert('Erreur : ' + (res.message || 'Inconnue'));
            }
        })
        .catch(function(){ alert('Erreur réseau.'); });
}

function sauvegarderNote() {
    if (!RES) return;
    var data = new FormData();
    data.append('action', 'note');
    data.append('id',     RES.id);
    data.append('note',   document.getElementById('mNote').value);

    fetch(window.location.pathname, {method:'POST', body:data})
        .then(function(r){ return r.json(); })
        .then(function(res){
            if (res.success) {
                var ok = document.getElementById('noteOk');
                ok.style.display = 'inline';
                setTimeout(function(){ ok.style.display='none'; }, 2500);
            }
        });
}
</script>

</body>
</html>