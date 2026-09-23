<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/Chambre.php';
$database = new Database();
$db = $database->getConnection();
$chambreObj = new Chambre($db);
$chambres_db = $chambreObj->getAllAvailable();

// Stats dynamiques pour l'interlude
$stats_types = $chambreObj->countByType();
$total_chambres = count($chambres_db);
$superficie_max = 0;
$nb_types = count($stats_types);
foreach ($chambres_db as $ch) {
    if ($ch['superficie_m2'] > $superficie_max) $superficie_max = $ch['superficie_m2'];
}

include(__DIR__ . '/../layouts/header.php');
?>

<style>
  /* ════════════════════════════════════════════
     PAGE CHAMBRES — Hôtel Seguro
     Direction : Editorial luxe · Asymétrie · Espace
  ════════════════════════════════════════════ */

  /* ── Hero de page ── */
  .page-hero {
    position: relative;
    width: 100%;
    height: 55vh;
    min-height: 400px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    justify-content: flex-start;
  }

  .page-hero-bg {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(105deg, rgba(13,26,18,0.75) 0%, rgba(13,26,18,0.3) 55%, transparent 100%),
      url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1920&q=85') center/cover no-repeat;
    transform-origin: center;
    animation: slowZoom 14s ease-in-out infinite alternate;
  }

  @keyframes slowZoom {
    from { transform: scale(1); }
    to   { transform: scale(1.04); }
  }

  .page-hero-content {
    position: relative;
    z-index: 2;
    padding: 0 80px 60px;
  }

  .page-eyebrow {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.56rem;
    letter-spacing: 0.7em;
    text-transform: uppercase;
    color: var(--or);
    margin-bottom: 16px;
    display: block;
    animation: fadeUp 1.2s ease 0.2s both;
  }

  .page-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(3rem, 6.5vw, 6rem);
    color: #fff;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    line-height: 1;
    animation: fadeUp 1.2s ease 0.4s both;
  }

  .page-title em {
    font-style: italic;
    color: var(--or-pale);
  }

  /* Barre décorative sous le titre */
  .title-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 24px;
    animation: fadeUp 1.2s ease 0.6s both;
  }
  .title-bar-line {
    width: 80px; height: 1px;
    background: linear-gradient(to right, var(--or), transparent);
  }
  .title-bar-text {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.6rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.5);
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── Fil d'Ariane ── */
  .breadcrumb-bar {
    background: #f9f7f2;
    border-bottom: 1px solid rgba(201,168,76,0.12);
    padding: 14px 80px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .breadcrumb-bar a, .breadcrumb-bar span {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.6rem;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: #999;
    text-decoration: none;
    transition: color 0.3s;
  }
  .breadcrumb-bar a:hover { color: var(--or); }
  .breadcrumb-bar .sep { color: rgba(201,168,76,0.4); font-size: 0.45rem; }
  .breadcrumb-bar .current { color: var(--vert); }

  /* ════════════════════════════════════════════
     FILTRES
  ════════════════════════════════════════════ */
  .filter-bar {
    padding: 48px 80px 0;
    display: flex;
    align-items: center;
    gap: 0;
    border-bottom: 1px solid rgba(201,168,76,0.1);
  }

  .filter-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    letter-spacing: 0.5em;
    text-transform: uppercase;
    color: #aaa;
    margin-right: 32px;
    white-space: nowrap;
  }

  .filter-btn {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: #888;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 16px 24px;
    cursor: pointer;
    transition: color 0.3s, border-color 0.3s;
    white-space: nowrap;
  }
  .filter-btn:hover { color: var(--vert); }
  .filter-btn.active {
    color: var(--vert);
    border-bottom-color: var(--or);
  }

  /* ════════════════════════════════════════════
     INTRO ÉDITORIALE
  ════════════════════════════════════════════ */
  .chambres-intro {
    padding: 80px 80px 40px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 60px;
  }

  .intro-left {
    flex: 0 0 auto;
  }

  .section-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    letter-spacing: 0.6em;
    text-transform: uppercase;
    color: var(--or);
    margin-bottom: 16px;
    display: block;
  }

  .section-heading {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2rem, 3.5vw, 3rem);
    color: var(--vert);
    line-height: 1.15;
    letter-spacing: 0.03em;
  }
  .section-heading em { font-style: italic; color: var(--or); }

  .intro-right {
    max-width: 420px;
    flex-shrink: 0;
  }

  .intro-right p {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-weight: 300;
    font-size: 1.1rem;
    color: #888;
    line-height: 1.9;
    letter-spacing: 0.02em;
  }

  /* ════════════════════════════════════════════
     GRILLE CHAMBRES — Layout éditorial asymétrique
  ════════════════════════════════════════════ */
  .chambres-section {
    padding: 40px 80px 120px;
  }

  /* ── Carte chambre ── */
  .chambre-card {
    position: relative;
    background: #fff;
    overflow: hidden;
    cursor: pointer;
    transition: box-shadow 0.5s ease;
  }
  .chambre-card:hover {
    box-shadow: 0 30px 80px rgba(26,58,42,0.12);
  }

  /* Image wrapper */
  .chambre-img {
    position: relative;
    overflow: hidden;
  }

  .chambre-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  }

  .chambre-card:hover .chambre-img img {
    transform: scale(1.05);
  }

  /* Overlay au hover */
  .chambre-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg,
      transparent 30%,
      rgba(13,26,18,0.65) 100%
    );
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: 1;
    display: flex;
    align-items: flex-end;
    padding: 28px;
  }
  .chambre-card:hover .chambre-overlay { opacity: 1; }

  .overlay-cta {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.58rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #fff;
    border-bottom: 1px solid rgba(201,168,76,0.6);
    padding-bottom: 4px;
    text-decoration: none;
    transition: color 0.3s, border-color 0.3s;
  }
  .overlay-cta:hover { color: var(--or); border-color: var(--or); }

  /* Badge type de chambre */
  .chambre-badge {
    position: absolute;
    top: 20px; left: 20px;
    z-index: 3;
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.5rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #fff;
    background: rgba(26,58,42,0.7);
    backdrop-filter: blur(8px);
    padding: 6px 14px;
    border-left: 2px solid var(--or);
  }

  /* Prix flottant */
  .chambre-price-tag {
    position: absolute;
    bottom: 20px; right: 20px;
    z-index: 3;
    text-align: right;
  }
  .price-from {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.48rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    display: block;
    margin-bottom: 2px;
  }
  .price-amount {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1.5rem;
    color: var(--or-pale);
    letter-spacing: 0.05em;
    line-height: 1;
  }
  .price-night {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.45rem;
    letter-spacing: 0.3em;
    color: rgba(255,255,255,0.5);
    display: block;
  }

  /* Corps de la carte */
  .chambre-body {
    padding: 28px 28px 24px;
    border-top: 1px solid rgba(201,168,76,0.1);
    position: relative;
  }

  /* Accent or au hover */
  .chambre-body::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 0; height: 2px;
    background: var(--or);
    transition: width 0.5s ease;
  }
  .chambre-card:hover .chambre-body::before { width: 100%; }

  .chambre-num {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 0.7rem;
    letter-spacing: 0.3em;
    color: rgba(201,168,76,0.4);
    display: block;
    margin-bottom: 8px;
  }

  .chambre-name {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1.5rem;
    color: var(--vert);
    letter-spacing: 0.03em;
    margin-bottom: 10px;
    line-height: 1.2;
  }

  .chambre-desc {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.75rem;
    color: #888;
    line-height: 1.85;
    letter-spacing: 0.03em;
    margin-bottom: 20px;
  }

  /* Équipements */
  .chambre-amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
  }
  .amenity {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #999;
    border: 1px solid rgba(201,168,76,0.2);
    padding: 4px 12px;
    transition: color 0.3s, border-color 0.3s;
  }
  .chambre-card:hover .amenity {
    color: var(--vert);
    border-color: rgba(26,58,42,0.25);
  }

  /* Footer de carte */
  .chambre-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    border-top: 1px solid rgba(201,168,76,0.08);
  }

  .chambre-detail {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.58rem;
    letter-spacing: 0.3em;
    color: #bbb;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .chambre-detail span { color: var(--vert); font-weight: 300; }

  .btn-chambre {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.58rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--or);
    text-decoration: none;
    border: 1px solid rgba(201,168,76,0.3);
    padding: 9px 22px;
    transition: all 0.3s;
    display: inline-block;
  }
  .btn-chambre:hover {
    background: var(--or);
    color: var(--noir);
    border-color: var(--or);
  }

  /* ════════════════════════════════════════════
     LAYOUTS SPÉCIAUX — cartes en vedette
  ════════════════════════════════════════════ */

  /* Grande carte horizontale (suite) */
  .chambre-featured {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    min-height: 480px;
    margin-bottom: 32px;
  }

  .chambre-featured .chambre-img {
    height: 100%;
    min-height: 400px;
  }

  .chambre-featured .chambre-body {
    padding: 48px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-top: none;
    border-left: 1px solid rgba(201,168,76,0.1);
  }

  .chambre-featured .chambre-body::before {
    top: 0; left: 0; right: auto;
    width: 2px; height: 0;
    transition: height 0.5s ease;
  }
  .chambre-featured:hover .chambre-body::before { height: 100%; width: 2px; }

  .chambre-featured .chambre-desc {
    font-size: 0.82rem;
    line-height: 2;
  }

  /* Prix dans le body pour la featured */
  .chambre-featured .price-body {
    margin-bottom: 24px;
  }
  .price-body-from {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #bbb;
    display: block;
    margin-bottom: 4px;
  }
  .price-body-amount {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 2rem;
    color: var(--or);
  }
  .price-body-night {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    color: #bbb;
    letter-spacing: 0.3em;
    margin-left: 4px;
  }

  /* ════════════════════════════════════════════
     SECTION EXPÉRIENCE — interlude vert
  ════════════════════════════════════════════ */
  .interlude {
    background: var(--vert);
    padding: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
    margin: 0 80px 60px;
  }

  .interlude-text { flex: 1; }

  .interlude-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.6em;
    text-transform: uppercase;
    color: rgba(201,168,76,0.7);
    margin-bottom: 16px;
    display: block;
  }

  .interlude-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(1.8rem, 3vw, 2.6rem);
    color: #fff;
    line-height: 1.25;
  }
  .interlude-title em { font-style: italic; color: var(--or-pale); }

  .interlude-stats {
    display: flex;
    gap: 60px;
    flex-shrink: 0;
  }

  .stat-item { text-align: center; }

  .stat-num {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 3rem;
    color: var(--or);
    line-height: 1;
    display: block;
  }
  .stat-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.45);
    margin-top: 8px;
    display: block;
  }

  /* ════════════════════════════════════════════
     CTA RÉSERVATION FINAL
  ════════════════════════════════════════════ */
  .chambre-cta {
    padding: 100px 80px;
    background: #f9f7f2;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .chambre-cta::before {
    content: 'SEGURO';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    font-family: 'Cormorant Garamond', serif;
    font-size: 18vw;
    font-weight: 300;
    color: rgba(26,58,42,0.03);
    letter-spacing: 0.3em;
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
  }

  .chambre-cta .section-label { margin-bottom: 16px; }

  .chambre-cta-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2rem, 4vw, 3.2rem);
    color: var(--vert);
    line-height: 1.2;
    margin-bottom: 12px;
    position: relative;
  }
  .chambre-cta-title em { font-style: italic; color: var(--or); }

  .chambre-cta p {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 1.1rem;
    color: #aaa;
    margin-bottom: 48px;
    position: relative;
  }

  .cta-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    position: relative;
  }

  .btn-reserve-main {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #fff;
    background: var(--vert);
    padding: 17px 50px;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s, transform 0.25s;
  }
  .btn-reserve-main:hover {
    background: var(--vert-clair);
    color: #fff;
    transform: translateY(-2px);
  }

  .btn-reserve-outline {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--or);
    border: 1px solid rgba(201,168,76,0.4);
    padding: 16px 40px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
  }
  .btn-reserve-outline:hover {
    background: var(--or);
    color: var(--noir);
    border-color: var(--or);
  }

  /* ── Animations reveal ── */
  .reveal {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.9s ease, transform 0.9s ease;
  }
  .reveal.visible {
    opacity: 1;
    transform: translateY(0);
  }

  /* ── Responsive ── */
  @media (max-width: 1100px) {
    .chambres-intro, .chambres-section, .filter-bar, .breadcrumb-bar { padding-left: 40px; padding-right: 40px; }
    .chambre-featured { grid-template-columns: 1fr; }
    .chambre-featured .chambre-img { min-height: 320px; }
    .chambre-featured .chambre-body { border-left: none; border-top: 1px solid rgba(201,168,76,0.1); }
    .interlude { margin: 0 40px 60px; padding: 60px 40px; }
    .chambre-cta { padding: 80px 40px; }
  }

  @media (max-width: 767px) {
    .chambres-intro { flex-direction: column; gap: 24px; padding: 60px 24px 32px; }
    .intro-right { max-width: 100%; }
    .chambres-section { padding: 24px 24px 80px; }
    .filter-bar { padding: 32px 24px 0; overflow-x: auto; }
    .breadcrumb-bar { padding: 14px 24px; }
    .interlude { flex-direction: column; margin: 0; padding: 60px 24px; gap: 40px; }
    .interlude-stats { justify-content: center; }
    .chambre-cta { padding: 60px 24px; }
    .page-hero-content { padding: 0 40px 40px; }
  }
