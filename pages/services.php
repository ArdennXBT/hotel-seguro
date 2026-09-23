


<?php include(__DIR__ . '/../layouts/header.php'); ?>

<style>
  /* ════════════════════════════════════════════
     PAGE SERVICES — Hôtel Seguro
  ════════════════════════════════════════════ */

  /* Hero de page — sobre, pas de vidéo */
  .page-hero {
    position: relative;
    width: 100%;
    height: 52vh;
    min-height: 380px;
    background:
      linear-gradient(180deg, rgba(0,0,0,0.45) 0%, rgba(0,0,0,0.25) 60%, rgba(0,0,0,0.6) 100%),
      url('https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1920&q=85') center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .page-hero-content { position: relative; z-index: 2; }

  .page-eyebrow {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.58rem;
    letter-spacing: 0.65em;
    text-transform: uppercase;
    color: var(--or);
    margin-bottom: 18px;
    animation: heroFadeIn 1.4s ease 0.2s both;
  }

  .page-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2.8rem, 6vw, 5.5rem);
    color: #fff;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    line-height: 1;
    animation: heroFadeIn 1.4s ease 0.4s both;
  }

  .page-title em {
    font-style: italic;
    color: var(--or-pale);
  }

  .page-ornament {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin: 20px auto 0;
    animation: heroFadeIn 1.4s ease 0.6s both;
  }
  .page-orn-line {
    width: 50px; height: 1px;
    background: linear-gradient(to right, transparent, rgba(201,168,76,0.6));
  }
  .page-orn-line.r { background: linear-gradient(to left, transparent, rgba(201,168,76,0.6)); }
  .page-orn-dot {
    width: 5px; height: 5px;
    border: 1px solid rgba(201,168,76,0.7);
    transform: rotate(45deg);
  }

  /* ── Fil d'Ariane ── */
  .breadcrumb-bar {
    background: #f9f7f2;
    border-bottom: 1px solid rgba(201,168,76,0.12);
    padding: 14px 60px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .breadcrumb-bar a, .breadcrumb-bar span {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #888;
    text-decoration: none;
    transition: color 0.3s;
  }
  .breadcrumb-bar a:hover { color: var(--or); }
  .breadcrumb-bar .sep { color: rgba(201,168,76,0.4); font-size: 0.5rem; }
  .breadcrumb-bar .current { color: var(--vert); }

  /* ── Intro section ── */
  .section-intro {
    padding: 100px 0 60px;
    text-align: center;
  }

  .section-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.58rem;
    letter-spacing: 0.6em;
    text-transform: uppercase;
    color: var(--or);
    margin-bottom: 20px;
    display: block;
  }

  .section-heading {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2rem, 4vw, 3.2rem);
    color: var(--vert);
    letter-spacing: 0.04em;
    line-height: 1.2;
    max-width: 680px;
    margin: 0 auto;
  }

  .section-heading em {
    font-style: italic;
    color: var(--or);
  }

  .section-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 28px auto;
  }
  .section-divider span {
    display: block; height: 1px; width: 60px;
    background: linear-gradient(to right, transparent, rgba(201,168,76,0.4));
  }
  .section-divider span.r { background: linear-gradient(to left, transparent, rgba(201,168,76,0.4)); }
  .section-divider i {
    width: 5px; height: 5px;
    background: var(--or);
    transform: rotate(45deg);
    display: block;
  }

  .section-desc {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-weight: 300;
    font-size: 1.15rem;
    color: #6a6a6a;
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.8;
    letter-spacing: 0.02em;
  }

  /* ════════════════════════════════════════════
     GRILLE SERVICES PRINCIPAUX
  ════════════════════════════════════════════ */
  .services-grid {
    padding: 20px 0 100px;
  }

  .service-card {
    position: relative;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(201,168,76,0.1);
    transition: box-shadow 0.4s ease, transform 0.4s ease;
    cursor: pointer;
  }

  .service-card:hover {
    box-shadow: 0 20px 60px rgba(26,58,42,0.1);
    transform: translateY(-4px);
  }

  /* Image du service */
  .service-img {
    position: relative;
    overflow: hidden;
    height: 280px;
  }

  .service-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.7s ease;
  }

  .service-card:hover .service-img img {
    transform: scale(1.06);
  }

  .service-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 40%, rgba(26,58,42,0.5) 100%);
    z-index: 1;
    transition: opacity 0.4s;
  }

  /* Numéro flottant */
  .service-num {
    position: absolute;
    top: 20px; right: 20px;
    z-index: 2;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1rem;
    color: rgba(255,255,255,0.6);
    letter-spacing: 0.1em;
    border: 1px solid rgba(255,255,255,0.25);
    width: 38px; height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Icône flottante */
  .service-icon {
    position: absolute;
    bottom: 20px; left: 24px;
    z-index: 2;
    font-size: 1.6rem;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
  }

  /* Corps de la carte */
  .service-body {
    padding: 32px 28px 28px;
    border-top: 2px solid transparent;
    transition: border-color 0.4s;
    background: #fff;
  }

  .service-card:hover .service-body {
    border-top-color: var(--or);
  }

  .service-name {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1.4rem;
    color: var(--vert);
    letter-spacing: 0.04em;
    margin-bottom: 12px;
  }

  .service-desc {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.78rem;
    color: #777;
    line-height: 1.9;
    letter-spacing: 0.03em;
    margin-bottom: 20px;
  }

  .service-link {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.58rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--or);
    text-decoration: none;
    position: relative;
    display: inline-block;
    transition: color 0.3s;
  }
  .service-link::after {
    content: '';
    position: absolute;
    bottom: -3px; left: 0;
    width: 0; height: 1px;
    background: var(--or);
    transition: width 0.4s;
  }
  .service-link:hover::after { width: 100%; }

  /* ════════════════════════════════════════════
     SECTION EXPÉRIENCE SIGNATURE — pleine largeur
  ════════════════════════════════════════════ */
  .signature-section {
    position: relative;
    height: 500px;
    overflow: hidden;
    display: flex;
    align-items: center;
  }

  .signature-bg {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(90deg, rgba(26,58,42,0.92) 0%, rgba(26,58,42,0.7) 50%, transparent 100%),
      url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=1920&q=85') center/cover no-repeat;
    z-index: 0;
  }

  .signature-content {
    position: relative;
    z-index: 2;
    padding: 0 80px;
    max-width: 620px;
  }

  .signature-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    letter-spacing: 0.65em;
    text-transform: uppercase;
    color: var(--or);
    margin-bottom: 20px;
    display: block;
  }

  .signature-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2rem, 4vw, 3.2rem);
    color: #fff;
    line-height: 1.2;
    margin-bottom: 24px;
  }

  .signature-title em { font-style: italic; color: var(--or-pale); }

  .signature-text {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.82rem;
    color: rgba(255,255,255,0.72);
    line-height: 2;
    letter-spacing: 0.04em;
    margin-bottom: 40px;
  }

  .btn-signature {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--noir);
    background: var(--or);
    padding: 15px 40px;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s, transform 0.25s;
  }
  .btn-signature:hover {
    background: var(--or-clair);
    color: var(--noir);
    transform: translateY(-2px);
  }

  /* ════════════════════════════════════════════
     PETITS SERVICES — liste élégante
  ════════════════════════════════════════════ */
  .extras-section {
    padding: 100px 0;
    background: #f9f7f2;
  }

  .extra-item {
    display: flex;
    align-items: flex-start;
    gap: 24px;
    padding: 32px 0;
    border-bottom: 1px solid rgba(201,168,76,0.12);
    transition: padding-left 0.3s;
  }
  .extra-item:first-child { border-top: 1px solid rgba(201,168,76,0.12); }
  .extra-item:hover { padding-left: 8px; }

  .extra-icon {
    font-size: 1.6rem;
    flex-shrink: 0;
    width: 48px;
    text-align: center;
    margin-top: 4px;
  }

  .extra-body {}

  .extra-name {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1.2rem;
    color: var(--vert);
    margin-bottom: 6px;
    letter-spacing: 0.03em;
  }

  .extra-desc {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.75rem;
    color: #888;
    line-height: 1.8;
    letter-spacing: 0.03em;
  }

  .extra-badge {
    margin-left: auto;
    flex-shrink: 0;
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--or);
    border: 1px solid rgba(201,168,76,0.3);
    padding: 5px 14px;
    white-space: nowrap;
    align-self: center;
  }

  /* ════════════════════════════════════════════
     CTA FINAL
  ════════════════════════════════════════════ */
  .cta-section {
    padding: 120px 0;
    text-align: center;
    background: #fff;
  }

  .cta-section .section-heading { margin-bottom: 16px; }

  .cta-section p {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-weight: 300;
    font-size: 1.1rem;
    color: #888;
    margin-bottom: 48px;
  }

  .cta-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    flex-wrap: wrap;
  }

  .btn-cta-main {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #fff;
    background: var(--vert);
    padding: 16px 48px;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s, transform 0.25s;
  }
  .btn-cta-main:hover {
    background: var(--vert-clair);
    color: #fff;
    transform: translateY(-2px);
  }

  .btn-cta-outline {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--vert);
    border: 1px solid rgba(26,58,42,0.3);
    padding: 15px 40px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
  }
  .btn-cta-outline:hover {
    border-color: var(--or);
    color: var(--or);
  }

  /* Animations entrée au scroll */
  .reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity 0.8s ease, transform 0.8s ease;
  }
  .reveal.visible {
    opacity: 1;
    transform: translateY(0);
  }

  @media (max-width: 991px) {
    .signature-content { padding: 0 40px; }
    .breadcrumb-bar { padding: 14px 24px; }
  }
  @media (max-width: 767px) {
    .service-img { height: 220px; }
    .signature-section { height: auto; padding: 80px 0; }
    .signature-bg {
      background:
        linear-gradient(180deg, rgba(26,58,42,0.88) 0%, rgba(26,58,42,0.75) 100%),
        url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=1200&q=85') center/cover no-repeat;
    }
    .extra-badge { display: none; }
  }
