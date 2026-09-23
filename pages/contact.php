<?php include(__DIR__ . '/../layouts/header.php'); ?>

<style>
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  @keyframes scrollPulse {
    0%,100% { transform: scaleY(0.7); opacity: 0.3; }
    50%      { transform: scaleY(1);   opacity: 1; }
  }

  /* ── Hero split ── */
  .contact-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 60vh;
    margin-top: 0;
  }
  .contact-hero-img {
    position: relative;
    overflow: hidden;
    min-height: 480px;
  }
  .contact-hero-img-bg {
    position: absolute; inset: 0;
    background: url('https://images.unsplash.com/photo-1609137144813-7d9921338f24?w=1200&q=85') center/cover no-repeat;
    transition: transform 0.8s ease;
  }
  .contact-hero-img:hover .contact-hero-img-bg { transform: scale(1.04); }
  .contact-hero-img-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(26,58,42,0.65) 0%, rgba(13,26,18,0.3) 100%);
    z-index: 1;
  }
  .hero-img-badge {
    position: absolute; bottom: 40px; left: 40px; z-index: 2;
    border-left: 2px solid var(--or); padding-left: 16px;
    animation: fadeUp 1.2s ease 0.4s both;
  }
  .hero-img-badge-title {
    font-family: 'Cormorant Garamond', serif; font-weight: 300;
    font-size: 1.1rem; color: #fff; display: block; letter-spacing: 0.05em;
  }
  .hero-img-badge-sub {
    font-family: 'Jost', sans-serif; font-weight: 200;
    font-size: 0.55rem; letter-spacing: 0.4em; text-transform: uppercase;
    color: rgba(201,168,76,0.8); display: block; margin-top: 4px;
  }

  .contact-hero-text {
    background: var(--vert);
    display: flex; flex-direction: column;
    align-items: flex-start; justify-content: center;
    padding: 80px 70px; position: relative; overflow: hidden;
  }
  .contact-hero-text::before {
    content: ''; position: absolute; bottom: -120px; right: -80px;
    width: 350px; height: 350px; border-radius: 50%;
    border: 1px solid rgba(201,168,76,0.08);
  }
  .contact-hero-text::after {
    content: ''; position: absolute; top: -80px; left: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    border: 1px solid rgba(201,168,76,0.06);
  }
  .contact-eyebrow {
    font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem;
    letter-spacing: 0.7em; text-transform: uppercase; color: var(--or);
    display: block; margin-bottom: 24px; position: relative; z-index: 2;
    animation: fadeUp 1.2s ease 0.2s both;
  }
  .contact-hero-title {
    font-family: 'Cormorant Garamond', serif; font-weight: 300;
    font-size: clamp(2.5rem, 4.5vw, 4.5rem); color: #fff; line-height: 1.1;
    letter-spacing: 0.04em; margin-bottom: 28px; position: relative; z-index: 2;
    animation: fadeUp 1.2s ease 0.4s both;
  }
  .contact-hero-title em { font-style: italic; color: var(--or-pale); }
  .contact-hero-desc {
    font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.8rem;
    color: rgba(255,255,255,0.55); line-height: 2; letter-spacing: 0.04em;
    max-width: 360px; margin-bottom: 40px; position: relative; z-index: 2;
    animation: fadeUp 1.2s ease 0.6s both;
  }
  .contact-quick-info { display: flex; flex-direction: column; gap: 16px; position: relative; z-index: 2; animation: fadeUp 1.2s ease 0.8s both; }
  .quick-info-item { display: flex; align-items: center; gap: 14px; }
  .quick-info-icon {
    width: 32px; height: 32px;
    border: 1px solid rgba(201,168,76,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; flex-shrink: 0;
  }
  .quick-info-text { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.72rem; color: rgba(255,255,255,0.6); letter-spacing: 0.05em; }
  .quick-info-text a { color: rgba(201,168,76,0.8); text-decoration: none; transition: color 0.3s; }
  .quick-info-text a:hover { color: var(--or); }

  /* Fil d'Ariane */
  .breadcrumb-bar {
    background: #f9f7f2; border-bottom: 1px solid rgba(201,168,76,0.12);
    padding: 14px 80px; display: flex; align-items: center; gap: 10px;
  }
  .breadcrumb-bar a, .breadcrumb-bar span {
    font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.6rem;
    letter-spacing: 0.22em; text-transform: uppercase; color: #999;
    text-decoration: none; transition: color 0.3s;
  }
  .breadcrumb-bar a:hover { color: var(--or); }
  .breadcrumb-bar .sep { color: rgba(201,168,76,0.4); font-size: 0.45rem; }
  .breadcrumb-bar .current { color: var(--vert); }

  /* ── Formulaire + Infos ── */
  .contact-main { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 0; min-height: 700px; }

  .contact-form-wrap { padding: 80px; background: #fff; border-right: 1px solid rgba(201,168,76,0.1); }

  .section-label {
    font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem;
    letter-spacing: 0.65em; text-transform: uppercase; color: var(--or);
    display: block; margin-bottom: 18px;
  }
  .form-title { font-family: 'Cormorant Garamond', serif; font-weight: 300; font-size: clamp(1.8rem, 3vw, 2.6rem); color: var(--vert); line-height: 1.2; letter-spacing: 0.03em; margin-bottom: 12px; }
  .form-title em { font-style: italic; color: var(--or); }
  .form-subtitle { font-family: 'Cormorant Garamond', serif; font-style: italic; font-weight: 300; font-size: 1rem; color: #aaa; margin-bottom: 48px; letter-spacing: 0.03em; }

  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-bottom: 28px; }
  .form-group { position: relative; margin-bottom: 28px; }
  .form-row .form-group { margin-bottom: 0; }
  .form-group label { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem; letter-spacing: 0.45em; text-transform: uppercase; color: #aaa; display: block; margin-bottom: 10px; transition: color 0.3s; }
  .form-group:focus-within label { color: var(--vert); }
  .form-group input, .form-group select, .form-group textarea {
    width: 100%; background: transparent; border: none;
    border-bottom: 1px solid rgba(201,168,76,0.2); color: #1a1a1a;
    font-family: 'Jost', sans-serif; font-weight: 300; font-size: 0.85rem;
    letter-spacing: 0.05em; padding: 10px 0; outline: none;
    transition: border-color 0.3s; border-radius: 0; -webkit-appearance: none;
  }
  .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-bottom-color: var(--vert); }
  .form-group input::placeholder, .form-group textarea::placeholder { color: rgba(26,58,42,0.2); font-style: italic; }
  .form-group select { cursor: pointer; color: #888; }
  .form-group select option { color: #1a1a1a; }
  .form-group::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 1px; background: var(--vert); transition: width 0.4s ease; }
  .form-group:focus-within::after { width: 100%; }
  .form-group textarea { resize: none; min-height: 110px; line-height: 1.8; }

  .form-consent { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 36px; margin-top: 8px; }
  .form-consent input[type="checkbox"] { width: 16px; height: 16px; border: 1px solid rgba(201,168,76,0.4); background: transparent; flex-shrink: 0; margin-top: 2px; cursor: pointer; accent-color: var(--vert); }
  .form-consent label { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.68rem; color: #aaa; letter-spacing: 0.03em; line-height: 1.7; cursor: pointer; margin-bottom: 0; }
  .form-consent label a { color: var(--or); text-decoration: none; }
  .form-consent label a:hover { text-decoration: underline; }

  .btn-send { font-family: 'Jost', sans-serif; font-weight: 300; font-size: 0.62rem; letter-spacing: 0.4em; text-transform: uppercase; color: #fff; background: var(--vert); border: none; padding: 18px 56px; cursor: pointer; transition: background 0.3s, transform 0.25s; display: inline-block; }
  .btn-send:hover { background: var(--vert-clair); transform: translateY(-2px); }

  .form-success { display: none; padding: 24px 28px; background: rgba(26,58,42,0.05); border-left: 3px solid var(--vert); margin-top: 24px; }
  .form-success p { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1.05rem; color: var(--vert); }

  /* ── Infos contact ── */
  .contact-info-wrap { padding: 80px 60px; background: #f9f7f2; display: flex; flex-direction: column; justify-content: space-between; }
  .info-block { margin-bottom: 48px; }
  .info-block h5 { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem; letter-spacing: 0.55em; text-transform: uppercase; color: var(--or); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid rgba(201,168,76,0.15); }
  .info-item { display: flex; align-items: flex-start; gap: 16px; padding: 14px 0; border-bottom: 1px solid rgba(201,168,76,0.06); transition: padding-left 0.3s; }
  .info-item:hover { padding-left: 6px; }
  .info-item:last-child { border-bottom: none; }
  .info-item-icon { font-size: 1.1rem; width: 28px; flex-shrink: 0; text-align: center; margin-top: 2px; }
  .info-item-label { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem; letter-spacing: 0.35em; text-transform: uppercase; color: #bbb; display: block; margin-bottom: 4px; }
  .info-item-value { font-family: 'Cormorant Garamond', serif; font-weight: 300; font-size: 1rem; color: var(--vert); letter-spacing: 0.03em; }
  .info-item-value a { color: var(--vert); text-decoration: none; transition: color 0.3s; }
  .info-item-value a:hover { color: var(--or); }

  /* Horaires */
  .horaires-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
  .horaire-line { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(201,168,76,0.06); font-family: 'Jost', sans-serif; font-weight: 200; }
  .horaire-service { font-size: 0.65rem; letter-spacing: 0.12em; color: #888; text-transform: uppercase; }
  .horaire-time { font-size: 0.7rem; color: var(--vert); font-weight: 300; }
  .horaire-24 { color: var(--or); font-style: italic; }

  .social-row { display: flex; gap: 12px; }
  .social-btn { display: flex; align-items: center; gap: 10px; padding: 10px 18px; border: 1px solid rgba(201,168,76,0.2); color: #888; text-decoration: none; font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.58rem; letter-spacing: 0.25em; text-transform: uppercase; transition: all 0.3s; }
  .social-btn:hover { border-color: var(--or); color: var(--or); background: rgba(201,168,76,0.04); }

  /* ════════════════════════════════════════════
     SECTION INTERLOCUTEURS PRIVILÉGIÉS §7.2
  ════════════════════════════════════════════ */
  .interlocuteurs-section {
    padding: 100px 0;
    background: var(--vert);
    position: relative; overflow: hidden;
  }
  .interlocuteurs-section::before {
    content: '7';
    position: absolute; top: -60px; right: -20px;
    font-family: 'Cormorant Garamond', serif; font-weight: 300;
    font-size: 30vw; color: rgba(255,255,255,0.02);
    line-height: 1; pointer-events: none;
  }
  .interlocuteurs-inner { padding: 0 80px; position: relative; z-index: 2; }
  .interlocuteurs-header { margin-bottom: 64px; }
  .interlocuteurs-heading {
    font-family: 'Cormorant Garamond', serif; font-weight: 300;
    font-size: clamp(2rem, 3.5vw, 3rem); color: #fff;
    line-height: 1.2; letter-spacing: 0.03em; margin-bottom: 12px;
  }
  .interlocuteurs-heading em { font-style: italic; color: var(--or-pale); }
  .interlocuteurs-intro {
    font-family: 'Jost', sans-serif; font-weight: 200;
    font-size: 0.78rem; color: rgba(255,255,255,0.5);
    line-height: 2; letter-spacing: 0.04em; max-width: 560px;
  }

  .interlocuteurs-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 2px;
  }
  .interlocuteur-card {
    background: rgba(255,255,255,0.04);
    border-top: 1px solid rgba(201,168,76,0.15);
    padding: 44px 40px;
    transition: background 0.4s;
    position: relative;
  }
  .interlocuteur-card:hover { background: rgba(255,255,255,0.08); }
  .interlocuteur-card::before {
    content: '';
    position: absolute; top: 0; left: 0;
    width: 0; height: 2px; background: var(--or);
    transition: width 0.5s ease;
  }
  .interlocuteur-card:hover::before { width: 100%; }

  .interlocuteur-dept {
    font-family: 'Jost', sans-serif; font-weight: 200;
    font-size: 0.5rem; letter-spacing: 0.55em; text-transform: uppercase;
    color: var(--or); display: block; margin-bottom: 16px;
  }
  .interlocuteur-name {
    font-family: 'Cormorant Garamond', serif; font-weight: 300;
    font-size: 1.6rem; color: #fff;
    line-height: 1.2; margin-bottom: 16px; letter-spacing: 0.03em;
  }
  .interlocuteur-name em { font-style: italic; color: var(--or-pale); }
  .interlocuteur-desc {
    font-family: 'Jost', sans-serif; font-weight: 200;
    font-size: 0.75rem; color: rgba(255,255,255,0.45);
    line-height: 1.9; letter-spacing: 0.03em; margin-bottom: 28px;
  }
  .interlocuteur-contacts { display: flex; flex-direction: column; gap: 10px; }
  .interlocuteur-contact-line {
    display: flex; align-items: center; gap: 12px;
    font-family: 'Jost', sans-serif; font-weight: 200;
    font-size: 0.68rem; color: rgba(255,255,255,0.55); letter-spacing: 0.06em;
  }
  .interlocuteur-contact-line a {
    color: rgba(201,168,76,0.8); text-decoration: none; transition: color 0.3s;
  }
  .interlocuteur-contact-line a:hover { color: var(--or); }
  .interlocuteur-contact-icon { font-size: 0.8rem; flex-shrink: 0; }

  /* ── Carte ── */
  .map-section { position: relative; }
  .map-header { padding: 80px 80px 48px; display: flex; align-items: flex-end; justify-content: space-between; background: #fff; }
  .map-heading { font-family: 'Cormorant Garamond', serif; font-weight: 300; font-size: clamp(1.8rem, 3vw, 2.6rem); color: var(--vert); line-height: 1.2; }
  .map-heading em { font-style: italic; color: var(--or); }
  .map-address { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.72rem; color: #888; line-height: 2; letter-spacing: 0.05em; text-align: right; }
  .map-embed { width: 100%; height: 420px; display: block; border: none; filter: grayscale(30%) contrast(1.05); transition: filter 0.4s; }
  .map-embed:hover { filter: grayscale(0%) contrast(1); }
  .map-access-bar { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: rgba(201,168,76,0.1); }
  .access-item { background: #fff; padding: 28px 24px; text-align: center; transition: background 0.3s; }
  .access-item:hover { background: #f9f7f2; }
  .access-icon { font-size: 1.4rem; display: block; margin-bottom: 10px; }
  .access-label { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.55rem; letter-spacing: 0.4em; text-transform: uppercase; color: #bbb; display: block; margin-bottom: 6px; }
  .access-value { font-family: 'Cormorant Garamond', serif; font-weight: 300; font-size: 1rem; color: var(--vert); letter-spacing: 0.03em; }

  /* ── FAQ ── */
  .faq-section { padding: 100px 80px; background: #f9f7f2; position: relative; overflow: hidden; }
  .faq-section::before { content: '?'; position: absolute; right: -40px; top: -60px; font-family: 'Cormorant Garamond', serif; font-size: 30vw; color: rgba(26,58,42,0.03); line-height: 1; pointer-events: none; }
  .faq-header { margin-bottom: 60px; }
  .faq-heading { font-family: 'Cormorant Garamond', serif; font-weight: 300; font-size: clamp(2rem, 3.5vw, 3rem); color: var(--vert); line-height: 1.2; }
  .faq-heading em { font-style: italic; color: var(--or); }
  .faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2px; }
  .faq-item { background: #fff; padding: 32px 36px; border-top: 1px solid rgba(201,168,76,0.1); cursor: pointer; transition: background 0.3s; }
  .faq-item:hover { background: #fefdf8; }
  .faq-q { font-family: 'Cormorant Garamond', serif; font-weight: 400; font-size: 1.15rem; color: var(--vert); letter-spacing: 0.03em; margin-bottom: 12px; display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; }
  .faq-q-icon { font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: var(--or); line-height: 1; flex-shrink: 0; transition: transform 0.3s; }
  .faq-item.open .faq-q-icon { transform: rotate(45deg); }
  .faq-a { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.75rem; color: #888; line-height: 2; letter-spacing: 0.03em; display: none; }
  .faq-item.open .faq-a { display: block; }

  /* ── Reveal ── */
  .reveal { opacity: 0; transform: translateY(36px); transition: opacity 0.8s ease, transform 0.8s ease; }
  .reveal.visible { opacity: 1; transform: translateY(0); }

  @media (max-width: 1100px) {
    .contact-main { grid-template-columns: 1fr; }
    .contact-info-wrap { padding: 60px 40px; }
    .contact-form-wrap { padding: 60px 40px; border-right: none; border-bottom: 1px solid rgba(201,168,76,0.1); }
    .map-header { padding: 60px 40px 40px; }
    .faq-section { padding: 80px 40px; }
    .breadcrumb-bar { padding: 14px 40px; }
    .map-access-bar { grid-template-columns: 1fr 1fr; }
    .horaires-grid { grid-template-columns: 1fr; }
    .interlocuteurs-inner { padding: 0 40px; }
    .interlocuteurs-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 767px) {
    .contact-hero { grid-template-columns: 1fr; }
    .contact-hero-img { min-height: 300px; }
    .contact-hero-text { padding: 60px 32px; }
    .contact-form-wrap, .contact-info-wrap { padding: 48px 24px; }
    .form-row { grid-template-columns: 1fr; }
    .map-header { padding: 48px 24px 32px; flex-direction: column; align-items: flex-start; gap: 20px; }
    .map-header .map-address { text-align: left; }
    .faq-section { padding: 60px 24px; }
    .faq-grid { grid-template-columns: 1fr; }
    .map-access-bar { grid-template-columns: 1fr 1fr; }
    .breadcrumb-bar { padding: 14px 24px; }
    .social-row { flex-wrap: wrap; }
    .interlocuteurs-inner { padding: 0 24px; }
    .interlocuteur-card { padding: 32px 24px; }
  }
</style>

<!-- HERO SPLIT -->
<div class="contact-hero" style="margin-top:0;padding-top:0;">
  <div class="contact-hero-img">
    <div class="contact-hero-img-bg"></div>
    <div class="contact-hero-img-overlay"></div>
    <div class="hero-img-badge">
      <span class="hero-img-badge-title">Hôtel SEGURO</span>
      <span class="hero-img-badge-sub">Agbodrafo · Togo · Afrique de l'Ouest</span>
    </div>
  </div>
  <div class="contact-hero-text">
    <span class="contact-eyebrow">§ 7 · Contact &amp; Partenariats</span>
    <h1 class="contact-hero-title">
      Prêt à vivre<br>l'expérience <em>SEGURO</em> ?
    </h1>
    <p class="contact-hero-desc">
      Toutes les informations pour nous rejoindre, réserver votre séjour
      ou discuter d'une future collaboration avec nos équipes.
    </p>
    <div class="contact-quick-info">
      <div class="quick-info-item">
        <div class="quick-info-icon">📞</div>
        <span class="quick-info-text"><a href="tel:+22800000000">+228 00 00 00 00</a></span>
      </div>
      <div class="quick-info-item">
        <div class="quick-info-icon">✉️</div>
        <span class="quick-info-text"><a href="mailto:contact@hotelseguro.com">contact@hotelseguro.com</a></span>
      </div>
      <div class="quick-info-item">
        <div class="quick-info-icon">🌐</div>
        <span class="quick-info-text"><a href="http://www.hotelseguro.com">www.hotelseguro.com</a></span>
      </div>
      <div class="quick-info-item">
        <div class="quick-info-icon">📍</div>
        <span class="quick-info-text">Agbodrafo, entrée de Aného — Togo</span>
      </div>
    </div>
  </div>
</div>

<!-- Fil d'Ariane -->
<div class="breadcrumb-bar">
  <a href="/acathon/index.php">Accueil</a>
  <span class="sep">◆</span>
  <span class="current">Contact &amp; Partenariats</span>
</div>

<!-- FORMULAIRE + INFOS -->
<div class="contact-main">

  <!-- Formulaire -->
  <div class="contact-form-wrap reveal">
    <span class="section-label">Écrivez-nous</span>
    <h2 class="form-title">Votre message,<br>notre <em>priorité</em></h2>
    <p class="form-subtitle">Réponse garantie sous 2 heures en journée.</p>

    <form id="contactForm" onsubmit="handleSubmit(event)" novalidate>
      <div class="form-row">
        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
        </div>
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" placeholder="Dupont" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" placeholder="jean@exemple.com" required>
        </div>
        <div class="form-group">
          <label for="tel">Téléphone</label>
          <input type="tel" id="tel" name="tel" placeholder="+228 00 00 00 00">
        </div>
      </div>
      <div class="form-group">
        <label for="sujet">Sujet de votre demande</label>
        <select id="sujet" name="sujet" required>
          <option value="" disabled selected>Sélectionnez un sujet…</option>
          <option value="reservation">Réservation de chambre</option>
          <option value="evenement">Séminaire / Conférence / Événement</option>
          <option value="skibar">Privatisation du Skibar</option>
          <option value="nautique">Jet Ski &amp; Croisière Yacht</option>
          <option value="partenariat">Partenariat stratégique</option>
          <option value="presse">Demande Presse &amp; Médias</option>
          <option value="autre">Autre demande</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="arrivee">Date d'arrivée</label>
          <input type="date" id="arrivee" name="arrivee">
        </div>
        <div class="form-group">
          <label for="depart">Date de départ</label>
          <input type="date" id="depart" name="depart">
        </div>
      </div>
      <div class="form-group">
        <label for="message">Votre message</label>
        <textarea id="message" name="message" placeholder="Décrivez votre demande, nous sommes à votre écoute…" required></textarea>
      </div>
      <div class="form-consent">
        <input type="checkbox" id="consent" name="consent" required>
        <label for="consent">
          J'accepte que mes données soient utilisées pour traiter ma demande,
          conformément à la <a href="#">politique de confidentialité</a> de l'Hôtel SEGURO.
        </label>
      </div>
      <button type="submit" class="btn-send">Envoyer le message</button>
      <div class="form-success" id="formSuccess">
        <p>✦ Merci pour votre message. Notre équipe vous répondra dans les plus brefs délais.</p>
      </div>
    </form>
  </div>

  <!-- Infos -->
  <div class="contact-info-wrap reveal">

    <!-- §7.1 Coordonnées -->
    <div class="info-block">
      <h5>§ 7.1 · Coordonnées de l'Hôtel</h5>
      <div class="info-item">
        <span class="info-item-icon">📍</span>
        <div>
          <span class="info-item-label">Adresse</span>
          <span class="info-item-value">Hôtel SEGURO<br>Agbodrafo, entrée de Aného<br>Togo, Afrique de l'Ouest</span>
        </div>
      </div>
      <div class="info-item">
        <span class="info-item-icon">📞</span>
        <div>
          <span class="info-item-label">Téléphone Principal</span>
          <span class="info-item-value"><a href="tel:+22800000000">+228 00 00 00 00</a></span>
        </div>
      </div>
      <div class="info-item">
        <span class="info-item-icon">✉️</span>
        <div>
          <span class="info-item-label">Réservations</span>
          <span class="info-item-value"><a href="mailto:reservations@hotelseguro.com">reservations@hotelseguro.com</a></span>
        </div>
      </div>
      <div class="info-item">
        <span class="info-item-icon">📧</span>
        <div>
          <span class="info-item-label">Informations Générales</span>
          <span class="info-item-value"><a href="mailto:contact@hotelseguro.com">contact@hotelseguro.com</a></span>
        </div>
      </div>
      <div class="info-item">
        <span class="info-item-icon">🌐</span>
        <div>
          <span class="info-item-label">Site Web</span>
          <span class="info-item-value"><a href="http://www.hotelseguro.com" target="_blank">www.hotelseguro.com</a></span>
        </div>
      </div>
    </div>

    <!-- Horaires -->
    <div class="info-block">
      <h5>Horaires des Services</h5>
      <div class="horaires-grid">
        <div class="horaire-line">
          <span class="horaire-service">Réception</span>
          <span class="horaire-time horaire-24">24h/24</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Conciergerie</span>
          <span class="horaire-time horaire-24">24h/24</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Room Service</span>
          <span class="horaire-time horaire-24">24h/24</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Restaurant</span>
          <span class="horaire-time">07h – 22h</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Skibar</span>
          <span class="horaire-time">16h – 00h</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Piscine / Jacuzzi</span>
          <span class="horaire-time">07h – 21h</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Fitness</span>
          <span class="horaire-time horaire-24">24h/24</span>
        </div>
        <div class="horaire-line">
          <span class="horaire-service">Check-in</span>
          <span class="horaire-time">À partir de 14h</span>
        </div>
      </div>
    </div>

    <!-- Réseaux -->
    <div class="info-block">
      <h5>Nous Suivre</h5>
      <div class="social-row">
        <a href="#" class="social-btn"><span>IG</span> Instagram</a>
        <a href="#" class="social-btn"><span>FB</span> Facebook</a>
        <a href="#" class="social-btn"><span>LI</span> LinkedIn</a>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════
     §7.2 — INTERLOCUTEURS PRIVILÉGIÉS
══════════════════════════════════════════ -->
<section class="interlocuteurs-section">
  <div class="interlocuteurs-inner">

    <div class="interlocuteurs-header reveal">
      <span class="section-label" style="color:rgba(201,168,76,0.7);">§ 7.2 · Pour Toute Demande Spécifique</span>
      <h2 class="interlocuteurs-heading">
        Nos Interlocuteurs<br><em>Privilégiés</em>
      </h2>
      <p class="interlocuteurs-intro">
        Notre équipe de direction se tient à votre disposition pour toute demande spécifique.
        Que ce soit pour un événement corporate, un partenariat ou une demande stratégique,
        vous avez un contact direct et dédié chez SEGURO.
      </p>
    </div>

    <div class="interlocuteurs-grid reveal">

      <!-- Département Commercial & Événementiel -->
      <div class="interlocuteur-card">
        <span class="interlocuteur-dept">Département Commercial &amp; Événementiel</span>
        <h3 class="interlocuteur-name">
          Séminaires, Conférences<br>&amp; <em>Événements Privés</em>
        </h3>
        <p class="interlocuteur-desc">
          Pour l'organisation de vos séminaires, conférences et événements privés.
          Nos espaces modulables accueillent de 10 à 120 personnes. Privatisation
          du Skibar panoramique sur demande. Devis personnalisé sous 24h.
        </p>
        <div class="interlocuteur-contacts">
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">👤</span>
            Responsable Commercial SEGURO
          </div>
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">✉️</span>
            <a href="mailto:commercial@hotelseguro.com">commercial@hotelseguro.com</a>
          </div>
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">📞</span>
            <a href="tel:+22800000000">Ligne directe sur demande</a>
          </div>
        </div>
      </div>

      <!-- Direction Générale -->
      <div class="interlocuteur-card">
        <span class="interlocuteur-dept">Direction Générale</span>
        <h3 class="interlocuteur-name">
          Partenariats<br><em>Stratégiques</em>
        </h3>
        <p class="interlocuteur-desc">
          Pour les demandes de partenariats stratégiques, collaborations institutionnelles,
          demandes presse et médias, ou toute proposition de coopération avec l'Hôtel SEGURO.
          Marius WATEBA, Directeur Général, se tient personnellement à votre disposition.
        </p>
        <div class="interlocuteur-contacts">
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">👤</span>
            Marius WATEBA — Directeur Général
          </div>
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">✉️</span>
            <a href="mailto:direction@hotelseguro.com">direction@hotelseguro.com</a>
          </div>
          <div class="interlocuteur-contact-line">
            <span class="interlocuteur-contact-icon">🌐</span>
            <a href="http://www.hotelseguro.com" target="_blank">www.hotelseguro.com</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CARTE & ACCÈS -->
<section class="map-section">
  <div class="map-header reveal">
    <div>
      <span class="section-label">Comment nous trouver</span>
      <h2 class="map-heading">Accès &amp; <em>Localisation</em></h2>
    </div>
    <div class="map-address">
      Hôtel SEGURO<br>
      Agbodrafo, entrée de Aného<br>
      Togo, Afrique de l'Ouest<br>
      <a href="https://maps.google.com/?q=Agbodrafo,Togo" target="_blank"
         style="color:var(--or);text-decoration:none;font-size:0.65rem;letter-spacing:0.25em;text-transform:uppercase;">
        Ouvrir dans Google Maps →
      </a>
    </div>
  </div>

  <iframe
    class="map-embed"
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126916.28!2d1.5!3d6.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023f00a0a0a0a0a%3A0x0!2sAgbodrafo%2C+Togo!5e0!3m2!1sfr!2sfr!4v1"
    allowfullscreen="" loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"
    title="Hôtel SEGURO — Agbodrafo, Togo">
  </iframe>

  <div class="map-access-bar reveal">
    <div class="access-item">
      <span class="access-icon">✈️</span>
      <span class="access-label">Aéroport de Lomé</span>
      <span class="access-value">45 min en voiture</span>
    </div>
    <div class="access-item">
      <span class="access-icon">🚘</span>
      <span class="access-label">Transfert Privé</span>
      <span class="access-value">Sur réservation</span>
    </div>
    <div class="access-item">
      <span class="access-icon">🏙️</span>
      <span class="access-label">Centre de Lomé</span>
      <span class="access-value">55 km · 45 min</span>
    </div>
    <div class="access-item">
      <span class="access-icon">🅿️</span>
      <span class="access-label">Parking Privé</span>
      <span class="access-value">Gratuit &amp; Sécurisé</span>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq-section">
  <div class="faq-header reveal">
    <span class="section-label">Questions Fréquentes</span>
    <h2 class="faq-heading">Tout ce que vous<br>devez <em>savoir</em></h2>
  </div>
  <div class="faq-grid reveal">

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Quels sont les horaires de check-in et check-out ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        L'arrivée est possible à partir de 14h00 et le départ est demandé avant 12h00.
        Des arrangements pour un early check-in ou late check-out peuvent être effectués
        selon la disponibilité — contactez notre conciergerie en amont de votre arrivée.
      </div>
    </div>

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Proposez-vous un transfert depuis l'aéroport de Lomé ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        Oui, nous proposons un service de transfert en véhicule climatisé avec chauffeur privé
        depuis l'aéroport international Gnassingbé Eyadéma de Lomé. Ce service est disponible
        sur réservation préalable, 24h/24.
      </div>
    </div>

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Comment réserver une session de Jet Ski ou une croisière Yacht ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        Nos activités nautiques exclusives — jet ski et croisières yacht — sont accessibles
        à nos hôtes et aux visiteurs extérieurs sur réservation. Contactez notre conciergerie
        ou utilisez le formulaire ci-dessus avec le sujet "Jet Ski &amp; Croisière Yacht".
      </div>
    </div>

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Peut-on privatiser le Skibar pour un événement ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        Oui, le Skibar panoramique est privatisable pour vos lancements de produits,
        cocktails d'entreprise et soirées privées. Contactez notre Département Commercial
        à commercial@hotelseguro.com pour un devis personnalisé sous 24h.
      </div>
    </div>

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Quelle est votre politique d'annulation ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        Les annulations effectuées plus de 48h avant la date d'arrivée sont entièrement
        remboursées. En dessous de 48h, une nuit est retenue. Pour les séjours de groupe,
        les événements et les activités nautiques, des conditions spécifiques s'appliquent.
      </div>
    </div>

    <div class="faq-item" onclick="toggleFaq(this)">
      <div class="faq-q">
        Comment proposer un partenariat avec l'Hôtel SEGURO ?
        <span class="faq-q-icon">+</span>
      </div>
      <div class="faq-a">
        Pour toute proposition de partenariat stratégique, collaboration institutionnelle
        ou demande presse, contactez directement notre Direction Générale à
        direction@hotelseguro.com. Marius WATEBA répond personnellement à toutes
        les demandes de partenariat.
      </div>
    </div>

  </div>
</section>

<script>
  const reveals = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), i * 60);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  reveals.forEach(el => obs.observe(el));

  function toggleFaq(item) {
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }

  function handleSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('contactForm');
    const success = document.getElementById('formSuccess');
    const btn = form.querySelector('.btn-send');
    btn.textContent = 'Envoi en cours…';
    btn.disabled = true;
    setTimeout(() => {
      form.style.opacity = '0.4';
      form.style.pointerEvents = 'none';
      success.style.display = 'block';
      btn.textContent = 'Message envoyé ✓';
    }, 1200);
  }

  const arrivee = document.getElementById('arrivee');
  const depart  = document.getElementById('depart');
  const today   = new Date().toISOString().split('T')[0];
  arrivee.min = today;
  arrivee.addEventListener('change', () => {
    depart.min = arrivee.value;
    if (depart.value && depart.value <= arrivee.value) depart.value = '';
  });
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>