</style>

<!-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ -->
<section class="page-hero">
  <div class="page-hero-bg"></div>
  <div class="page-hero-content">
    <span class="page-eyebrow">Hôtel Seguro · Lomé </span>
    <h1 class="page-title">Nos <em>Chambres</em><br>&amp; Suites</h1>
    <div class="title-bar">
      <span class="title-bar-line"></span>
      <span class="title-bar-text">8 types d'hébergement · Vue nature</span>
    </div>
  </div>
</section>

<!-- Fil d'Ariane -->
<div class="breadcrumb-bar">
  <a href="/acathon/index.php">Accueil</a>
  <span class="sep">◆</span>
  <span class="current">Chambres &amp; Suites</span>
</div>

<!-- ══════════════════════════════════════════
     FILTRES
══════════════════════════════════════════ -->
<div class="filter-bar">
  <span class="filter-label">Filtrer par</span>
  <button class="filter-btn active" onclick="filterChambres('all', this)">Tout</button>
  <button class="filter-btn" onclick="filterChambres('standard', this)">Standard</button>
  <button class="filter-btn" onclick="filterChambres('superieure', this)">Supérieure</button>
  <button class="filter-btn" onclick="filterChambres('suite', this)">Suite</button>
  <button class="filter-btn" onclick="filterChambres('villa', this)">Villa</button>
