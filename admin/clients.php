<?php
/**
 * ADMIN CLIENTS — Hôtel SEGURO
 */

session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    header('Location: ../pages/connexion-client.php');
    exit;
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// ── Traitement AJAX modification client ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if ($_POST['action'] === 'modifier') {
        $id        = trim($_POST['id']        ?? '');
        $nom       = trim($_POST['nom']       ?? '');
        $prenom    = trim($_POST['prenom']    ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $pays      = trim($_POST['pays']      ?? '');
        $role      = trim($_POST['role']      ?? 'client');
        if (!$id || !$nom || !$email) {
            echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']);
            exit;
        }
        try {
            $stmt = $db->prepare("UPDATE users SET nom=?, prenom=?, email=?, telephone=?, pays=?, role=? WHERE id=?");
            $stmt->execute([$nom, $prenom, $email, $telephone, $pays, $role, $id]);
            echo json_encode(['success' => true, 'message' => 'Client mis à jour avec succès.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        }
    }
    exit;
}

$search = $_GET['search'] ?? '';
$clients = $search ? $user->searchClients($search) : $user->getAllClients();

$page      = max(1, intval($_GET['page'] ?? 1));
$per_page  = 20;
$total_clients = count($clients);
$total_pages   = ceil($total_clients / $per_page);
$clients       = array_slice($clients, ($page - 1) * $per_page, $per_page);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients — Hôtel Seguro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --vert:#1a3a2a; --vert-clair:#2d5c40; --or:#c9a84c;
            --blanc:#faf8f3; --gris:#f5f5f5; --gris-fonce:#666;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Jost',sans-serif;background:var(--gris);color:var(--vert);display:flex;min-height:100vh;}
        /* ── Sidebar ── */
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
        /* ── Main ── */
        .main-content{flex:1;margin-left:260px;padding:30px;}
        .top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e0e0e0;}
        .page-title h1{font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:400;}
        .page-title p{font-size:.85rem;color:var(--gris-fonce);margin-top:4px;}
        .search-input{padding:10px 16px;border:1px solid #e0e0e0;border-radius:4px;font-size:.9rem;width:300px;}
        .card{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;}
        .card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;}
        .card-title{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:600;color:var(--vert);}
        table{width:100%;border-collapse:collapse;}
        th{text-align:left;padding:16px 24px;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--gris-fonce);font-weight:500;background:#fafafa;border-bottom:1px solid #f0f0f0;}
        td{padding:16px 24px;font-size:.9rem;border-bottom:1px solid #f8f8f8;}
        tr:hover td{background:#fafafa;}
        .badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:500;}
        .badge-admin{background:rgba(201,168,76,.15);color:#856404;}
        .badge-client{background:rgba(26,58,42,.15);color:var(--vert);}
        .badge-super{background:rgba(220,53,69,.12);color:#721c24;}
        .actions{display:flex;gap:8px;}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:4px;font-size:.85rem;transition:all .3s;cursor:pointer;border:none;font-family:'Jost',sans-serif;}
        .btn-primary{background:var(--or);color:var(--vert);}
        .btn-primary:hover{background:#b8962a;}
        .btn-sm{padding:6px 12px;font-size:.75rem;}
        .pagination{display:flex;justify-content:center;gap:8px;padding:20px 24px;}
        .pagination a{padding:8px 16px;border:1px solid #e0e0e0;border-radius:4px;text-decoration:none;color:var(--vert);transition:all .3s;}
        .pagination a:hover,.pagination a.active{background:var(--or);color:var(--vert);border-color:var(--or);}
        /* ── Modals ── */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal-box{background:#fff;border-radius:16px;padding:36px;width:500px;max-width:95vw;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:fadeIn .2s ease;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .modal-close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:#999;line-height:1;}
        .modal-close:hover{color:#333;}
        .modal-title{font-family:'Cormorant Garamond',serif;color:var(--vert);margin-bottom:24px;font-size:1.6rem;font-weight:600;}
        .voir-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:.9rem;}
        .voir-label{color:#888;}
        .voir-val{font-weight:500;color:#222;}
        .form-grid{display:grid;gap:14px;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
        .form-group label{display:block;font-size:.8rem;color:#666;font-weight:500;margin-bottom:4px;}
        .form-group input,.form-group select{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-family:'Jost',sans-serif;font-size:.9rem;transition:border .2s;}
        .form-group input:focus,.form-group select:focus{outline:none;border-color:var(--or);}
        .form-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:8px;}
        .btn-cancel{padding:10px 20px;border:1px solid #ddd;background:#fff;border-radius:8px;cursor:pointer;font-family:'Jost',sans-serif;}
        .btn-save{padding:10px 24px;background:var(--vert);color:var(--or);border:none;border-radius:8px;cursor:pointer;font-family:'Jost',sans-serif;font-weight:500;}
        .btn-save:hover{background:var(--vert-clair);}
        .form-msg{display:none;padding:10px 14px;border-radius:6px;font-size:.85rem;margin-top:4px;}
        .form-msg.ok{background:#d4edda;color:#155724;}
        .form-msg.err{background:#f8d7da;color:#721c24;}
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-crown"></i>
            <span>Hôtel Seguro</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
        <a href="../pages/calendrier-disponibilites.php" class="nav-item"><i class="fas fa-calendar-alt"></i><span>Calendrier</span></a>
        <div class="nav-section" style="margin-top:20px;">Gestion</div>
        <a href="reservations.php" class="nav-item"><i class="fas fa-book"></i><span>Réservations</span></a>
        <a href="clients.php" class="nav-item active"><i class="fas fa-users"></i><span>Clients</span></a>
        <a href="../pages/chambres.php" class="nav-item"><i class="fas fa-bed"></i><span>Chambres</span></a>
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
        <div class="page-title">
            <h1>Clients</h1>
            <p>Gestion des clients de l'hôtel</p>
        </div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" class="search-input" placeholder="Rechercher un client..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users" style="color:var(--or);margin-right:8px;"></i>Liste des clients</h3>
            <span class="badge badge-client"><?php echo $total_clients; ?> clients</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th><th>Email</th><th>Code Client</th>
                        <th>Rôle</th><th>Inscription</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                        <td><code style="background:#f0f0f0;padding:2px 8px;border-radius:3px;"><?php echo htmlspecialchars($client['code_client']); ?></code></td>
                        <td>
                            <?php
                            $roleClass = match($client['role']) {
                                'admin'       => 'badge-admin',
                                'super_admin' => 'badge-super',
                                default       => 'badge-client',
                            };
                            ?>
                            <span class="badge <?php echo $roleClass; ?>"><?php echo ucfirst($client['role']); ?></span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($client['created_at'])); ?></td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-primary btn-sm"
                                    title="Voir le profil"
                                    onclick='voirClient(<?php echo json_encode($client, JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm"
                                    title="Modifier"
                                    onclick='modifierClient(<?php echo json_encode($client, JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'>
                                    <i class="fas fa-edit"></i>
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
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"
               class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- ══ MODAL VOIR ══════════════════════════════════════════ -->
<div id="modalVoir" class="modal-overlay">
    <div class="modal-box">
        <button class="modal-close" onclick="fermer('modalVoir')">✕</button>
        <div class="modal-title">Profil client</div>
        <div id="voirContenu"></div>
    </div>
</div>

<!-- ══ MODAL MODIFIER ══════════════════════════════════════ -->
<div id="modalModifier" class="modal-overlay">
    <div class="modal-box" style="width:540px;">
        <button class="modal-close" onclick="fermer('modalModifier')">✕</button>
        <div class="modal-title">Modifier le client</div>
        <form id="formModifier" class="form-grid">
            <input type="hidden" id="mod_id">
            <div class="form-row">
                <div class="form-group">
                    <label>Nom *</label>
                    <input id="mod_nom" type="text" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input id="mod_prenom" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input id="mod_email" type="email" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input id="mod_telephone" type="text">
                </div>
                <div class="form-group">
                    <label>Pays</label>
                    <input id="mod_pays" type="text">
                </div>
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select id="mod_role">
                    <option value="client">Client</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div id="formMsg" class="form-msg"></div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="fermer('modalModifier')">Annuler</button>
                <button type="submit" class="btn-save">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrir(id) { document.getElementById(id).classList.add('open'); }
function fermer(id) { document.getElementById(id).classList.remove('open'); }

// Fermer en cliquant sur le fond
document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === m) m.classList.remove('open');
    });
});

function voirClient(c) {
    var champs = [
        ['Nom complet',        (c.prenom||'') + ' ' + (c.nom||'')],
        ['Email',              c.email||'—'],
        ['Code client',        c.code_client||'—'],
        ['Téléphone',          c.telephone||'—'],
        ['Pays',               c.pays||'—'],
        ['Rôle',               c.role||'—'],
        ['Inscrit le',         c.created_at  ? c.created_at.substring(0,10)  : '—'],
        ['Dernière connexion',  c.last_login  ? c.last_login.substring(0,10)  : 'Jamais'],
    ];
    var html = '';
    champs.forEach(function(r) {
        html += '<div class="voir-row"><span class="voir-label">'+r[0]+'</span><span class="voir-val">'+r[1]+'</span></div>';
    });
    document.getElementById('voirContenu').innerHTML = html;
    ouvrir('modalVoir');
}

function modifierClient(c) {
    document.getElementById('mod_id').value        = c.id        || '';
    document.getElementById('mod_nom').value       = c.nom       || '';
    document.getElementById('mod_prenom').value    = c.prenom    || '';
    document.getElementById('mod_email').value     = c.email     || '';
    document.getElementById('mod_telephone').value = c.telephone || '';
    document.getElementById('mod_pays').value      = c.pays      || '';
    document.getElementById('mod_role').value      = c.role      || 'client';
    var msg = document.getElementById('formMsg');
    msg.style.display = 'none';
    msg.className = 'form-msg';
    ouvrir('modalModifier');
}

document.getElementById('formModifier').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('.btn-save');
    btn.textContent = 'Enregistrement…';
    btn.disabled = true;

    var data = new FormData();
    data.append('action',    'modifier');
    data.append('id',        document.getElementById('mod_id').value);
    data.append('nom',       document.getElementById('mod_nom').value);
    data.append('prenom',    document.getElementById('mod_prenom').value);
    data.append('email',     document.getElementById('mod_email').value);
    data.append('telephone', document.getElementById('mod_telephone').value);
    data.append('pays',      document.getElementById('mod_pays').value);
    data.append('role',      document.getElementById('mod_role').value);

    fetch(window.location.pathname, {method:'POST', body:data})
        .then(function(r){ return r.json(); })
        .then(function(res) {
            var msg = document.getElementById('formMsg');
            msg.style.display = 'block';
            if (res.success) {
                msg.className = 'form-msg ok';
                msg.textContent = res.message;
                setTimeout(function(){ location.reload(); }, 1200);
            } else {
                msg.className = 'form-msg err';
                msg.textContent = res.message;
                btn.textContent = 'Enregistrer';
                btn.disabled = false;
            }
        })
        .catch(function() {
            btn.textContent = 'Enregistrer';
            btn.disabled = false;
        });
});
</script>

</body>
</html>