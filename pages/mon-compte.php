<?php
/**
 * MON COMPTE — Espace client Hôtel SEGURO
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/connexion.php');
    exit;
}

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Chambre.php';
require_once __DIR__ . '/../includes/Reservation.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$chambre = new Chambre($db);
$reservation = new Reservation($db);

$chambres_all = $chambre->getAllAvailable();
$user->getById($_SESSION['user_id']);
$reservations = $user->getReservations();

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_modifier'])) {
        $reservation_id     = $_POST['reservation_id'] ?? '';
        $chambre_id         = $_POST['chambre_id'] ?? '';
        $date_arrivee       = $_POST['date_arrivee'] ?? '';
        $date_depart        = $_POST['date_depart'] ?? '';
        $nb_adultes         = intval($_POST['nb_adultes'] ?? 1);
        $nb_enfants         = intval($_POST['nb_enfants'] ?? 0);
        $demandes_speciales = trim($_POST['demandes_speciales'] ?? '');

        if ($reservation->getById($reservation_id) && $reservation->belongsTo($user->id)) {
            if ($reservation->modify($date_arrivee, $date_depart, $nb_adultes, $nb_enfants, $demandes_speciales, $chambre_id)) {
                $succes = 'Votre réservation a été modifiée avec succès.';
                $reservations = $user->getReservations();
            } else {
                $erreur = 'Erreur lors de la modification de la réservation.';
            }
        } else {
            $erreur = 'Réservation introuvable.';
        }
    }

    if (isset($_POST['action_annuler'])) {
        $reservation_id = $_POST['reservation_id'] ?? '';
        if ($reservation->getById($reservation_id) && $reservation->belongsTo($user->id)) {
            if ($reservation->cancel()) {
                $succes = 'Votre réservation a été annulée.';
                $reservations = $user->getReservations();
            } else {
                $erreur = 'Erreur lors de l\'annulation.';
            }
        } else {
            $erreur = 'Réservation introuvable.';
        }
    }
}

include(__DIR__ . '/../layouts/header.php');
?>

<style>
  :root {
    --vert: #1a3a2a; --vert-clair: #2d5c40;
    --or: #c9a84c;   --or-pale: #f5e9c4;
    --noir: #111111;
  }

  .compte-container {
    max-width: 1100px;
    margin: 100px auto 80px;
    padding: 0 28px;
  }

  /* ── Header compte ── */
  .compte-header {
    background: linear-gradient(135deg, var(--vert), var(--vert-clair));
    border-radius: 4px;
    padding: 44px 48px;
    margin-bottom: 48px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
  }
  .compte-header::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
    pointer-events: none;
  }
  .compte-info { position: relative; z-index: 1; }

  .compte-nom {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 2.4rem;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: 0.04em;
  }
  .compte-email {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.65);
    letter-spacing: 0.06em;
    margin-bottom: 12px;
  }
  .compte-code {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(201,168,76,0.3);
    border-radius: 4px;
    padding: 8px 18px;
    display: inline-block;
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.78rem;
    letter-spacing: 0.2em;
    color: var(--or-pale);
  }

  /* ── Bouton déconnexion ── */
  .btn-deconnexion {
    position: relative; z-index: 2;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.6rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.65);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 10px 22px;
    text-decoration: none;
    transition: all 0.3s;
    white-space: nowrap;
    align-self: flex-start;
    margin-top: 8px;
  }
  .btn-deconnexion:hover {
    background: rgba(220,53,69,0.2);
    border-color: rgba(220,53,69,0.5);
    color: #ff8080;
  }
  .btn-deconnexion-icon {
    font-size: 0.9rem;
    line-height: 1;
  }

  /* ── Titres de section ── */
  .section-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 2rem;
    color: var(--vert);
    margin-bottom: 32px;
    padding-bottom: 16px;
    position: relative;
    letter-spacing: 0.04em;
  }
  .section-title::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 60px; height: 1px;
    background: var(--or);
  }

  /* ── Alertes ── */
  .alert {
    padding: 16px 20px;
    margin-bottom: 24px;
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.8rem;
    letter-spacing: 0.04em;
    border-left: 3px solid;
  }
  .alert-success { background: rgba(40,167,69,0.06); color: #1e7e34; border-left-color: #28a745; }
  .alert-error   { background: rgba(220,53,69,0.06); color: #c0392b; border-left-color: #dc3545; }

  /* ── Cartes réservation ── */
  .reservations-grid { display: grid; gap: 20px; }

  .reservation-card {
    background: #fff;
    border-left: 3px solid var(--or);
    padding: 32px 36px;
    box-shadow: 0 4px 20px rgba(26,58,42,0.06);
    transition: box-shadow 0.3s, transform 0.3s;
  }
  .reservation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 36px rgba(26,58,42,0.1);
  }

  .reservation-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 14px;
  }
  .reservation-ref {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1.2rem;
    color: var(--vert);
    letter-spacing: 0.04em;
  }
  .reservation-date-creation {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.62rem;
    color: #aaa;
    letter-spacing: 0.12em;
    margin-top: 4px;
  }

  /* Statuts */
  .reservation-statut {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.55rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 2px;
  }
  .statut-en_cours  { background: rgba(255,193,7,0.1);  color: #e0a800; border: 1px solid rgba(255,193,7,0.3); }
  .statut-validee   { background: rgba(40,167,69,0.08); color: #1e7e34; border: 1px solid rgba(40,167,69,0.25); }
  .statut-modifiee  { background: rgba(23,162,184,0.08);color: #117a8b; border: 1px solid rgba(23,162,184,0.25); }
  .statut-annulee   { background: rgba(220,53,69,0.08); color: #c0392b; border: 1px solid rgba(220,53,69,0.25); }
  .statut-terminee  { background: rgba(108,117,125,0.08);color: #5a6268; border: 1px solid rgba(108,117,125,0.2); }

  .reservation-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
  }
  .detail-item { padding: 14px 16px; background: #f9f7f2; }
  .detail-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 6px;
  }
  .detail-value {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 1rem;
    color: var(--vert);
  }

  .demandes-block {
    background: rgba(201,168,76,0.04);
    border-left: 2px solid rgba(201,168,76,0.25);
    padding: 12px 16px;
    margin-bottom: 20px;
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.72rem;
    color: #777;
    font-style: italic;
    letter-spacing: 0.04em;
  }
  .demandes-block strong {
    display: block;
    font-style: normal;
    font-weight: 300;
    font-size: 0.52rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #bbb;
    margin-bottom: 4px;
  }

  .reservation-actions { display: flex; gap: 10px; flex-wrap: wrap; }

  .btn-action {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.58rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    padding: 9px 20px;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
  }
  .btn-modifier { background: var(--or); color: #fff; }
  .btn-modifier:hover { background: #b8941f; transform: translateY(-1px); color: #fff; }
  .btn-annuler  { background: #dc3545; color: #fff; }
  .btn-annuler:hover { background: #c82333; transform: translateY(-1px); }
  .btn-detail   { background: transparent; color: var(--vert); border: 1px solid rgba(26,58,42,0.3); }
  .btn-detail:hover { background: var(--vert); color: #fff; }

  /* ── Empty state ── */
  .empty-state { text-align: center; padding: 60px 20px; }
  .empty-state-icon { font-size: 3rem; color: rgba(201,168,76,0.25); margin-bottom: 20px; }
  .empty-state-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1.8rem;
    color: var(--vert);
    margin-bottom: 12px;
  }
  .empty-state p { font-family: 'Jost', sans-serif; font-weight: 200; font-size: 0.78rem; color: #aaa; margin-bottom: 28px; }

  /* ── Modales ── */
  .modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .modal-content {
    background: #fff;
    padding: 44px 48px;
    max-width: 620px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
  }
  .modal-close {
    position: absolute; top: 20px; right: 20px;
    width: 32px; height: 32px;
    border: 1px solid rgba(201,168,76,0.2);
    background: transparent;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: #aaa;
    transition: all 0.3s;
  }
  .modal-close:hover { background: #dc3545; color: #fff; border-color: #dc3545; }
  .modal-title {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: 1.8rem;
    color: var(--vert);
    margin-bottom: 32px;
    letter-spacing: 0.04em;
  }
  .modal-title em { font-style: italic; color: var(--or); }

  .form-group { margin-bottom: 24px; }
  .form-label {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    font-size: 0.52rem;
    letter-spacing: 0.45em;
    text-transform: uppercase;
    color: #aaa;
    display: block;
    margin-bottom: 10px;
  }
  .form-control {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 1px solid rgba(201,168,76,0.25);
    color: #1a1a1a;
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    font-size: 0.85rem;
    padding: 10px 0;
    outline: none;
    transition: border-color 0.3s;
  }
  .form-control:focus { border-bottom-color: var(--vert); }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

  .modal-actions { display: flex; gap: 12px; margin-top: 32px; }

  @media (max-width: 768px) {
    .compte-container { margin: 80px auto 60px; padding: 0 16px; }
    .compte-header { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
    .compte-nom { font-size: 1.8rem; }
    .reservation-card { padding: 24px 20px; }
    .form-row { grid-template-columns: 1fr; }
    .modal-content { padding: 32px 24px; }
    .btn-deconnexion { align-self: auto; width: 100%; justify-content: center; }
  }
</style>

<div class="compte-container">

  <!-- ── Header compte ── -->
  <div class="compte-header">
    <div class="compte-info">
      <div class="compte-nom">
        <?= htmlspecialchars($user->prenom) ?> <?= htmlspecialchars($user->nom) ?>
      </div>
      <div class="compte-email"><?= htmlspecialchars($user->email) ?></div>
      <div class="compte-code">Code : <?= htmlspecialchars($user->code_client) ?></div>
    </div>

    <!-- BOUTON DÉCONNEXION -->
    <!-- <a href="/pages/deconnexion.php" class="btn-deconnexion">
      <span class="btn-deconnexion-icon">⎋</span>
      Se déconnecter
    </a> -->
  </div>

  <?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
  <?php endif; ?>

  <!-- ── Réservations ── -->
  <section>
    <h2 class="section-title">Mes réservations</h2>

    <?php if (empty($reservations)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">📅</div>
        <div class="empty-state-title">Aucune réservation</div>
        <p>Vous n'avez pas encore effectué de réservation à l'Hôtel SEGURO.</p>
        <a href="/pages/reservation.php" class="btn-action btn-modifier"
           style="padding:12px 32px;">
          Faire une réservation
        </a>
      </div>

    <?php else: ?>
      <div class="reservations-grid">
        <?php foreach ($reservations as $res): ?>
          <div class="reservation-card">

            <div class="reservation-header">
              <div>
                <div class="reservation-ref"><?= htmlspecialchars($res['reference']) ?></div>
                <div class="reservation-date-creation">
                  Créée le <?= date('d/m/Y', strtotime($res['created_at'])) ?>
                </div>
              </div>
              <span class="reservation-statut statut-<?= $res['statut'] ?>">
                <?= $reservation->getStatutLibelle($res['statut']) ?>
              </span>
            </div>

            <div class="reservation-details">
              <div class="detail-item">
                <div class="detail-label">Chambre</div>
                <div class="detail-value"><?= htmlspecialchars($res['chambre_nom']) ?></div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Dates</div>
                <div class="detail-value">
                  <?= date('d/m/Y', strtotime($res['date_arrivee'])) ?>
                  &rarr;
                  <?= date('d/m/Y', strtotime($res['date_depart'])) ?>
                </div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Personnes</div>
                <div class="detail-value">
                  <?= $res['nb_adultes'] ?> adulte(s)
                  <?php if ($res['nb_enfants'] > 0): ?>
                    + <?= $res['nb_enfants'] ?> enfant(s)
                  <?php endif; ?>
                </div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Total</div>
                <div class="detail-value">
                  <?= number_format($res['prix_total'], 0, ',', ' ') ?> FCFA
                </div>
              </div>
            </div>

            <?php if (!empty($res['demandes_speciales'])): ?>
              <div class="demandes-block">
                <strong>Demandes spéciales</strong>
                <?= htmlspecialchars($res['demandes_speciales']) ?>
              </div>
            <?php endif; ?>

            <div class="reservation-actions">
              <?php if (in_array($res['statut'], ['en_cours', 'validee', 'modifiee'])): ?>
                <button class="btn-action btn-modifier"
                        onclick="openModalModifier('<?= $res['id'] ?>')">
                  Modifier
                </button>
              <?php endif; ?>

              <?php if (!in_array($res['statut'], ['terminee', 'annulee'])): ?>
                <form method="post" style="display:inline;"
                      onsubmit="return confirm('Confirmer l\'annulation de cette réservation ?');">
                  <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                  <button type="submit" name="action_annuler" class="btn-action btn-annuler">
                    Annuler
                  </button>
                </form>
              <?php endif; ?>

              <button class="btn-action btn-detail"
                      onclick="showDetails('<?= $res['id'] ?>')">
                Voir détails
              </button>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

</div>

<!-- ── Modal Modifier ── -->
<div id="modalModifier" class="modal">
  <div class="modal-content">
    <button class="modal-close" onclick="closeModal('modalModifier')">×</button>
    <h3 class="modal-title">Modifier ma <em>réservation</em></h3>

    <form method="post" id="formModifier">
      <input type="hidden" name="reservation_id" id="edit_reservation_id">

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Date d'arrivée</label>
          <input type="date" name="date_arrivee" id="edit_date_arrivee" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Date de départ</label>
          <input type="date" name="date_depart" id="edit_date_depart" class="form-control" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Adultes</label>
          <select name="nb_adultes" id="edit_nb_adultes" class="form-control" required>
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> adulte<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Enfants</label>
          <select name="nb_enfants" id="edit_nb_enfants" class="form-control">
            <?php for ($i = 0; $i <= 3; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> enfant<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Demandes spéciales</label>
        <textarea name="demandes_speciales" id="edit_demandes" class="form-control" rows="3"></textarea>
      </div>

      <div class="modal-actions">
        <button type="submit" name="action_modifier" class="btn-action btn-modifier">
          Enregistrer
        </button>
        <button type="button" class="btn-action btn-detail"
                onclick="closeModal('modalModifier')">
          Annuler
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ── Modal Détails ── -->
<div id="modalDetails" class="modal">
  <div class="modal-content" style="max-width:680px;">
    <button class="modal-close" onclick="closeModal('modalDetails')">×</button>
    <h3 class="modal-title">Détails de la <em>réservation</em></h3>

    <form method="post">
      <input type="hidden" name="reservation_id" id="detail_reservation_id">

      <div style="background:#f9f7f2;padding:20px;margin-bottom:28px;border-left:2px solid var(--or);">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
          <div>
            <div class="detail-label">Référence</div>
            <div class="detail-value" id="detail_reference">—</div>
          </div>
          <div>
            <div class="detail-label">Statut</div>
            <div id="detail_statut">—</div>
          </div>
          <div>
            <div class="detail-label">Total</div>
            <div class="detail-value" style="color:var(--or);" id="detail_total">—</div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Chambre</label>
        <select name="chambre_id" id="detail_chambre_select" class="form-control" required>
          <?php foreach ($chambres_all as $ch): ?>
            <option value="<?= $ch['id'] ?>">
              <?= htmlspecialchars($ch['nom']) ?> —
              <?= number_format($ch['prix_nuit'], 0, ',', ' ') ?> FCFA/nuit
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Arrivée</label>
          <input type="date" name="date_arrivee" id="detail_date_arrivee" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Départ</label>
          <input type="date" name="date_depart" id="detail_date_depart" class="form-control" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Adultes</label>
          <select name="nb_adultes" id="detail_nb_adultes" class="form-control" required>
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> adulte<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Enfants</label>
          <select name="nb_enfants" id="detail_nb_enfants" class="form-control">
            <?php for ($i = 0; $i <= 3; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?> enfant<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Demandes spéciales</label>
        <textarea name="demandes_speciales" id="detail_demandes" class="form-control" rows="3"></textarea>
      </div>

      <div class="modal-actions">
        <button type="submit" name="action_modifier" class="btn-action btn-modifier">
          Enregistrer les modifications
        </button>
        <button type="button" class="btn-action btn-detail"
                onclick="closeModal('modalDetails')">
          Fermer
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const reservationsData = <?= json_encode($reservations) ?>;

function openModalModifier(id) {
  const r = reservationsData.find(x => x.id === id);
  if (!r) return;
  document.getElementById('edit_reservation_id').value = r.id;
  document.getElementById('edit_date_arrivee').value   = r.date_arrivee;
  document.getElementById('edit_date_depart').value    = r.date_depart;
  document.getElementById('edit_nb_adultes').value     = r.nb_adultes;
  document.getElementById('edit_nb_enfants').value     = r.nb_enfants;
  document.getElementById('edit_demandes').value       = r.demandes_speciales || '';
  document.getElementById('modalModifier').style.display = 'flex';
}

function showDetails(id) {
  const r = reservationsData.find(x => x.id === id);
  if (!r) return;
  document.getElementById('detail_reservation_id').value     = r.id;
  document.getElementById('detail_reference').textContent    = r.reference;
  document.getElementById('detail_statut').textContent       = r.statut;
  document.getElementById('detail_total').textContent        =
    new Intl.NumberFormat('fr-FR').format(r.prix_total) + ' FCFA';
  document.getElementById('detail_chambre_select').value     = r.chambre_id;
  document.getElementById('detail_date_arrivee').value       = r.date_arrivee;
  document.getElementById('detail_date_depart').value        = r.date_depart;
  document.getElementById('detail_nb_adultes').value         = r.nb_adultes;
  document.getElementById('detail_nb_enfants').value         = r.nb_enfants;
  document.getElementById('detail_demandes').value           = r.demandes_speciales || '';
  document.getElementById('modalDetails').style.display      = 'flex';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

document.querySelectorAll('.modal').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>