</div>

<!-- ══════════════════════════════════════════
     INTRO ÉDITORIALE
══════════════════════════════════════════ -->
<div class="chambres-intro reveal">
  <div class="intro-left">
    <span class="section-label">L'art du repos</span>
    <h2 class="section-heading">
      Chaque chambre,<br>un <em>univers</em> à part entière
    </h2>
  </div>
  <div class="intro-right">
    <p>
      Conçues à l'intersection du design contemporain et des traditions
      artisanales béninoises, nos chambres et suites offrent un refuge
      de sérénité absolue. Matières naturelles, lumière tamisée,
      vue sur la nature — le luxe dans sa forme la plus authentique.
    </p>
  </div>
</div>

<!-- ══════════════════════════════════════════
     CHAMBRES — Données depuis la base
══════════════════════════════════════════ -->
<section class="chambres-section">
<?php if (empty($chambres_db)): ?>
<div style="text-align:center;padding:80px;color:#aaa;font-family:'Cormorant Garamond',serif;font-size:1.2rem;">
    Aucune chambre disponible pour le moment.
</div>
<?php else: ?>
<?php
$featured = null;
$autres = [];
foreach ($chambres_db as $ch) {
    $amenities = json_decode($ch['amenities'] ?? '[]', true) ?: [];
    $ch['_amenities'] = $amenities;
    if (!$featured && $ch['prix_nuit'] == max(array_column($chambres_db,'prix_nuit'))) {
        $featured = $ch;
    } else {
        $autres[] = $ch;
    }
}
if (!$featured && !empty($chambres_db)) { $featured = $chambres_db[0]; $autres = array_slice($chambres_db,1); }
$TYPES = ['standard'=>'Standard','superieure'=>'Supérieure','suite'=>'Suite','villa'=>'Villa'];
?>

