<?php if(session_status()===PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hôtel Seguro — LOMÉ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@200;300;400&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --vert:       #1a3a2a;
      --vert-clair: #2d5c40;
      --or:         #c9a84c;
      --or-clair:   #e0c068;
      --or-pale:    #f5e9c4;
      --blanc:      #faf8f3;
      --noir:       #111111;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      background: #ffffff;
      color: #1a1a1a;
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      overflow-x: hidden;
    }

    /* ════════════════════════════════════════════
       HEADER
       — Sur le hero  : transparent, texte blanc
       — Après scroll : blanc, texte sombre
    ════════════════════════════════════════════ */
    #header {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
      padding: 28px 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      /* Par défaut : transparent sur hero */
      background: transparent;
      transition: background 0.5s ease,
                  padding   0.4s ease,
                  box-shadow 0.5s ease;
    }

    /* ── État scrollé : fond blanc propre ── */
    #header.scrolled {
      background: rgba(255, 255, 255, 0.97);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      padding: 14px 60px;
      box-shadow: 0 2px 24px rgba(0,0,0,0.07);
      border-bottom: 1px solid rgba(201, 168, 76, 0.18);
    }

    /* ────────────────────────────────────────
       LOGO
    ──────────────────────────────────────── */
    .logo {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      text-decoration: none;
    }

    .logo-ornament {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 3px;
    }

    .logo-line {
      width: 28px; height: 1px;
      background: linear-gradient(to right, transparent, var(--or));
      transition: background 0.5s;
    }
    .logo-line.right {
      background: linear-gradient(to left, transparent, var(--or));
    }
    /* Après scroll : lignes vert */
    #header.scrolled .logo-line {
      background: linear-gradient(to right, transparent, var(--or));
    }
    #header.scrolled .logo-line.right {
      background: linear-gradient(to left, transparent, var(--or));
    }

    .logo-diamond {
      width: 4px; height: 4px;
      background: var(--or);
      transform: rotate(45deg);
      flex-shrink: 0;
      transition: background 0.5s;
    }
    #header.scrolled .logo-diamond { background: var(--or); }

    .logo-name {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 1.6rem;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      line-height: 1;
      color: var(--or);           /* or sur hero */
      transition: color 0.5s;
    }
    #header.scrolled .logo-name { color: var(--or); }

    .logo-sub {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.52rem;
      letter-spacing: 0.5em;
      text-transform: uppercase;
      margin-top: 4px;
      color: rgba(201,168,76,0.7);
      transition: color 0.5s;
    }
    #header.scrolled .logo-sub { color: rgba(201,168,76,0.6); }

    /* ────────────────────────────────────────
       NAV DESKTOP
    ──────────────────────────────────────── */
    nav {
      display: flex;
      align-items: center;
      gap: 40px;
    }

    nav a {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.68rem;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      text-decoration: none;
      position: relative;
      /* Sur hero : blanc */
      color: rgba(255,255,255,0.88);
      transition: color 0.4s;
    }

    nav a::after {
      content: '';
      position: absolute;
      bottom: -4px; left: 0;
      width: 0; height: 1px;
      background: var(--or);
      transition: width 0.4s ease;
    }

    nav a:hover::after { width: 100%; }
    nav a:hover { color: var(--or); }

    /* Après scroll : texte sombre */
    #header.scrolled nav a {
      color: #2a2a2a;
    }
    #header.scrolled nav a:hover { color: var(--or); }
    
    /* Bouton Connexion après scroll */
    #header.scrolled nav a[href*="connexion"] {
      /* border-color: var(--or) !important;
      color: var(--or) !important;
      background-color: #2d5c40; */
    }

    /* ── Bouton Réserver ── */
    .btn-reserver {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.62rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      padding: 10px 24px;
      text-decoration: none;
      border: 1px solid var(--or);
      color: var(--or) !important;   /* or sur hero */
      transition: background 0.3s, color 0.3s, border-color 0.4s;
    }
    .btn-reserver:hover {
      background: var(--or);
      color: #fff !important;
    }

    /* Après scroll : bouton vert */
    #header.scrolled .btn-reserver {
      border-color: var(--vert);
      color: var(--vert) !important;
    }
    #header.scrolled .btn-reserver:hover {
      background: var(--vert);
      color: #fff !important;
    }

    /* ────────────────────────────────────────
       HAMBURGER MOBILE
    ──────────────────────────────────────── */
    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
      background: none;
      border: none;
      padding: 4px;
    }
    .hamburger span {
      display: block;
      width: 24px; height: 1px;
      background: #ffffff;    /* blanc sur hero */
      transition: background 0.4s, transform 0.3s, opacity 0.3s;
    }
    #header.scrolled .hamburger span { background: #1a1a1a; } /* sombre après scroll */

    .hamburger.open span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

    /* ── Menu mobile plein écran ── */
    .mobile-menu {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(17, 17, 17, 0.97);
      z-index: 999;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 40px;
    }
    .mobile-menu.active { display: flex; }
    .mobile-menu a {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 300;
      letter-spacing: 0.15em;
      color: var(--blanc);
      text-decoration: none;
      transition: color 0.3s;
    }
    .mobile-menu a:hover { color: var(--or); }

    /* ════════════════════════════════════════════
       HERO
    ════════════════════════════════════════════ */
    #hero {
      position: relative;
      width: 100%;
      height: 85vh;
      min-height: 560px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-video {
      position: absolute;
      inset: 0;
      width: 100%; height: 100%;
      object-fit: cover;
      object-position: center;
      z-index: 0;
    }

    .hero-video-fallback {
      position: absolute;
      inset: 0;
      z-index: 0;
      background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80') center/cover no-repeat;
    }

    /* Overlay léger style Singita */
    .hero-overlay {
      position: absolute;
      inset: 0;
      z-index: 1;
      background: linear-gradient(180deg,
        rgba(0,0,0,0.28) 0%,
        rgba(0,0,0,0.06) 35%,
        rgba(0,0,0,0.06) 60%,
        rgba(0,0,0,0.52) 100%
      );
    }

    /* Contenu hero */
    .hero-content {
      position: relative;
      z-index: 10;
      text-align: center;
      padding: 0 24px;
    }

    @keyframes heroFadeIn {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .hero-ornament {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      margin-bottom: 20px;
      animation: heroFadeIn 1.6s ease 0.4s both;
    }
    .hero-orn-line {
      width: 55px; height: 1px;
      background: linear-gradient(to right, transparent, rgba(255,255,255,0.7));
    }
    .hero-orn-line.right {
      background: linear-gradient(to left, transparent, rgba(255,255,255,0.7));
    }
    .hero-orn-diamond {
      width: 5px; height: 5px;
      border: 1px solid rgba(255,255,255,0.8);
      transform: rotate(45deg);
    }

    .hero-title {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: clamp(3.8rem, 9.5vw, 9rem);
      line-height: 0.95;
      color: #ffffff;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      text-shadow: 0 2px 30px rgba(0,0,0,0.18);
      animation: heroFadeIn 1.6s ease 0.6s both;
    }
    .hero-title em {
      font-style: italic;
      font-weight: 300;
      color: var(--or-pale);
    }

    .hero-subtitle {
      font-family: 'Cormorant Garamond', serif;
      font-style: italic;
      font-weight: 300;
      font-size: clamp(1rem, 2.2vw, 1.45rem);
      color: rgba(255,255,255,0.82);
      margin-top: 20px;
      max-width: 520px;
      margin-left: auto;
      margin-right: auto;
      text-shadow: 0 1px 10px rgba(0,0,0,0.2);
      animation: heroFadeIn 1.6s ease 0.8s both;
    }

    .hero-cta-wrap {
      margin-top: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 28px;
      flex-wrap: wrap;
      animation: heroFadeIn 1.6s ease 1.05s both;
    }

    .cta-primary {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.62rem;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      color: var(--noir);
      background: var(--or);
      padding: 15px 42px;
      text-decoration: none;
      display: inline-block;
      transition: background 0.3s, transform 0.25s;
    }
    .cta-primary:hover { background: var(--or-clair); transform: translateY(-2px); }

    .cta-secondary {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.62rem;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.9);
      text-decoration: none;
      position: relative;
      transition: color 0.3s;
    }
    .cta-secondary::after {
      content: '';
      position: absolute;
      bottom: -4px; left: 0;
      width: 100%; height: 1px;
      background: rgba(255,255,255,0.4);
      transition: background 0.3s;
    }
    .cta-secondary:hover { color: var(--or-pale); }
    .cta-secondary:hover::after { background: var(--or); }

    /* Scroll indicator */
    .scroll-indicator {
      position: absolute;
      bottom: 36px; left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      animation: heroFadeIn 1.6s ease 1.4s both;
    }
    .scroll-text {
      font-size: 0.48rem;
      letter-spacing: 0.45em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.5);
    }
    .scroll-line {
      width: 1px; height: 46px;
      background: linear-gradient(to bottom, rgba(255,255,255,0.5), transparent);
      animation: scrollPulse 2s ease-in-out infinite;
    }
    @keyframes scrollPulse {
      0%,100% { opacity: 0.35; transform: scaleY(0.75); }
      50%      { opacity: 1;   transform: scaleY(1); }
    }

    /* Coins décoratifs */
    .hero-corner { position: absolute; z-index: 5; }
    .hero-corner.tl { top: 36px; left: 56px; }
    .hero-corner.tr { top: 36px; right: 56px; transform: scaleX(-1); }
    .hero-corner.bl { bottom: 36px; left: 56px; transform: scaleY(-1); }
    .hero-corner.br { bottom: 36px; right: 56px; transform: scale(-1); }
    .hero-corner svg { opacity: 0.35; }

    /* ════════════════════════════════════════════
       FOOTER
    ════════════════════════════════════════════ */
    #footer {
      background: #111111;
      border-top: 1px solid rgba(201,168,76,0.12);
      padding: 80px 0 0;
      position: relative;
      overflow: hidden;
    }
    #footer::before {
      content: '';
      position: absolute;
      top: 0; left: 50%;
      transform: translateX(-50%);
      width: 1px; height: 60px;
      background: linear-gradient(to bottom, var(--or), transparent);
      opacity: 0.4;
    }

    .footer-logo-area { text-align: center; margin-bottom: 64px; }

    .footer-logo-name {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 2.2rem;
      color: var(--or);
      letter-spacing: 0.4em;
      text-transform: uppercase;
    }
    .footer-logo-tagline {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.55rem;
      letter-spacing: 0.5em;
      text-transform: uppercase;
      color: rgba(201,168,76,0.45);
      margin-top: 10px;
    }

    .footer-divider {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin: 16px auto;
    }
    .footer-divider span {
      display: block; height: 1px; width: 80px;
      background: linear-gradient(to right, transparent, rgba(201,168,76,0.3));
    }
    .footer-divider span.right {
      background: linear-gradient(to left, transparent, rgba(201,168,76,0.3));
    }
    .footer-divider i {
      width: 5px; height: 5px;
      border: 1px solid rgba(201,168,76,0.4);
      transform: rotate(45deg);
      display: block; flex-shrink: 0;
    }

    .footer-nav-col h6 {
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.6rem;
      letter-spacing: 0.4em;
      text-transform: uppercase;
      color: var(--or);
      margin-bottom: 24px;
    }
    .footer-nav-col ul { list-style: none; padding: 0; }
    .footer-nav-col ul li { margin-bottom: 12px; }
    .footer-nav-col ul li a {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 1rem;
      color: rgba(250,248,243,0.5);
      text-decoration: none;
      transition: color 0.3s;
    }
    .footer-nav-col ul li a:hover { color: var(--or-pale); }

    .footer-contact-text {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.75rem;
      color: rgba(250,248,243,0.4);
      letter-spacing: 0.08em;
      line-height: 2;
    }
    .footer-contact-text a {
      color: rgba(201,168,76,0.7);
      text-decoration: none;
      transition: color 0.3s;
    }
    .footer-contact-text a:hover { color: var(--or); }

    .footer-social {
      display: flex;
      gap: 20px;
      margin-top: 24px;
    }
    .footer-social a {
      width: 36px; height: 36px;
      border: 1px solid rgba(201,168,76,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(201,168,76,0.5);
      text-decoration: none;
      font-size: 0.75rem;
      transition: border-color 0.3s, color 0.3s;
    }
    .footer-social a:hover { border-color: var(--or); color: var(--or); }

    .footer-newsletter input[type="email"] {
      background: transparent;
      border: none;
      border-bottom: 1px solid rgba(201,168,76,0.25);
      color: var(--blanc);
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.75rem;
      letter-spacing: 0.1em;
      padding: 10px 0;
      width: 100%;
      outline: none;
      transition: border-color 0.3s;
    }
    .footer-newsletter input::placeholder { color: rgba(250,248,243,0.25); font-style: italic; }
    .footer-newsletter input:focus { border-bottom-color: var(--or); }

    .footer-newsletter button {
      margin-top: 14px;
      background: none;
      border: 1px solid rgba(201,168,76,0.3);
      color: var(--or);
      font-family: 'Jost', sans-serif;
      font-weight: 300;
      font-size: 0.6rem;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      padding: 10px 28px;
      cursor: pointer;
      transition: all 0.3s;
    }
    .footer-newsletter button:hover {
      background: var(--or);
      color: var(--noir);
      border-color: var(--or);
    }

    .footer-bottom {
      margin-top: 64px;
      padding: 24px 60px;
      border-top: 1px solid rgba(201,168,76,0.07);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }
    .footer-bottom p, .footer-bottom a {
      font-family: 'Jost', sans-serif;
      font-weight: 200;
      font-size: 0.6rem;
      letter-spacing: 0.2em;
      color: rgba(250,248,243,0.2);
      text-decoration: none;
    }
    .footer-bottom a:hover { color: var(--or); }
    .footer-bottom-links { display: flex; gap: 24px; }

    /* ════════════════════════════════════════════
       RESPONSIVE
    ════════════════════════════════════════════ */
    @media (max-width: 991px) {
      #header { padding: 20px 30px; }
      #header.scrolled { padding: 14px 30px; }
      nav { display: none; }
      .hamburger { display: flex; }
      .hero-corner { display: none; }
    }
    @media (max-width: 576px) {
      .hero-cta-wrap { flex-direction: column; gap: 20px; }
      .footer-bottom { padding: 20px 24px; flex-direction: column; text-align: center; }
    }
  </style>
</head>
<body>

  <!-- MENU MOBILE -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="/pages/chambres.php"    onclick="closeMenu()">Chambres</a>
    <a href="/pages/services.php"    onclick="closeMenu()">Services</a>
    <a href="/pages/about.php"       onclick="closeMenu()">À Propos</a>
    <a href="/pages/contact.php"     onclick="closeMenu()">Contact</a>
    <?php if (!empty($_SESSION['user_id'])): ?>
    <a href="/pages/mon-compte.php" onclick="closeMenu()" style="color:var(--or);">Mon Compte</a>
    <a href="/pages/deconnexion.php" onclick="closeMenu()" style="color:var(--or);">Déconnexion</a>
    <?php else: ?>
    <a href="/pages/connexion-client.php" onclick="closeMenu()" style="color:var(--or);">Connexion</a>
    <?php endif; ?>
    <a href="/pages/reservation-system.php" onclick="closeMenu()" style="color:var(--or);font-style:italic;">Réserver</a>
  </div>

  <!-- HEADER -->
  <header id="header">

    <a href="/index.php" class="logo">
      <div class="logo-ornament">
        <span class="logo-line"></span>
        <span class="logo-diamond"></span>
        <span class="logo-line right"></span>
      </div>
      <span class="logo-name">Seguro</span>
      <span class="logo-sub">Hôtel &amp; Nature · Agbodrafo, Togo</span>
    </a>

    <nav>
      <a href="/pages/chambres.php">Chambres</a>
      <a href="/pages/services.php">Services</a>
      <a href="/pages/about.php">À Propos</a>
      <a href="/pages/contact.php">Contact</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
      <a href="/pages/mon-compte.php"   class="btn-reserver" >Mon Compte</a>
      <a href="/pages/deconnexion.php"  class="btn-reserver" >Déconnexion</a>
      <?php else: ?>
      <a href="/pages/connexion-client.php" class="btn-reserver" >Connexion</a>
      <?php endif; ?>
      <a href="/pages/reservation-system.php" class="btn-reserver">Réserver</a>
    </nav>

    <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>

  </header>