<?php
// ════════════════════════════════════════════════════════
// pages/connexion.php — Page de connexion client SEGURO
// Connexion par email + code_client uniquement
// ════════════════════════════════════════════════════════
require_once __DIR__ . '/../includes/connexion.php';
require_once __DIR__ . '/../includes/auth.php';

// Si déjà connecté → rediriger
if (estConnecte()) {
    header('Location: /pages/mon-compte.php');
    exit;
}

$erreur  = '';
$succes  = '';
$redirect = sanitize($_GET['redirect'] ?? '/pages/mon-compte.php');

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $code  = sanitize($_POST['code']  ?? '');

    if (empty($email) || empty($code)) {
        $erreur = 'Veuillez renseigner votre email et votre code client.';
    } else {
        $result = connecterParCode($email, $code);

        if ($result['success']) {
            header('Location: ' . $redirect);
            exit;
        } else {
            $erreur = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — Hôtel SEGURO</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Jost:wght@200;300;400&display=swap" rel="stylesheet">
  <style>
    :root {
      --vert: #1a3a2a; --vert-clair: #2d5c40;
      --or: #c9a84c;   --or-pale: #f5e9c4;
      --noir: #111111; --blanc: #faf8f3;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      background: #f9f7f2;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .connexion-wrap {
      display: grid;
      grid-template-columns: 1fr 1fr;
      max-width: 900px;
      width: 100%;
      min-height: 560px;
      box-shadow: 0 20px 60px rgba(26,58,42,0.12);
    }

    /* Colonne gauche — image */
    .connexion-img {
      background:
        linear-gradient(135deg, rgba(26,58,42,0.75) 0%, rgba(13,26,18,0.4) 100%),
        url('https://images.unsplash.com/photo-1609137144813-7d9921338f24?w=800&q=80')
        center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 40px;
    }

    .img-logo {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 2rem;
      color: var(--or);
      letter-spacing: 0.4em;
      text-transform: uppercase;
      display: block;
      margin-bottom: 8px;
    }
    .img-tagline {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.6rem;
      letter-spacing: 0.5em;
      text-transform: uppercase;
      color: rgba(201,168,76,0.55);
    }

    /* Colonne droite — formulaire */
    .connexion-form {
      background: #fff;
      padding: 56px 48px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-label-top {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.55rem;
      letter-spacing: 0.6em;
      text-transform: uppercase;
      color: var(--or);
      display: block;
      margin-bottom: 12px;
    }

    h1 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 2rem;
      color: var(--vert);
      line-height: 1.2;
      margin-bottom: 8px;
    }
    h1 em { font-style: italic; color: var(--or); }

    .form-sub {
      font-family: 'Cormorant Garamond', serif;
      font-style: italic;
      font-size: 0.95rem;
      color: #aaa;
      margin-bottom: 40px;
      line-height: 1.6;
    }

    .form-group {
      position: relative;
      margin-bottom: 28px;
    }
    .form-group label {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.52rem;
      letter-spacing: 0.45em;
      text-transform: uppercase;
      color: #aaa;
      display: block;
      margin-bottom: 10px;
      transition: color 0.3s;
    }
    .form-group:focus-within label { color: var(--vert); }
    .form-group input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid rgba(201,168,76,0.25);
      color: #1a1a1a;
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.9rem;
      letter-spacing: 0.05em;
      padding: 10px 0;
      outline: none;
      transition: border-color 0.3s;
    }
    .form-group input:focus { border-bottom-color: var(--vert); }
    .form-group input::placeholder { color: rgba(26,58,42,0.2); font-style: italic; }
    .form-group::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 0; height: 1px;
      background: var(--vert);
      transition: width 0.4s ease;
    }
    .form-group:focus-within::after { width: 100%; }

    /* Champ code — style spécial */
    .code-input {
      font-family: 'Cormorant Garamond', serif !important;
      font-size: 1.2rem !important;
      letter-spacing: 0.25em !important;
      text-transform: uppercase;
    }

    .btn-connexion {
      width: 100%;
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.62rem;
      letter-spacing: 0.4em;
      text-transform: uppercase;
      color: #fff;
      background: var(--vert);
      border: none;
      padding: 16px;
      cursor: pointer;
      transition: background 0.3s, transform 0.25s;
      margin-top: 8px;
    }
    .btn-connexion:hover { background: var(--vert-clair); transform: translateY(-1px); }

    /* Messages */
    .msg-erreur {
      background: rgba(220,53,69,0.06);
      border-left: 3px solid #dc3545;
      padding: 14px 16px;
      margin-bottom: 24px;
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.75rem;
      color: #c0392b;
      letter-spacing: 0.04em;
      line-height: 1.7;
    }

    .form-footer {
      margin-top: 28px;
      padding-top: 20px;
      border-top: 1px solid rgba(201,168,76,0.1);
      text-align: center;
    }
    .form-footer p {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.68rem;
      color: #aaa;
      letter-spacing: 0.05em;
      line-height: 1.8;
    }
    .form-footer a {
      color: var(--or);
      text-decoration: none;
      transition: color 0.3s;
    }
    .form-footer a:hover { color: var(--vert); }

    .info-code {
      background: rgba(26,58,42,0.04);
      border-left: 2px solid rgba(201,168,76,0.3);
      padding: 14px 16px;
      margin-bottom: 28px;
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.68rem;
      color: #888;
      line-height: 1.8;
      letter-spacing: 0.04em;
    }

    @media (max-width: 640px) {
      .connexion-wrap { grid-template-columns: 1fr; }
      .connexion-img { min-height: 200px; justify-content: flex-start; padding-top: 40px; }
      .connexion-form { padding: 40px 28px; }
    }
  </style>
</head>
<body>

<div class="connexion-wrap">

  <!-- Côté gauche -->
  <div class="connexion-img">
    <span class="img-logo">SEGURO</span>
    <span class="img-tagline">Agbodrafo · Togo · Afrique de l'Ouest</span>
  </div>

  <!-- Formulaire -->
  <div class="connexion-form">

    <span class="form-label-top">Espace Client</span>
    <h1>Connectez-<br>vous à <em>SEGURO</em></h1>
    <p class="form-sub">
      Votre code client vous a été envoyé par email lors de votre première réservation.
    </p>

    <?php if ($erreur): ?>
      <div class="msg-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="info-code">
      Vous n'avez pas encore de compte ? Il sera créé <strong style="font-weight:400">automatiquement</strong>
      lors de votre première réservation et votre code vous sera envoyé par email.
    </div>

    <form method="POST" action="" novalidate>
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

      <div class="form-group">
        <label for="email">Adresse e-mail</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="votre@email.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          required
          autocomplete="email">
      </div>

      <div class="form-group">
        <label for="code">Votre code client</label>
        <input
          type="text"
          id="code"
          name="code"
          class="code-input"
          placeholder="SEG-2025-XXXX"
          value="<?= htmlspecialchars($_POST['code'] ?? '') ?>"
          maxlength="14"
          required
          autocomplete="off">
      </div>

      <button type="submit" class="btn-connexion">Se connecter</button>
    </form>

    <div class="form-footer">
      <p>
        Vous avez perdu votre code ?<br>
        <a href="/pages/contact.php">Contactez notre équipe</a> —
        nous vous le renverrons immédiatement.
      </p>
    </div>

  </div>
</div>

<script>
  // Formater automatiquement le code en majuscules
  document.getElementById('code').addEventListener('input', function() {
    let v = this.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');
    this.value = v;
  });
</script>

</body>
</html>