<?php if ($featured): ?>
  <!-- Chambre vedette -->
  <div class="chambre-card chambre-featured reveal" data-type="<?= $featured['type'] ?>">
    <div class="chambre-img">
      <?php if($featured['image_principale']): ?>
      <img src="<?= htmlspecialchars($featured['image_principale']) ?>" alt="<?= htmlspecialchars($featured['nom']) ?>">
      <?php else: ?>
      <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a3a2a,#2d5c40);display:flex;align-items:center;justify-content:center;font-size:5rem;color:rgba(201,168,76,.3);">🏨</div>
      <?php endif; ?>
      <div class="chambre-overlay">
        <a href="/acathon/pages/reservation-system.php" class="overlay-cta">Réserver cette chambre →</a>
      </div>
      <span class="chambre-badge"><?= $TYPES[$featured['type']]??ucfirst($featured['type']) ?></span>
    </div>
    <div class="chambre-body">
      <span class="chambre-num">— Chambre N° <?= $featured['numero'] ?></span>
      <h3 class="chambre-name"><?= htmlspecialchars($featured['nom']) ?></h3>
      <p class="chambre-desc"><?= htmlspecialchars($featured['description'] ?? '') ?></p>
      <div class="chambre-amenities">
        <?php foreach ($featured['_amenities'] as $a): ?>
        <span class="amenity"><?= htmlspecialchars($a) ?></span>
        <?php endforeach; ?>
      </div>
      <div class="price-body">
        <span class="price-body-from">À partir de</span>
        <span class="price-body-amount"><?= number_format($featured['prix_nuit'],0,',',' ') ?> <small style="font-size:1.2rem">FCFA</small></span>
        <span class="price-body-night">/ nuit</span>
      </div>
      <div class="chambre-footer">
        <div class="chambre-detail">
          <span><?= $featured['capacite_max'] ?></span> personnes &nbsp;·&nbsp; <?= $featured['superficie_m2'] ?> m² &nbsp;·&nbsp; Étage <span><?= $featured['etage'] ?></span>
        </div>
        <a href="/acathon/pages/reservation-system.php" class="btn-chambre">Réserver</a>
      </div>
    </div>
  </div>