</style>

<!-- ══════════════════════════════════════════
     HERO DE PAGE
══════════════════════════════════════════ -->
<section class="page-hero">
  <div class="page-hero-content">
    <p class="page-eyebrow">Hôtel Seguro · Calavi</p>
    <h1 class="page-title">Nos <em>Services</em></h1>
    <div class="page-ornament">
      <span class="page-orn-line"></span>
      <span class="page-orn-dot"></span>
      <span class="page-orn-line r"></span>
    </div>
  </div>
</section>

<!-- Fil d'Ariane -->
<div class="breadcrumb-bar">
  <a href="../index.php">Accueil</a>
  <span class="sep">◆</span>
  <span class="current">Services</span>
</div>

<!-- ══════════════════════════════════════════
     INTRO
══════════════════════════════════════════ -->
<section class="section-intro reveal">
  <div class="container">
    <span class="section-label">L'art du séjour</span>
    <h2 class="section-heading">
      Chaque détail pensé<br>pour votre <em>confort absolu</em>
    </h2>
    <div class="section-divider">
      <span></span><i></i><span class="r"></span>
    </div>
    <p class="section-desc">
      À l'Hôtel Seguro, le service n'est pas une prestation —
      c'est une attention constante, discrète et sincère,
      qui transforme chaque moment en souvenir.
    </p>
  </div>
