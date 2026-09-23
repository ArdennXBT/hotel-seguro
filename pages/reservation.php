<?php include(__DIR__ . '/../layouts/header.php'); ?>

<style>
  /* ════════════════════════════════════════════
     PAGE RÉSERVATION — Hôtel Seguro
     Direction : Funnel multi-étapes · Luxe fonctionnel
     Split layout : formulaire gauche / résumé droite
  ════════════════════════════════════════════ */

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  @keyframes stepIn {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: translateX(0); }
  }
  @keyframes pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(201,168,76,0.3); }
    50%      { box-shadow: 0 0 0 8px rgba(201,168,76,0); }
  }

  /* ════════════════════════════════════════════
     HERO RESERVATION — compact, sobre
  ════════════════════════════════════════════ */
  .resa-hero {
    position: relative;
    height: 42vh;
    min-height: 320px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
  }

  .resa-hero-bg {
    position: absolute; inset: 0;
    background:
      linear-gradient(180deg,
        rgba(13,26,18,0.25) 0%,
        rgba(13,26,18,0.65) 70%,
        rgba(13,26,18,0.88) 100%
      ),
      url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1920&q=85')
      center/cover no-repeat;
    animation: slowZoom 16s ease-in-out infinite alternate;
  }
  @keyframes slowZoom {
    from { transform: scale(1); }
    to   { transform: scale(1.04); }
  }

  .resa-hero-content {
    position: relative; z-index: 2;
    padding: 0 80px 48px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 40px;
  }

  .resa-hero-left {}
  .resa-eyebrow {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    letter-spacing: 0.7em;
    text-transform: uppercase;
    color: var(--or);
    display: block;
    margin-bottom: 12px;
    animation: fadeUp 1.2s ease 0.2s both;
  }
  .resa-hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2.2rem, 5vw, 4rem);
    color: #fff;
    line-height: 1.05;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    animation: fadeUp 1.2s ease 0.4s both;
  }
  .resa-hero-title em { font-style: italic; color: var(--or-pale); }

  /* Garanties hero */
  .resa-guarantees {
    display: flex;
    gap: 28px;
    align-items: center;
    flex-shrink: 0;
    animation: fadeUp 1.2s ease 0.6s both;
  }
  .guarantee-item {
    text-align: center;
    border-left: 1px solid rgba(201,168,76,0.2);
    padding-left: 20px;
  }
  .guarantee-item:first-child { border-left: none; padding-left: 0; }
  .guarantee-icon { font-size: 1.1rem; display: block; margin-bottom: 4px; }
  .guarantee-text {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.55);
    line-height: 1.5;
  }

  /* Fil d'Ariane */
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
     INDICATEUR D'ÉTAPES
  ════════════════════════════════════════════ */
  .steps-bar {
    background: #fff;
    border-bottom: 1px solid rgba(201,168,76,0.1);
    padding: 28px 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
  }

  .step-item {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: default;
  }

  .step-num {
    width: 36px; height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(201,168,76,0.25);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1rem;
    color: #ccc;
    transition: all 0.4s;
    flex-shrink: 0;
  }

  .step-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.58rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #ccc;
    transition: color 0.4s;
    white-space: nowrap;
  }

  .step-connector {
    width: 80px; height: 1px;
    background: rgba(201,168,76,0.15);
    margin: 0 16px;
    flex-shrink: 0;
  }

  /* Étape active */
  .step-item.active .step-num {
    background: var(--vert);
    border-color: var(--vert);
    color: #fff;
    animation: pulse 2s ease infinite;
  }
  .step-item.active .step-label { color: var(--vert); }

  /* Étape complétée */
  .step-item.done .step-num {
    background: var(--or);
    border-color: var(--or);
    color: var(--noir);
  }
  .step-item.done .step-label { color: var(--or); }

  /* ════════════════════════════════════════════
     LAYOUT PRINCIPAL — Formulaire + Résumé
  ════════════════════════════════════════════ */
  .resa-main {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    min-height: 700px;
    align-items: start;
  }

  /* ── Zone formulaire ── */
  .resa-form-area {
    padding: 64px 80px;
    background: #fff;
    border-right: 1px solid rgba(201,168,76,0.08);
  }

  /* ── Résumé de réservation ── */
  .resa-summary {
    padding: 64px 48px;
    background: #f9f7f2;
    position: sticky;
    top: 80px;
  }

  /* ════════════════════════════════════════════
     ÉTAPES DU FORMULAIRE
  ════════════════════════════════════════════ */
  .form-step {
    display: none;
    animation: stepIn 0.5s ease both;
  }
  .form-step.active { display: block; }

  .step-heading {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    color: var(--vert);
    line-height: 1.2;
    margin-bottom: 8px;
    letter-spacing: 0.03em;
  }
  .step-heading em { font-style: italic; color: var(--or); }

  .step-sub {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 1rem;
    color: #aaa;
    margin-bottom: 40px;
    letter-spacing: 0.03em;
  }

  .step-divider {
    display: flex; align-items: center; gap: 12px; margin-bottom: 40px;
  }
  .step-divider span {
    height: 1px; width: 40px;
    background: linear-gradient(to right, var(--or), transparent);
    display: block;
  }
  .step-divider i {
    width: 4px; height: 4px; background: var(--or);
    transform: rotate(45deg); display: block;
  }

  /* ── Champs formulaire ── */
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
    margin-bottom: 28px;
  }

  .form-group {
    position: relative;
    margin-bottom: 28px;
  }
  .form-row .form-group { margin-bottom: 0; }

  .form-group label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.45em;
    text-transform: uppercase;
    color: #bbb;
    display: block;
    margin-bottom: 10px;
    transition: color 0.3s;
  }
  .form-group:focus-within label { color: var(--vert); }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 1px solid rgba(201,168,76,0.2);
    color: #1a1a1a;
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.88rem;
    letter-spacing: 0.04em;
    padding: 10px 0;
    outline: none;
    transition: border-color 0.3s;
    border-radius: 0;
    -webkit-appearance: none;
  }
  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus { border-bottom-color: var(--vert); }
  .form-group input::placeholder,
  .form-group textarea::placeholder { color: rgba(26,58,42,0.2); font-style: italic; }
  .form-group select { cursor: pointer; color: #888; }
  .form-group select option { color: #1a1a1a; }
  .form-group::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 0; height: 1px;
    background: var(--vert);
    transition: width 0.4s ease;
  }
  .form-group:focus-within::after { width: 100%; }

  /* ── Sélection du type de chambre (étape 1) ── */
  .chambre-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 32px;
  }

  .chambre-option {
    position: relative;
    cursor: pointer;
  }
  .chambre-option input[type="radio"] {
    position: absolute; opacity: 0; width: 0; height: 0;
  }
  .chambre-option-card {
    border: 1px solid rgba(201,168,76,0.15);
    padding: 20px;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
  }
  .chambre-option:hover .chambre-option-card {
    border-color: rgba(201,168,76,0.4);
    background: rgba(201,168,76,0.02);
  }
  .chambre-option input:checked + .chambre-option-card {
    border-color: var(--vert);
    background: rgba(26,58,42,0.03);
  }
  .chambre-option input:checked + .chambre-option-card::before {
    content: '✓';
    position: absolute;
    top: 10px; right: 12px;
    font-size: 0.7rem;
    color: var(--vert);
    font-family: 'Jost', sans-serif;
  }

  .co-img {
    width: 100%; height: 100px;
    object-fit: cover;
    display: block;
    margin-bottom: 14px;
  }
  .co-name {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1rem;
    color: var(--vert);
    margin-bottom: 4px;
    letter-spacing: 0.03em;
  }
  .co-price {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    color: var(--or);
    letter-spacing: 0.2em;
  }
  .co-size {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    color: #bbb;
    letter-spacing: 0.2em;
    margin-top: 4px;
  }

  /* ── Compteur de personnes ── */
  .counter-group {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(201,168,76,0.12);
    margin-bottom: 16px;
  }
  .counter-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.7rem;
    color: #666;
    letter-spacing: 0.1em;
    flex: 1;
  }
  .counter-sub {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    color: #bbb;
    letter-spacing: 0.1em;
    display: block;
    margin-top: 2px;
  }
  .counter-controls {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .counter-btn {
    width: 30px; height: 30px;
    border: 1px solid rgba(201,168,76,0.3);
    background: none;
    color: var(--or);
    font-size: 1rem;
    line-height: 1;
    cursor: pointer;
    transition: all 0.25s;
    display: flex; align-items: center; justify-content: center;
  }
  .counter-btn:hover { background: var(--or); color: var(--noir); border-color: var(--or); }
  .counter-val {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1.2rem;
    color: var(--vert);
    width: 24px;
    text-align: center;
  }

  /* ── Services optionnels (étape 3) ── */
  .options-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 32px;
  }

  .option-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px;
    border: 1px solid rgba(201,168,76,0.12);
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
  }
  .option-item:hover { border-color: rgba(201,168,76,0.35); background: rgba(201,168,76,0.02); }
  .option-item input[type="checkbox"] { position: absolute; opacity: 0; }
  .option-item.checked {
    border-color: var(--vert);
    background: rgba(26,58,42,0.03);
  }

  .option-checkbox {
    width: 18px; height: 18px;
    border: 1px solid rgba(201,168,76,0.3);
    flex-shrink: 0;
    margin-top: 2px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.3s;
    font-size: 0.6rem;
    color: transparent;
  }
  .option-item.checked .option-checkbox {
    background: var(--vert);
    border-color: var(--vert);
    color: #fff;
  }
  .option-icon { font-size: 1.2rem; flex-shrink: 0; }
  .option-name {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 0.95rem;
    color: var(--vert);
    display: block;
    margin-bottom: 3px;
  }
  .option-desc {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    color: #aaa;
    letter-spacing: 0.03em;
    line-height: 1.6;
  }
  .option-price {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.6rem;
    color: var(--or);
    letter-spacing: 0.2em;
    display: block;
    margin-top: 4px;
  }

  /* ── Boutons navigation ── */
  .form-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 48px;
    padding-top: 28px;
    border-top: 1px solid rgba(201,168,76,0.1);
  }

  .btn-prev {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.6rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #aaa;
    background: none;
    border: 1px solid rgba(201,168,76,0.2);
    padding: 12px 28px;
    cursor: pointer;
    transition: all 0.3s;
  }
  .btn-prev:hover { color: var(--vert); border-color: var(--vert); }

  .btn-next {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #fff;
    background: var(--vert);
    border: none;
    padding: 16px 48px;
    cursor: pointer;
    transition: background 0.3s, transform 0.25s;
  }
  .btn-next:hover { background: var(--vert-clair); transform: translateY(-2px); }

  .btn-confirm {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--noir);
    background: var(--or);
    border: none;
    padding: 18px 56px;
    cursor: pointer;
    transition: background 0.3s, transform 0.25s;
  }
  .btn-confirm:hover { background: var(--or-clair); transform: translateY(-2px); }

  /* ════════════════════════════════════════════
     RÉSUMÉ DE RÉSERVATION (colonne droite)
  ════════════════════════════════════════════ */
  .summary-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.6em;
    text-transform: uppercase;
    color: var(--or);
    display: block;
    margin-bottom: 16px;
  }

  .summary-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1.8rem;
    color: var(--vert);
    margin-bottom: 28px;
    line-height: 1.2;
    letter-spacing: 0.03em;
  }
  .summary-title em { font-style: italic; color: var(--or); }

  /* Image de la chambre sélectionnée */
  .summary-chambre-img {
    width: 100%; height: 180px;
    object-fit: cover;
    display: block;
    margin-bottom: 20px;
  }
  .summary-chambre-img-placeholder {
    width: 100%; height: 180px;
    background: rgba(26,58,42,0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
  }
  .summary-chambre-img-placeholder span {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 1rem;
    color: #ccc;
  }

  /* Lignes du résumé */
  .summary-line {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid rgba(201,168,76,0.08);
    gap: 12px;
  }
  .summary-line:last-of-type { border-bottom: none; }

  .sl-key {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #aaa;
  }
  .sl-val {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1rem;
    color: var(--vert);
    text-align: right;
    letter-spacing: 0.02em;
  }

  .summary-divider {
    height: 1px;
    background: linear-gradient(to right, var(--or), transparent);
    margin: 20px 0;
    opacity: 0.3;
  }

  /* Total */
  .summary-total {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 16px 0;
    margin-top: 8px;
  }
  .total-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.6rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #888;
  }
  .total-amount {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 2rem;
    color: var(--or);
    line-height: 1;
  }
  .total-nights {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    color: #bbb;
    letter-spacing: 0.2em;
    display: block;
    margin-top: 4px;
    text-align: right;
  }

  /* Badges garanties */
  .summary-badges {
    margin-top: 28px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .guarantee-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    color: #888;
    letter-spacing: 0.06em;
  }
  .guarantee-badge span {
    font-size: 0.85rem;
  }

  /* Aide */
  .summary-help {
    margin-top: 32px;
    padding: 20px;
    background: rgba(26,58,42,0.04);
    border-left: 2px solid rgba(201,168,76,0.3);
  }
  .summary-help p {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.65rem;
    color: #888;
    line-height: 1.8;
    letter-spacing: 0.04em;
  }
  .summary-help a {
    color: var(--or);
    text-decoration: none;
  }
  .summary-help a:hover { text-decoration: underline; }

  /* ════════════════════════════════════════════
     CONFIRMATION (étape finale)
  ════════════════════════════════════════════ */
  .confirmation-screen {
    display: none;
    text-align: center;
    padding: 80px 80px;
    background: #fff;
    grid-column: 1 / -1;
    animation: fadeIn 0.8s ease;
  }
  .confirmation-screen.active { display: block; }

  .confirm-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    border: 1px solid var(--or);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 32px;
    font-size: 1.8rem;
  }

  .confirm-ref {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.55rem;
    letter-spacing: 0.6em;
    text-transform: uppercase;
    color: var(--or);
    display: block;
    margin-bottom: 16px;
  }

  .confirm-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(2rem, 4vw, 3.5rem);
    color: var(--vert);
    line-height: 1.2;
    margin-bottom: 20px;
  }
  .confirm-title em { font-style: italic; color: var(--or); }

  .confirm-text {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.8rem;
    color: #888;
    line-height: 2;
    max-width: 520px;
    margin: 0 auto 48px;
    letter-spacing: 0.04em;
  }

  .confirm-ref-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.8rem;
    color: var(--vert);
    letter-spacing: 0.2em;
    display: block;
    margin-bottom: 8px;
  }

  .confirm-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 48px;
  }
  .btn-confirm-action {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.62rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    padding: 15px 40px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
  }
  .btn-confirm-action.primary {
    background: var(--vert);
    color: #fff;
  }
  .btn-confirm-action.primary:hover { background: var(--vert-clair); color: #fff; transform: translateY(-2px); }
  .btn-confirm-action.outline {
    border: 1px solid rgba(201,168,76,0.3);
    color: var(--or);
  }
  .btn-confirm-action.outline:hover { background: var(--or); color: var(--noir); border-color: var(--or); }

  /* ── Reveal ── */
  .reveal { opacity: 0; transform: translateY(32px); transition: opacity 0.8s ease, transform 0.8s ease; }
  .reveal.visible { opacity: 1; transform: translateY(0); }

  /* ── Responsive ── */
  @media (max-width: 1100px) {
    .resa-main { grid-template-columns: 1fr; }
    .resa-summary { position: static; padding: 48px 40px; }
    .resa-form-area { padding: 48px 40px; border-right: none; border-bottom: 1px solid rgba(201,168,76,0.1); }
    .steps-bar { padding: 20px 40px; overflow-x: auto; justify-content: flex-start; }
    .resa-hero-content { padding: 0 40px 40px; }
    .breadcrumb-bar { padding: 14px 40px; }
  }
  @media (max-width: 767px) {
    .resa-hero-content { flex-direction: column; align-items: flex-start; gap: 24px; padding: 0 24px 36px; }
    .resa-guarantees { gap: 16px; }
    .chambre-selector { grid-template-columns: 1fr; }
    .options-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .resa-form-area, .resa-summary { padding: 40px 24px; }
    .steps-bar { padding: 16px 24px; gap: 0; }
    .step-connector { width: 30px; margin: 0 8px; }
    .step-label { display: none; }
    .confirmation-screen { padding: 60px 24px; }
    .breadcrumb-bar { padding: 14px 24px; }
  }
</style>

<!-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ -->
<section class="resa-hero">
  <div class="resa-hero-bg"></div>
  <div class="resa-hero-content">
    <div class="resa-hero-left">
      <span class="resa-eyebrow">Réservation en ligne</span>
      <h1 class="resa-hero-title">Votre séjour<br><em>Seguro</em></h1>
    </div>
    <div class="resa-guarantees">
      <div class="guarantee-item">
        <span class="guarantee-icon">🔒</span>
        <span class="guarantee-text">Paiement<br>sécurisé</span>
      </div>
      <div class="guarantee-item">
        <span class="guarantee-icon">✓</span>
        <span class="guarantee-text">Meilleur tarif<br>garanti</span>
      </div>
      <div class="guarantee-item">
        <span class="guarantee-icon">↩</span>
        <span class="guarantee-text">Annulation<br>gratuite 48h</span>
      </div>
    </div>
  </div>
</section>

<!-- Fil d'Ariane -->
<div class="breadcrumb-bar">
  <a href="/index.php">Accueil</a>
  <span class="sep">◆</span>
  <a href="/pages/chambres.php">Chambres</a>
  <span class="sep">◆</span>
  <span class="current">Réservation</span>
</div>

<!-- ══════════════════════════════════════════
     INDICATEUR ÉTAPES
══════════════════════════════════════════ -->
<div class="steps-bar" id="stepsBar">
  <div class="step-item active" id="step-ind-1">
    <div class="step-num">1</div>
    <span class="step-label">Votre séjour</span>
  </div>
  <div class="step-connector"></div>
  <div class="step-item" id="step-ind-2">
    <div class="step-num">2</div>
    <span class="step-label">Vos coordonnées</span>
  </div>
  <div class="step-connector"></div>
  <div class="step-item" id="step-ind-3">
    <div class="step-num">3</div>
    <span class="step-label">Options</span>
  </div>
  <div class="step-connector"></div>
  <div class="step-item" id="step-ind-4">
    <div class="step-num">4</div>
    <span class="step-label">Confirmation</span>
  </div>
</div>

<!-- ══════════════════════════════════════════
     LAYOUT PRINCIPAL
══════════════════════════════════════════ -->
<div class="resa-main" id="resaMain">

  <!-- ═══════════════════════
       FORMULAIRE MULTI-ÉTAPES
  ═══════════════════════════ -->
  <div class="resa-form-area">

    <!-- ── ÉTAPE 1 — Séjour ── -->
    <div class="form-step active" id="step1">
      <h2 class="step-heading">Votre <em>séjour</em></h2>
      <p class="step-sub">Dates, type de chambre et nombre de personnes.</p>
      <div class="step-divider"><span></span><i></i></div>

      <!-- Dates -->
      <div class="form-row">
        <div class="form-group">
          <label for="arrivee">Date d'arrivée</label>
          <input type="date" id="arrivee" name="arrivee" required>
        </div>
        <div class="form-group">
          <label for="depart">Date de départ</label>
          <input type="date" id="depart" name="depart" required>
        </div>
      </div>

      <!-- Compteurs personnes -->
      <div style="margin-bottom: 32px;">
        <div class="counter-group">
          <div class="counter-label">
            Adultes
            <span class="counter-sub">Âge 18 et plus</span>
          </div>
          <div class="counter-controls">
            <button class="counter-btn" onclick="adjustCount('adultes', -1)">−</button>
            <span class="counter-val" id="adultes-val">2</span>
            <button class="counter-btn" onclick="adjustCount('adultes', 1)">+</button>
          </div>
        </div>
        <div class="counter-group">
          <div class="counter-label">
            Enfants
            <span class="counter-sub">Âge 2–17 ans</span>
          </div>
          <div class="counter-controls">
            <button class="counter-btn" onclick="adjustCount('enfants', -1)">−</button>
            <span class="counter-val" id="enfants-val">0</span>
            <button class="counter-btn" onclick="adjustCount('enfants', 1)">+</button>
          </div>
        </div>
      </div>

      <!-- Sélection chambre -->
      <label style="font-family:'Jost',sans-serif;font-weight:200;font-size:0.52rem;letter-spacing:0.45em;text-transform:uppercase;color:#bbb;display:block;margin-bottom:16px;">
        Type de chambre
      </label>
      <div class="chambre-selector">

        <label class="chambre-option">
          <input type="radio" name="chambre" value="standard" onchange="updateSummary()">
          <div class="chambre-option-card">
            <img class="co-img" src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400&q=75" alt="Standard">
            <div class="co-name">Standard Confort</div>
            <div class="co-price">55 000 FCFA / nuit</div>
            <div class="co-size">28 m² · Double bed</div>
          </div>
        </label>

        <label class="chambre-option">
          <input type="radio" name="chambre" value="superieure" onchange="updateSummary()" checked>
          <div class="chambre-option-card">
            <img class="co-img" src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&q=75" alt="Supérieure">
            <div class="co-name">Supérieure Nature</div>
            <div class="co-price">95 000 FCFA / nuit</div>
            <div class="co-size">42 m² · Queen bed</div>
          </div>
        </label>

        <label class="chambre-option">
          <input type="radio" name="chambre" value="suite" onchange="updateSummary()">
          <div class="chambre-option-card">
            <img class="co-img" src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=400&q=75" alt="Suite">
            <div class="co-name">Suite Junior</div>
            <div class="co-price">145 000 FCFA / nuit</div>
            <div class="co-size">58 m² · King bed</div>
          </div>
        </label>

        <label class="chambre-option">
          <input type="radio" name="chambre" value="villa" onchange="updateSummary()">
          <div class="chambre-option-card">
            <img class="co-img" src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=75" alt="Villa">
            <div class="co-name">Villa Privée</div>
            <div class="co-price">420 000 FCFA / nuit</div>
            <div class="co-size">120 m² · Piscine privée</div>
          </div>
        </label>

      </div>

      <div class="form-nav">
        <div></div>
        <button class="btn-next" onclick="goToStep(2)">Continuer →</button>
      </div>
    </div>

    <!-- ── ÉTAPE 2 — Coordonnées ── -->
    <div class="form-step" id="step2">
      <h2 class="step-heading">Vos <em>coordonnées</em></h2>
      <p class="step-sub">Informations personnelles pour votre réservation.</p>
      <div class="step-divider"><span></span><i></i></div>

      <div class="form-row">
        <div class="form-group">
          <label for="civilite">Civilité</label>
          <select id="civilite" name="civilite">
            <option value="m">M.</option>
            <option value="mme">Mme</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label for="nationalite">Nationalité</label>
          <select id="nationalite" name="nationalite">
            <option value="bj">Béninoise</option>
            <option value="fr">Française</option>
            <option value="sn">Sénégalaise</option>
            <option value="ci">Ivoirienne</option>
            <option value="autre">Autre</option>
          </select>
        </div>
      </div>

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
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="jean@exemple.com" required>
        </div>
        <div class="form-group">
          <label for="tel">Téléphone</label>
          <input type="tel" id="tel" name="tel" placeholder="+229 00 00 00 00" required>
        </div>
      </div>

      <div class="form-group">
        <label for="pays">Pays de résidence</label>
        <select id="pays" name="pays">
          <option value="bj">Bénin</option>
          <option value="fr">France</option>
          <option value="sn">Sénégal</option>
          <option value="ci">Côte d'Ivoire</option>
          <option value="tg">Togo</option>
          <option value="gh">Ghana</option>
          <option value="autre">Autre pays</option>
        </select>
      </div>

      <div class="form-group">
        <label for="demandes">Demandes spéciales <span style="color:#ddd;letter-spacing:0">(facultatif)</span></label>
        <textarea id="demandes" name="demandes" placeholder="Lit bébé, vue préférentielle, régime alimentaire particulier…" style="min-height:80px;"></textarea>
      </div>

      <div class="form-nav">
        <button class="btn-prev" onclick="goToStep(1)">← Retour</button>
        <button class="btn-next" onclick="goToStep(3)">Continuer →</button>
      </div>
    </div>

    <!-- ── ÉTAPE 3 — Options ── -->
    <div class="form-step" id="step3">
      <h2 class="step-heading">Options &amp; <em>extras</em></h2>
      <p class="step-sub">Personnalisez votre séjour avec nos services.</p>
      <div class="step-divider"><span></span><i></i></div>

      <div class="options-grid">

        <div class="option-item" onclick="toggleOption(this, 25000)">
          <input type="checkbox" name="options" value="petit-dej">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">☕</span>
          <div>
            <span class="option-name">Petit-déjeuner</span>
            <span class="option-desc">Buffet tropical complet chaque matin pour toute la chambre.</span>
            <span class="option-price">+25 000 FCFA / nuit</span>
          </div>
        </div>

        <div class="option-item" onclick="toggleOption(this, 35000)">
          <input type="checkbox" name="options" value="transfert">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">🚘</span>
          <div>
            <span class="option-name">Transfert aéroport</span>
            <span class="option-desc">Véhicule climatisé avec chauffeur privé, aller ou aller-retour.</span>
            <span class="option-price">+35 000 FCFA / trajet</span>
          </div>
        </div>

        <div class="option-item" onclick="toggleOption(this, 60000)">
          <input type="checkbox" name="options" value="spa">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">🌿</span>
          <div>
            <span class="option-name">Soin Spa 60 min</span>
            <span class="option-desc">Massage aux huiles botaniques locales pour 2 personnes.</span>
            <span class="option-price">+60 000 FCFA / séance</span>
          </div>
        </div>

        <div class="option-item" onclick="toggleOption(this, 45000)">
          <input type="checkbox" name="options" value="dinner">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">🍽️</span>
          <div>
            <span class="option-name">Dîner romantique</span>
            <span class="option-desc">Table privative en terrasse, menu 4 services pour 2 personnes.</span>
            <span class="option-price">+45 000 FCFA</span>
          </div>
        </div>

        <div class="option-item" onclick="toggleOption(this, 20000)">
          <input type="checkbox" name="options" value="excursion">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">🛶</span>
          <div>
            <span class="option-name">Excursion Ganvié</span>
            <span class="option-desc">Pirogue guidée sur le lac Nokoué, village lacustre de Ganvié.</span>
            <span class="option-price">+20 000 FCFA / pers.</span>
          </div>
        </div>

        <div class="option-item" onclick="toggleOption(this, 15000)">
          <input type="checkbox" name="options" value="champagne">
          <div class="option-checkbox">✓</div>
          <span class="option-icon">🥂</span>
          <div>
            <span class="option-name">Accueil Champagne</span>
            <span class="option-desc">Bouteille de champagne et fruits de saison à l'arrivée.</span>
            <span class="option-price">+15 000 FCFA</span>
          </div>
        </div>

      </div>

      <div class="form-nav">
        <button class="btn-prev" onclick="goToStep(2)">← Retour</button>
        <button class="btn-next" onclick="goToStep(4)">Récapitulatif →</button>
      </div>
    </div>

    <!-- ── ÉTAPE 4 — Récapitulatif & Paiement ── -->
    <div class="form-step" id="step4">
      <h2 class="step-heading">Récapitulatif &amp; <em>paiement</em></h2>
      <p class="step-sub">Vérifiez votre réservation avant de confirmer.</p>
      <div class="step-divider"><span></span><i></i></div>

      <!-- Résumé textuel détaillé -->
      <div id="recap-details" style="margin-bottom:36px;">
        <!-- généré par JS -->
      </div>

      <!-- Mode de paiement -->
      <div style="margin-bottom:36px;">
        <label style="font-family:'Jost',sans-serif;font-weight:200;font-size:0.52rem;letter-spacing:0.45em;text-transform:uppercase;color:#bbb;display:block;margin-bottom:16px;">
          Mode de paiement
        </label>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:28px;">
          <label style="cursor:pointer;display:flex;align-items:center;gap:10px;padding:14px 20px;border:1px solid rgba(201,168,76,0.2);font-family:'Jost',sans-serif;font-size:0.7rem;font-weight:200;color:#666;letter-spacing:0.1em;">
            <input type="radio" name="paiement" value="carte" checked style="accent-color:var(--vert);"> 💳 Carte bancaire
          </label>
          <label style="cursor:pointer;display:flex;align-items:center;gap:10px;padding:14px 20px;border:1px solid rgba(201,168,76,0.2);font-family:'Jost',sans-serif;font-size:0.7rem;font-weight:200;color:#666;letter-spacing:0.1em;">
            <input type="radio" name="paiement" value="mobile" style="accent-color:var(--vert);"> 📱 Mobile Money
          </label>
          <label style="cursor:pointer;display:flex;align-items:center;gap:10px;padding:14px 20px;border:1px solid rgba(201,168,76,0.2);font-family:'Jost',sans-serif;font-size:0.7rem;font-weight:200;color:#666;letter-spacing:0.1em;">
            <input type="radio" name="paiement" value="hotel" style="accent-color:var(--vert);"> 🏨 Paiement à l'hôtel
          </label>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="card-num">Numéro de carte</label>
            <input type="text" id="card-num" placeholder="•••• •••• •••• ••••" maxlength="19">
          </div>
          <div class="form-group">
            <label for="card-name">Nom sur la carte</label>
            <input type="text" id="card-name" placeholder="JEAN DUPONT">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="card-exp">Expiration</label>
            <input type="text" id="card-exp" placeholder="MM/AA" maxlength="5">
          </div>
          <div class="form-group">
            <label for="card-cvv">CVV</label>
            <input type="text" id="card-cvv" placeholder="•••" maxlength="3">
          </div>
        </div>
      </div>

      <!-- CGV -->
      <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:32px;">
        <input type="checkbox" id="cgv" required style="margin-top:3px;accent-color:var(--vert);flex-shrink:0;">
        <label for="cgv" style="font-family:'Jost',sans-serif;font-weight:200;font-size:0.68rem;color:#aaa;letter-spacing:0.03em;line-height:1.7;cursor:pointer;">
          J'accepte les <a href="#" style="color:var(--or);text-decoration:none;">conditions générales de vente</a>
          et la <a href="#" style="color:var(--or);text-decoration:none;">politique d'annulation</a> de l'Hôtel Seguro.
        </label>
      </div>

      <div class="form-nav">
        <button class="btn-prev" onclick="goToStep(3)">← Retour</button>
        <button class="btn-confirm" onclick="confirmReservation()">Confirmer la réservation ✓</button>
      </div>
    </div>

  </div>

  <!-- ═══════════════════════
       RÉSUMÉ LATÉRAL
  ═══════════════════════════ -->
  <div class="resa-summary reveal" id="resaSummary">

    <span class="summary-label">Résumé</span>
    <h3 class="summary-title">Votre <em>séjour</em></h3>

    <div id="summary-img-wrap">
      <div class="summary-chambre-img-placeholder">
        <span>Sélectionnez une chambre</span>
      </div>
    </div>

    <div id="summary-lines">
      <div class="summary-line">
        <span class="sl-key">Chambre</span>
        <span class="sl-val" id="sum-chambre">Supérieure Nature</span>
      </div>
      <div class="summary-line">
        <span class="sl-key">Arrivée</span>
        <span class="sl-val" id="sum-arrivee">—</span>
      </div>
      <div class="summary-line">
        <span class="sl-key">Départ</span>
        <span class="sl-val" id="sum-depart">—</span>
      </div>
      <div class="summary-line">
        <span class="sl-key">Nuits</span>
        <span class="sl-val" id="sum-nuits">—</span>
      </div>
      <div class="summary-line">
        <span class="sl-key">Personnes</span>
        <span class="sl-val" id="sum-personnes">2 adultes</span>
      </div>
    </div>

    <div class="summary-divider"></div>

    <div id="summary-options-wrap" style="display:none;margin-bottom:16px;">
      <div class="summary-line" style="border-bottom:none;">
        <span class="sl-key">Options</span>
        <span class="sl-val" id="sum-options" style="font-size:0.85rem;color:#888;">—</span>
      </div>
    </div>

    <div class="summary-total">
      <div>
        <span class="total-label">Total estimé</span>
      </div>
      <div style="text-align:right;">
        <span class="total-amount" id="sum-total">95 000</span>
        <small style="font-family:'Cormorant Garamond',serif;font-size:1rem;color:var(--or);"> FCFA</small>
        <span class="total-nights" id="sum-total-detail">1 nuit · sans options</span>
      </div>
    </div>

    <div class="summary-badges">
      <div class="guarantee-badge"><span>✓</span> Meilleur tarif garanti</div>
      <div class="guarantee-badge"><span>↩</span> Annulation gratuite jusqu'à 48h avant</div>
      <div class="guarantee-badge"><span>🔒</span> Paiement 100% sécurisé</div>
    </div>

    <div class="summary-help">
      <p>Besoin d'aide ? Contactez notre équipe au<br>
        <a href="tel:+22900000000">+229 00 00 00 00</a> ou par
        <a href="mailto:reservations@hotelseguro.com">e-mail</a>.
      </p>
    </div>

  </div>

  <!-- ═══════════════════════
       ÉCRAN CONFIRMATION
  ═══════════════════════════ -->
  <div class="confirmation-screen" id="confirmationScreen">
    <div class="confirm-icon">✦</div>
    <span class="confirm-ref">Réservation confirmée</span>
    <h2 class="confirm-title">
      Bienvenue à<br>l'Hôtel <em>Seguro</em>
    </h2>
    <p class="confirm-text">
      Votre réservation a été enregistrée avec succès. Un e-mail de confirmation
      vous a été envoyé avec tous les détails de votre séjour.
      Notre équipe a hâte de vous accueillir.
    </p>
    <span class="confirm-ref-num" id="confirmRefNum">SEGURO-2025-0001</span>
    <div style="font-family:'Jost',sans-serif;font-weight:200;font-size:0.6rem;color:#bbb;letter-spacing:0.3em;">
      Numéro de réservation
    </div>
    <div class="confirm-actions">
      <a href="/index.php" class="btn-confirm-action primary">Retour à l'accueil</a>
      <a href="/pages/contact.php" class="btn-confirm-action outline">Contacter la conciergerie</a>
    </div>
  </div>

</div><!-- /resa-main -->

<script>
  // ════════════════════════════════════════════
  // DONNÉES CHAMBRES
  // ════════════════════════════════════════════
  const chambres = {
    standard:   { nom: 'Standard Confort',   prix: 55000,  img: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600&q=80' },
    superieure: { nom: 'Supérieure Nature',  prix: 95000,  img: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&q=80' },
    suite:      { nom: 'Suite Junior',       prix: 145000, img: 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600&q=80' },
    villa:      { nom: 'Villa Privée',       prix: 420000, img: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80' }
  };

  let optionsTotal = 0;
  const counts = { adultes: 2, enfants: 0 };

  // ── Dates par défaut ──────────────────────
  const today = new Date();
  const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate() + 1);
  const dayAfter = new Date(today); dayAfter.setDate(dayAfter.getDate() + 3);

  document.getElementById('arrivee').value = tomorrow.toISOString().split('T')[0];
  document.getElementById('depart').value  = dayAfter.toISOString().split('T')[0];
  document.getElementById('arrivee').min   = tomorrow.toISOString().split('T')[0];

  document.getElementById('arrivee').addEventListener('change', () => {
    const arr = new Date(document.getElementById('arrivee').value);
    const dep = new Date(document.getElementById('depart').value);
    if (dep <= arr) {
      const next = new Date(arr); next.setDate(next.getDate() + 1);
      document.getElementById('depart').value = next.toISOString().split('T')[0];
    }
    document.getElementById('depart').min = document.getElementById('arrivee').value;
    updateSummary();
  });
  document.getElementById('depart').addEventListener('change', updateSummary);

  // ── Compteurs personnes ───────────────────
  function adjustCount(type, delta) {
    const min = type === 'adultes' ? 1 : 0;
    const max = type === 'adultes' ? 6 : 4;
    counts[type] = Math.max(min, Math.min(max, counts[type] + delta));
    document.getElementById(type + '-val').textContent = counts[type];
    updateSummary();
  }

  // ── Options ───────────────────────────────
  function toggleOption(el, prix) {
    el.classList.toggle('checked');
    const cb = el.querySelector('input[type="checkbox"]');
    cb.checked = !cb.checked;
    optionsTotal = 0;
    document.querySelectorAll('.option-item.checked').forEach(item => {
      const p = parseInt(item.getAttribute('data-prix') || 0);
      optionsTotal += p;
    });
    el.setAttribute('data-prix', prix);
    // recalcul
    let ot = 0;
    document.querySelectorAll('.option-item').forEach(item => {
      if (item.classList.contains('checked')) {
        ot += parseInt(item.getAttribute('data-prix') || 0);
      }
    });
    optionsTotal = ot;
    updateSummary();
  }

  // ── Mise à jour résumé ────────────────────
  function updateSummary() {
    const chambreVal = document.querySelector('input[name="chambre"]:checked')?.value || 'superieure';
    const chambre = chambres[chambreVal];

    const arrDate = new Date(document.getElementById('arrivee').value);
    const depDate = new Date(document.getElementById('depart').value);
    const nuits   = Math.max(1, Math.round((depDate - arrDate) / (1000*60*60*24)));

    // Image
    const imgWrap = document.getElementById('summary-img-wrap');
    imgWrap.innerHTML = `<img class="summary-chambre-img" src="${chambre.img}" alt="${chambre.nom}">`;

    // Lignes
    document.getElementById('sum-chambre').textContent  = chambre.nom;
    document.getElementById('sum-arrivee').textContent  = arrDate.toLocaleDateString('fr-FR', {day:'numeric',month:'long',year:'numeric'});
    document.getElementById('sum-depart').textContent   = depDate.toLocaleDateString('fr-FR', {day:'numeric',month:'long',year:'numeric'});
    document.getElementById('sum-nuits').textContent    = nuits + ' nuit' + (nuits > 1 ? 's' : '');
    document.getElementById('sum-personnes').textContent = counts.adultes + ' adulte' + (counts.adultes > 1 ? 's' : '') + (counts.enfants > 0 ? `, ${counts.enfants} enfant${counts.enfants > 1 ? 's' : ''}` : '');

    // Options actives
    const optNames = [];
    document.querySelectorAll('.option-item.checked .option-name').forEach(n => optNames.push(n.textContent));
    const optWrap = document.getElementById('summary-options-wrap');
    if (optNames.length > 0) {
      optWrap.style.display = 'block';
      document.getElementById('sum-options').textContent = optNames.join(', ');
    } else {
      optWrap.style.display = 'none';
    }

    // Total
    const total = (chambre.prix * nuits) + optionsTotal;
    document.getElementById('sum-total').textContent = total.toLocaleString('fr-FR');
    document.getElementById('sum-total-detail').textContent =
      nuits + ' nuit' + (nuits > 1 ? 's' : '') + (optionsTotal > 0 ? ` + options` : '');
  }

  // ── Navigation étapes ─────────────────────
  function goToStep(n) {
    // Masquer toutes les étapes
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.step-item').forEach((s, i) => {
      s.classList.remove('active', 'done');
      if (i + 1 < n)  s.classList.add('done');
      if (i + 1 === n) s.classList.add('active');
    });
    // Afficher l'étape cible
    document.getElementById('step' + n).classList.add('active');

    // Scroll haut du formulaire
    document.querySelector('.resa-form-area').scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Préremplir le récap étape 4
    if (n === 4) buildRecap();

    updateSummary();
  }

  // ── Récap étape 4 ─────────────────────────
  function buildRecap() {
    const chambreVal = document.querySelector('input[name="chambre"]:checked')?.value || 'superieure';
    const chambre    = chambres[chambreVal];
    const arrDate    = new Date(document.getElementById('arrivee').value);
    const depDate    = new Date(document.getElementById('depart').value);
    const nuits      = Math.max(1, Math.round((depDate - arrDate) / (1000*60*60*24)));
    const prenom     = document.getElementById('prenom')?.value || '—';
    const nom        = document.getElementById('nom')?.value || '—';

    const optItems = [];
    document.querySelectorAll('.option-item.checked').forEach(item => {
      const name  = item.querySelector('.option-name').textContent;
      const price = item.getAttribute('data-prix');
      optItems.push(`<div class="summary-line"><span class="sl-key">${name}</span><span class="sl-val" style="font-size:0.9rem">${parseInt(price).toLocaleString('fr-FR')} FCFA</span></div>`);
    });

    const total = (chambre.prix * nuits) + optionsTotal;

    document.getElementById('recap-details').innerHTML = `
      <div style="background:#f9f7f2;padding:24px 28px;border-left:2px solid var(--or);">
        <div class="summary-line"><span class="sl-key">Hôte</span><span class="sl-val">${prenom} ${nom}</span></div>
        <div class="summary-line"><span class="sl-key">Chambre</span><span class="sl-val">${chambre.nom}</span></div>
        <div class="summary-line"><span class="sl-key">Arrivée</span><span class="sl-val">${arrDate.toLocaleDateString('fr-FR',{day:'numeric',month:'long',year:'numeric'})}</span></div>
        <div class="summary-line"><span class="sl-key">Départ</span><span class="sl-val">${depDate.toLocaleDateString('fr-FR',{day:'numeric',month:'long',year:'numeric'})}</span></div>
        <div class="summary-line"><span class="sl-key">Durée</span><span class="sl-val">${nuits} nuit${nuits>1?'s':''}</span></div>
        <div class="summary-line"><span class="sl-key">Personnes</span><span class="sl-val">${counts.adultes} adulte${counts.adultes>1?'s':''}${counts.enfants>0?`, ${counts.enfants} enfant${counts.enfants>1?'s':''}`:''}</span></div>
        ${optItems.join('')}
        <div style="height:1px;background:linear-gradient(to right,var(--or),transparent);opacity:0.3;margin:16px 0;"></div>
        <div class="summary-line" style="border:none;"><span class="sl-key" style="font-size:0.65rem;">Total</span><span class="sl-val" style="font-size:1.4rem;color:var(--or);">${total.toLocaleString('fr-FR')} FCFA</span></div>
      </div>
    `;
  }

  // ── Confirmation finale ───────────────────
  function confirmReservation() {
    if (!document.getElementById('cgv').checked) {
      alert('Veuillez accepter les conditions générales de vente.');
      return;
    }

    // Générer numéro de réservation
    const ref = 'SEGURO-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
    document.getElementById('confirmRefNum').textContent = ref;

    // Masquer le résumé, afficher la confirmation
    document.getElementById('resaSummary').style.display = 'none';
    document.querySelector('.resa-form-area').style.display = 'none';
    document.getElementById('stepsBar').style.display = 'none';
    document.getElementById('confirmationScreen').classList.add('active');
    document.getElementById('resaMain').style.gridTemplateColumns = '1fr';

    // Scroll
    document.getElementById('confirmationScreen').scrollIntoView({ behavior: 'smooth' });

    // Marquer toutes les étapes done
    document.querySelectorAll('.step-item').forEach(s => { s.classList.remove('active'); s.classList.add('done'); });
  }

  // ── Format carte bancaire ─────────────────
  document.getElementById('card-num').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').substring(0, 16);
    this.value = v.replace(/(.{4})/g, '$1 ').trim();
  });
  document.getElementById('card-exp').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
    this.value = v;
  });

  // ── Reveal au scroll ─────────────────────
  const reveals = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
  }, { threshold: 0.08 });
  reveals.forEach(el => obs.observe(el));

  // Init
  updateSummary();
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>