<?php endif; ?>

  <!-- Grille autres chambres -->
  <div class="row g-4 mt-2">
  <?php foreach ($autres as $i => $ch):
    $delay = ($i % 3) * 0.05;
    $col = ($ch['type'] === 'villa') ? 'col-lg-8 col-md-12' : 'col-lg-4 col-md-6';
  ?>
    <div class="<?= $col ?> reveal" data-type="<?= $ch['type'] ?>" style="transition-delay:<?= $delay ?>s">
      <div class="chambre-card h-100">
        <div class="chambre-img" style="height:260px">
          <?php if($ch['image_principale']): ?>
          <img src="<?= htmlspecialchars($ch['image_principale']) ?>" alt="<?= htmlspecialchars($ch['nom']) ?>">
          <?php else: ?>
          <div style="width:100%;height:100%;background:linear-gradient(135deg,#e8f0ea,#d4e4d8);display:flex;align-items:center;justify-content:center;font-size:3rem;color:rgba(26,58,42,.2);">🛏️</div>
          <?php endif; ?>
          <div class="chambre-overlay">
            <a href="/acathon/pages/reservation-system.php" class="overlay-cta">Réserver →</a>
          </div>
          <span class="chambre-badge"><?= $TYPES[$ch['type']]??ucfirst($ch['type']) ?></span>
          <div class="chambre-price-tag">
            <span class="price-from">Dès</span>
            <span class="price-amount"><?= number_format($ch['prix_nuit'],0,',',' ') ?></span>
            <span class="price-night">FCFA / nuit</span>
          </div>
        </div>
        <div class="chambre-body">
          <span class="chambre-num">— N° <?= $ch['numero'] ?></span>
          <h3 class="chambre-name"><?= htmlspecialchars($ch['nom']) ?></h3>
          <p class="chambre-desc"><?= htmlspecialchars($ch['description'] ?? '') ?></p>
          <div class="chambre-amenities">
            <?php foreach (array_slice($ch['_amenities'],0,4) as $a): ?>
            <span class="amenity"><?= htmlspecialchars($a) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="chambre-footer">
            <div class="chambre-detail">
              <span><?= $ch['capacite_max'] ?></span> pers. · <span><?= $ch['superficie_m2'] ?> m²</span>
            </div>
            <a href="/acathon/pages/reservation-system.php" class="btn-chambre">Réserver</a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>