</section>

<!-- ══════════════════════════════════════════
     GRILLE SERVICES PRINCIPAUX
══════════════════════════════════════════ -->
<section class="services-grid">
  <div class="container">
    <div class="row g-4">

      <!-- Spa & Bien-être -->
      <div class="col-lg-4 col-md-6 reveal">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80" alt="Spa Seguro">
            <div class="service-img-overlay"></div>
            <span class="service-num">01</span>
            <span class="service-icon">🌿</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Spa &amp; Bien-être</h3>
            <p class="service-desc">
              Un sanctuaire de sérénité au cœur de la nature.
              Massages aux huiles botaniques locales, soins du visage,
              bain vapeur et hammam — pour un ressourcement total du corps et de l'esprit.
            </p>
            <a href="#spa" class="service-link">En savoir plus →</a>
          </div>
        </div>
      </div>

      <!-- Restaurant & Gastronomie -->
      <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.1s">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80" alt="Restaurant Seguro">
            <div class="service-img-overlay"></div>
            <span class="service-num">02</span>
            <span class="service-icon">🍽️</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Restaurant Gastronomique</h3>
            <p class="service-desc">
              Une cuisine fusion afro-méditerranéenne qui célèbre
              les saveurs du Bénin. Ingrédients locaux, produits frais
              du marché, chef exécutif primé — une expérience gustative unique.
            </p>
            <a href="#restaurant" class="service-link">Découvrir le menu →</a>
          </div>
        </div>
      </div>

      <!-- Piscine & Lounge -->
      <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.2s">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80" alt="Piscine Seguro">
            <div class="service-img-overlay"></div>
            <span class="service-num">03</span>
            <span class="service-icon">🏊</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Piscine &amp; Lounge</h3>
            <p class="service-desc">
              Une piscine à débordement face à la verdure tropicale,
              entourée de transats en teck, de parasols en raphia naturel
              et d'un bar à cocktails ouvert du matin au soir.
            </p>
            <a href="#piscine" class="service-link">Voir les espaces →</a>
          </div>
        </div>
      </div>

      <!-- Salle de Conférence -->
      <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.1s">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80" alt="Salle de conférence">
            <div class="service-img-overlay"></div>
            <span class="service-num">04</span>
            <span class="service-icon">💼</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Espaces Événementiels</h3>
            <p class="service-desc">
              Salles de réunion modulables, équipement audiovisuel dernière génération,
              connexion haut débit et équipe dédiée pour vos séminaires, conférences
              et événements d'entreprise.
            </p>
            <a href="#events" class="service-link">Demander un devis →</a>
          </div>
        </div>
      </div>

      <!-- Transfert & Navette -->
      <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.2s">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=800&q=80" alt="Transfert VIP">
            <div class="service-img-overlay"></div>
            <span class="service-num">05</span>
            <span class="service-icon">🚘</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Transfert &amp; Navette VIP</h3>
            <p class="service-desc">
              Service de transfert aéroport en véhicule climatisé avec chauffeur privé.
              Navettes régulières vers Cotonou. Location de véhicule avec chauffeur
              pour vos déplacements au Bénin.
            </p>
            <a href="#transfert" class="service-link">Réserver un transfert →</a>
          </div>
        </div>
      </div>

      <!-- Activités Nature -->
      <div class="col-lg-4 col-md-6 reveal" style="transition-delay: 0.3s">
        <div class="service-card h-100">
          <div class="service-img">
            <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=800&q=80" alt="Activités nature">
            <div class="service-img-overlay"></div>
            <span class="service-num">06</span>
            <span class="service-icon">🌳</span>
          </div>
          <div class="service-body">
            <h3 class="service-name">Activités &amp; Excursions</h3>
            <p class="service-desc">
              Découvrez le Bénin autrement : visites guidées de Cotonou,
              excursions sur le lac Nokoué, pirogue au village lacustre de Ganvié,
              safari photo dans les parcs naturels environnants.
            </p>
            <a href="#activites" class="service-link">Explorer les activités →</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════
     SECTION SIGNATURE PLEINE LARGEUR
