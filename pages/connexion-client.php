<?php
/**
 * ════════════════════════════════════════════════════════
 * CONNEXION CLIENT — Espace personnel Hôtel SEGURO
 * ════════════════════════════════════════════════════════
 */

session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Reservation.php';

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: mon-compte.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$reservation = new Reservation($db);

$erreur = '';
$succes = '';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code_client = trim($_POST['code_client'] ?? '');
    
    if (empty($email) || empty($code_client)) {
        $erreur = 'Veuillez remplir tous les champs';
    } else {
        $client = $user->login($email, $code_client);
        
        if ($client) {
            $_SESSION['user_id'] = $client->id;
            $_SESSION['user_email'] = $client->email;
            $_SESSION['user_nom'] = $client->nom;
            $_SESSION['user_prenom'] = $client->prenom;
            $_SESSION['user_role'] = $client->role;
            
            // Redirection selon le rôle
            if ($client->isAdmin()) {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: mon-compte.php');
            }
            exit;
        } else {
            $erreur = 'Email ou code client incorrect';
        }
    }
}

include(__DIR__ . '/../layouts/header.php');
?>

<style>
/* ════════════════════════════════════════════════════════
   PAGE CONNEXION — Styles
═══════════════════════════════════════════════════════ */
.connexion-container {
    max-width: 500px;
    margin: 80px auto;
    padding: 0 20px;
}

.connexion-box {
    background: white;
    border-radius: 16px;
    padding: 48px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.connexion-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(to right, var(--vert), var(--or));
}

.connexion-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.5rem;
    color: var(--vert);
    text-align: center;
    margin-bottom: 16px;
}

.connexion-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 40px;
    font-family: 'Jost', sans-serif;
    font-size: 0.95rem;
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
    padding: 14px 16px;
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

.btn-connexion {
    width: 100%;
    background: var(--vert);
    color: white;
    border: none;
    padding: 16px;
    border-radius: 8px;
    font-family: 'Jost', sans-serif;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 32px;
}

.btn-connexion:hover {
    background: var(--vert-clair);
    transform: translateY(-2px);
}



.help-section {
    background: rgba(201,168,76,0.05);
    border-radius: 12px;
    padding: 24px;
    margin-top: 32px;
}

.help-title {
    font-family: 'Jost', sans-serif;
    font-weight: 500;
    color: var(--vert);
    margin-bottom: 12px;
    font-size: 1rem;
}

.help-text {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 8px;
}

.code-format {
    background: white;
    border: 1px solid rgba(201,168,76,0.2);
    border-radius: 6px;
    padding: 8px 12px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: var(--or);
    text-align: center;
    margin: 12px 0;
}

.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-family: 'Jost', sans-serif;
}

.alert-error {
    background: rgba(220,53,69,0.1);
    color: #dc3545;
    border: 1px solid rgba(220,53,69,0.2);
}

@media (max-width: 768px) {
    .connexion-container {
        margin: 40px auto;
        padding: 0 16px;
    }
    
    .connexion-box {
        padding: 32px 24px;
    }
    
    .connexion-title {
        font-size: 2rem;
    }
}
</style>

<div class="connexion-container">
    <div class="connexion-box">
        <h1 class="connexion-title">Connexion</h1>
        <p class="connexion-subtitle">Accédez à votre espace personnel</p>
        
        <?php if ($erreur): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       placeholder="votre@email.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Code client</label>
                <input type="text" name="code_client" class="form-control" 
                       placeholder="CLI-12345" required>
                <small style="color: #666; font-size: 0.85rem; margin-top: 4px; display: block;">
                    Ce code vous a été envoyé par email lors de votre première réservation
                </small>
            </div>
            
            <button type="submit" class="btn-connexion">
                Me connecter
            </button>
        </form>
        
        <div class="help-section">
            <div class="help-title">Pas encore de compte ?</div>
            <div class="help-text">
                Un compte est créé automatiquement lors de votre première réservation. 
                Si vous n'avez pas reçu votre code client, vérifiez vos spams ou contactez-nous.
            </div>
            
            <div class="help-title">Format du code client:</div>
            <div class="code-format">CLI-12345</div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="reservation-system.php" style="color: var(--or); text-decoration: none; font-weight: 500;">
                    Faire une réservation →
                </a>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>