<?php endif; ?>
</section>

<!-- ══════════════════════════════════════════
     INTERLUDE STATS
══════════════════════════════════════════ -->
<div class="interlude reveal">
  <div class="interlude-text">
    <span class="interlude-label">L'Hôtel Seguro en chiffres</span>
    <h3 class="interlude-title">
      Un cadre pensé pour<br>votre <em>confort total</em>
    </h3>
  </div>
  <div class="interlude-stats">
    <div class="stat-item">
      <span class="stat-num"><?= $total_chambres ?></span>
      <span class="stat-label">Chambres<br>&amp; Suites</span>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= $nb_types ?></span>
      <span class="stat-label">Types<br>d'hébergement</span>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= $superficie_max ?></span>
      <span class="stat-label">m² max<br>Villa</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">24/7</span>
      <span class="stat-label">Service<br>en chambre</span>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════ -->
<section class="chambre-cta reveal">
  <span class="section-label">Votre séjour vous attend</span>
  <h2 class="chambre-cta-title">
    Réservez la chambre<br>de vos <em>rêves</em>
  </h2>
  <p>Disponibilités en temps réel · Meilleur tarif garanti</p>
  <div class="cta-group">
    <a href="/acathon/pages/reservation-system.php" class="btn-reserve-main">Vérifier les disponibilités</a>
    <a href="/acathon/pages/contact.php" class="btn-reserve-outline">Nous contacter</a>
  </div>
</section>

<script>
  // ── Reveal au scroll ──────────────────────
  const reveals = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  reveals.forEach(el => obs.observe(el));

  // ── Filtre par type ───────────────────────
  function filterChambres(type, btn) {
    // Boutons actifs
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Cartes
    document.querySelectorAll('[data-type]').forEach(card => {
      const parent = card.closest('.col-lg-4, .col-lg-8, .col-md-6, .col-md-12, .chambre-featured') || card;
      if (type === 'all' || card.dataset.type === type) {
        parent.style.display = '';
        setTimeout(() => parent.style.opacity = '1', 10);
      } else {
        parent.style.opacity = '0';
        setTimeout(() => parent.style.display = 'none', 300);
      }
    });
  }
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>