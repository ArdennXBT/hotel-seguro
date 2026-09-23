<?php

// Protection contre la redéclaration
if (!function_exists('genererCodeClient')) {

require_once( 'connexion.php');


// ════════════════════════════════════════════════════════
// GÉNÉRATION DU CODE CLIENT
// Format : SEG-AAAA-XXXX (ex: SEG-2025-7K3M)
// ════════════════════════════════════════════════════════
function genererCodeClient(): string {
    $pdo  = getPDO();
    $year = date('Y');

    do {
        $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // sans 0/O/1/I
        $suffix = '';
        for ($i = 0; $i < 4; $i++) {
            $suffix .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $code = "SEG-{$year}-{$suffix}";

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE code_client = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetchColumn() > 0); // réessaie si collision

    return $code;
}

// ════════════════════════════════════════════════════════
// GÉNÉRATION DE LA RÉFÉRENCE DE RÉSERVATION
// Format : SEGURO-2025-XXXX (ex: SEGURO-2025-4821)
// ════════════════════════════════════════════════════════
function genererReferenceReservation(): string {
    $pdo  = getPDO();
    $year = date('Y');

    do {
        $num = random_int(1000, 9999);
        $ref = "SEGURO-{$year}-{$num}";

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE reference = ?");
        $stmt->execute([$ref]);
    } while ($stmt->fetchColumn() > 0);

    return $ref;
}

// ════════════════════════════════════════════════════════
// CRÉATION AUTOMATIQUE DU COMPTE CLIENT
// Appelé lors de la première réservation
// Retourne : ['success' => true, 'user' => [...], 'code' => 'SEG-...']
// ════════════════════════════════════════════════════════
function creerCompteClient(array $data): array {
    $pdo = getPDO();

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([strtolower(trim($data['email']))]);
    $existant = $stmt->fetch();

    if ($existant) {
        // Compte déjà existant → connexion directe
        connecterUser($existant);
        return [
            'success'      => true,
            'nouveau'      => false,
            'user'         => $existant,
            'code_client'  => $existant['code_client'],
        ];
    }

    // Nouveau compte
    $id          = genererUUID();
    $code_client = genererCodeClient();
    $email       = strtolower(trim($data['email']));
    $nom         = ucfirst(strtolower(trim($data['nom'])));
    $prenom      = ucfirst(strtolower(trim($data['prenom'])));
    $telephone   = trim($data['telephone'] ?? '');
    $pays        = trim($data['pays'] ?? '');

    $stmt = $pdo->prepare("
        INSERT INTO users (id, nom, prenom, email, code_client, telephone, pays, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'client')
    ");
    $stmt->execute([$id, $nom, $prenom, $email, $code_client, $telephone, $pays]);

    $user = [
        'id'          => $id,
        'nom'         => $nom,
        'prenom'      => $prenom,
        'email'       => $email,
        'code_client' => $code_client,
        'telephone'   => $telephone,
        'pays'        => $pays,
        'role'        => 'client',
    ];

    connecterUser($user);

    // Envoyer le code par email
    envoyerEmailBienvenue($user);

    logAction($id, 'COMPTE_CREE', 'users', $id, null, $user);

    return [
        'success'     => true,
        'nouveau'     => true,
        'user'        => $user,
        'code_client' => $code_client,
    ];
}

// ════════════════════════════════════════════════════════
// CONNEXION PAR EMAIL + CODE CLIENT
// ════════════════════════════════════════════════════════
function connecterParCode(string $email, string $code): array {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        SELECT * FROM users
        WHERE email = ? AND code_client = ?
        LIMIT 1
    ");
    $stmt->execute([strtolower(trim($email)), strtoupper(trim($code))]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Email ou code incorrect.'];
    }

    // Mettre à jour last_login
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
        ->execute([$user['id']]);

    connecterUser($user);
    logAction($user['id'], 'CONNEXION', 'users', $user['id'], null, null);

    return ['success' => true, 'user' => $user];
}

// ════════════════════════════════════════════════════════
// SESSION — Connecter / Déconnecter / Vérifier
// ════════════════════════════════════════════════════════
function connecterUser(array $user): void {
    $_SESSION['user_id']     = $user['id'];
    $_SESSION['user_nom']    = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_role']   = $user['role'];
    $_SESSION['user_code']   = $user['code_client'];
    $_SESSION['connecte']    = true;
}

function deconnecterUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function estConnecte(): bool {
    return !empty($_SESSION['connecte']) && !empty($_SESSION['user_id']);
}

function estAdmin(): bool {
    return estConnecte() &&
           in_array($_SESSION['user_role'], ['admin', 'super_admin']);
}

function getUserConnecte(): ?array {
    if (!estConnecte()) return null;
    return [
        'id'          => $_SESSION['user_id'],
        'nom'         => $_SESSION['user_nom'],
        'prenom'      => $_SESSION['user_prenom'],
        'email'       => $_SESSION['user_email'],
        'role'        => $_SESSION['user_role'],
        'code_client' => $_SESSION['user_code'],
    ];
}

// Protéger une page — redirige si non connecté
function requireLogin(string $redirect = '/acathon/pages/mon-compte.php'): void {
    if (!estConnecte()) {
        header('Location: ' . BASE_URL . '/pages/connexion.php?redirect=' . urlencode($redirect));
        exit;
    }
}

// Protéger une page admin
function requireAdmin(): void {
    if (!estAdmin()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// ════════════════════════════════════════════════════════
// EMAIL — Bienvenue avec code client
// ════════════════════════════════════════════════════════
function envoyerEmailBienvenue(array $user): void {
    $to      = $user['email'];
    $subject = "Bienvenue à l'Hôtel SEGURO — Votre code d'accès";

    $message = "
    Bonjour {$user['prenom']} {$user['nom']},

    Votre compte Hôtel SEGURO a été créé avec succès.

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    Votre email       : {$user['email']}
    Votre code client : {$user['code_client']}
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Conservez ce code précieusement. Il vous permettra de :
    → Consulter vos réservations
    → Modifier ou annuler un séjour
    → Vous connecter à votre espace client

    Pour vous connecter : " . BASE_URL . "/pages/connexion.php

    L'équipe Hôtel SEGURO
    Agbodrafo, Togo · contact@hotelseguro.com
    ";

    $headers  = "From: noreply@hotelseguro.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // En développement MAMP : mail() peut ne pas fonctionner
    // Décommenter en production ou utiliser PHPMailer/SMTP
    // mail($to, $subject, $message, $headers);

    // Pour dev : afficher dans les logs
    error_log("EMAIL BIENVENUE → {$to} | Code: {$user['code_client']}");
}

// Email de confirmation de réservation
function envoyerEmailConfirmationResa(array $user, array $reservation): void {
    $to      = $user['email'];
    $subject = "Réservation {$reservation['reference']} — Hôtel SEGURO";

    $message = "
    Bonjour {$user['prenom']},

    Votre demande de réservation a bien été enregistrée.

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    Référence : {$reservation['reference']}
    Chambre   : {$reservation['chambre_nom']}
    Arrivée   : {$reservation['date_arrivee']}
    Départ    : {$reservation['date_depart']}
    Total     : " . number_format($reservation['prix_total'], 0, ',', ' ') . " FCFA
    Statut    : En attente de validation
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Notre équipe va valider votre réservation sous 24h.
    Vous recevrez un email de confirmation dès validation.

    Suivre votre réservation : " . BASE_URL . "/pages/mon-compte.php

    L'équipe Hôtel SEGURO
    ";

    error_log("EMAIL RESA → {$to} | Réf: {$reservation['reference']}");
    // mail($to, $subject, $message, "From: noreply@hotelseguro.com\r\n");
}

// Email quand admin valide
function envoyerEmailValidation(array $user, array $reservation): void {
    $to      = $user['email'];
    $subject = "Réservation confirmée — {$reservation['reference']} — Hôtel SEGURO";

    $message = "
    Bonjour {$user['prenom']},

    Excellente nouvelle ! Votre réservation est confirmée.

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    Référence : {$reservation['reference']}
    Chambre   : {$reservation['chambre_nom']}
    Arrivée   : {$reservation['date_arrivee']} dès 14h00
    Départ    : {$reservation['date_depart']} avant 12h00
    Total     : " . number_format($reservation['prix_total'], 0, ',', ' ') . " FCFA
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Nous avons hâte de vous accueillir.

    L'équipe Hôtel SEGURO · +228 00 00 00 00
    ";

    error_log("EMAIL VALIDATION → {$to} | Réf: {$reservation['reference']}");
    // mail($to, $subject, $message, "From: noreply@hotelseguro.com\r\n");
}

// ════════════════════════════════════════════════════════
// UTILITAIRES
// ════════════════════════════════════════════════════════
function genererUUID(): string {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function logAction(
    ?string $userId,
    string  $action,
    ?string $table,
    ?string $cibleId,
    ?array  $avant,
    ?array  $apres
): void {
    try {
        $pdo  = getPDO();
        $stmt = $pdo->prepare("
            INSERT INTO logs_actions
                (id, user_id, action, table_cible, cible_id, avant, apres, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            genererUUID(),
            $userId,
            $action,
            $table,
            $cibleId,
            $avant  ? json_encode($avant,  JSON_UNESCAPED_UNICODE) : null,
            $apres  ? json_encode($apres,  JSON_UNESCAPED_UNICODE) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (Exception $e) {
        error_log("LogAction error: " . $e->getMessage());
    }
}

// Sanitizer simple pour les entrées
function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

// Formater un prix en FCFA
function formatPrix(float $prix): string {
    return number_format($prix, 0, ',', ' ') . ' FCFA';
}

// Formater une date FR
function formatDate(string $date): string {
    $mois = [
        '01' => 'janvier', '02' => 'février', '03' => 'mars',
        '04' => 'avril',   '05' => 'mai',      '06' => 'juin',
        '07' => 'juillet', '08' => 'août',     '09' => 'septembre',
        '10' => 'octobre', '11' => 'novembre', '12' => 'décembre',
    ];
    [$y, $m, $d] = explode('-', $date);
    return intval($d) . ' ' . $mois[$m] . ' ' . $y;
}

} // Fin de la protection contre la redéclaration