══════════════════════════════════════════ -->
<section class="signature-section reveal">
  <div class="signature-bg"></div>
  <div class="signature-content">
    <span class="signature-label">Service Signature</span>
    <h2 class="signature-title">
      Le Conciergerie<br><em>Seguro</em>
    </h2>
    <p class="signature-text">
      Notre équipe de conciergerie est disponible 24h/24, 7j/7.
      Réservations de restaurant, billets d'avion, organisation d'événements privés,
      recommandations personnalisées — nous anticipons chacun de vos besoins
      avant même que vous ne les exprimiez.
    </p>
    <a href="../pages/contact.php" class="btn-signature">Contacter le Concierge</a>
  </div>
</section>

<!-- ══════════════════════════════════════════
     SERVICES ADDITIONNELS
══════════════════════════════════════════ -->
<section class="extras-section">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center mb-5 reveal">
        <span class="section-label">Inclus dans votre séjour</span>
        <h2 class="section-heading">Les <em>petites attentions</em><br>qui font la différence</h2>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">

        <div class="extra-item reveal">
          <span class="extra-icon">🛎️</span>
          <div class="extra-body">
            <h4 class="extra-name">Room Service 24h/24</h4>
            <p class="extra-desc">Menu complet disponible à toute heure dans votre chambre, servi avec élégance.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">☕</span>
          <div class="extra-body">
            <h4 class="extra-name">Petit-déjeuner Buffet</h4>
            <p class="extra-desc">Buffet tropical chaque matin : fruits frais, viennoiseries, œufs à la demande, jus naturels.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">🧹</span>
          <div class="extra-body">
            <h4 class="extra-name">Ménage Quotidien</h4>
            <p class="extra-desc">Service de chambre deux fois par jour, renouvellement du linge et des serviettes.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">📶</span>
          <div class="extra-body">
            <h4 class="extra-name">Wi-Fi Haut Débit</h4>
            <p class="extra-desc">Connexion fibre optique dans toutes les chambres et espaces communs de l'hôtel.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

      </div>

      <div class="col-lg-6">

        <div class="extra-item reveal">
          <span class="extra-icon">🚗</span>
          <div class="extra-body">
            <h4 class="extra-name">Parking Sécurisé</h4>
            <p class="extra-desc">Parking privé gardienné et éclairé, accès direct à l'hôtel via ascenseur dédié.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">🏋️</span>
          <div class="extra-body">
            <h4 class="extra-name">Salle de Sport</h4>
            <p class="extra-desc">Équipements cardio et musculation modernes, cours de yoga au lever du soleil sur demande.</p>
          </div>
          <span class="extra-badge">Inclus</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">👶</span>
          <div class="extra-body">
            <h4 class="extra-name">Baby-sitting</h4>
            <p class="extra-desc">Service de garde d'enfants certifié sur réservation, pour vos soirées en toute sérénité.</p>
          </div>
          <span class="extra-badge">Sur demande</span>
        </div>

        <div class="extra-item reveal">
          <span class="extra-icon">👔</span>
          <div class="extra-body">
            <h4 class="extra-name">Pressing &amp; Blanchisserie</h4>
            <p class="extra-desc">Service express en 4 heures ou standard 24h, livraison directement dans votre chambre.</p>
          </div>
          <span class="extra-badge">Sur demande</span>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════ -->
<section class="cta-section reveal">
  <div class="container">
    <span class="section-label">Prêt à vivre l'expérience ?</span>
    <h2 class="section-heading">
      Réservez votre séjour<br>à l'Hôtel <em>Seguro</em>
    </h2>
    <div class="section-divider">
      <span></span><i></i><span class="r"></span>
    </div>
    <p>Chaque réservation inclut l'accès à tous nos services.</p>
    <div class="cta-group">
      <a href="../pages/reservation.php" class="btn-cta-main">Réserver maintenant</a>
      <a href="../pages/contact.php" class="btn-cta-outline">Nous contacter</a>
    </div>
  </div>
</section>

<script>
  // Révélation au scroll
  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  reveals.forEach(el => observer.observe(el));
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>