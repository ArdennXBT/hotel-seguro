<?php
/**
 * CALENDRIER DES DISPONIBILITÉS — Hôtel SEGURO
 */

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/Chambre.php';

$database = new Database();
$db       = $database->getConnection();
$chambre  = new Chambre($db);

$year      = intval($_GET['year']      ?? date('Y'));
$month     = intval($_GET['month']     ?? date('m'));
$chambre_id = $_GET['chambre_id']      ?? null;

if ($year  < 2024 || $year  > 2030) $year  = date('Y');
if ($month < 1    || $month > 12  ) $month = date('m');

$chambres_all        = $chambre->getAllAvailable();
$chambre_selectionnee = null;
if ($chambre_id) {
    $chambre_selectionnee = $chambre->getById($chambre_id);
}

// ── Récupérer les dates bloquées pour le mois ──────────────
// Source 1 : table indisponibilites (réservations validées via trigger + blocages manuels)
// Source 2 : réservations en_cours/modifiee (pas encore validées mais existantes)
$date_debut_mois = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$date_fin_mois   = date('Y-m-t', strtotime($date_debut_mois));

// Jours bloqués : [chambre_id => [date => ['statut'=>..., 'motif'=>...]]]
$jours_bloques = [];

// Source 1 — indisponibilites (validées)
$sql1 = "SELECT chambre_id, date_debut, date_fin, motif
          FROM indisponibilites
          WHERE date_fin >= ? AND date_debut <= ?";
$params1 = [$date_debut_mois, $date_fin_mois];
if ($chambre_id) { $sql1 .= " AND chambre_id = ?"; $params1[] = $chambre_id; }
$stmt1 = $db->prepare($sql1);
$stmt1->execute($params1);
foreach ($stmt1->fetchAll() as $row) {
    $d = new DateTime($row['date_debut']);
    $fin = new DateTime($row['date_fin']);
    while ($d <= $fin) {
        $key = $d->format('Y-m-d');
        $jours_bloques[$row['chambre_id']][$key] = ['statut' => 'validee', 'motif' => $row['motif']];
        $d->modify('+1 day');
    }
}

// Source 2 — réservations en cours (pas encore dans indisponibilites)
$sql2 = "SELECT r.chambre_id, r.date_arrivee, r.date_depart, r.reference, r.statut, c.nom as chambre_nom
          FROM reservations r
          JOIN chambres c ON c.id = r.chambre_id
          WHERE r.statut IN ('en_cours','modifiee')
          AND r.date_depart >= ? AND r.date_arrivee <= ?";
$params2 = [$date_debut_mois, $date_fin_mois];
if ($chambre_id) { $sql2 .= " AND r.chambre_id = ?"; $params2[] = $chambre_id; }
$stmt2 = $db->prepare($sql2);
$stmt2->execute($params2);
foreach ($stmt2->fetchAll() as $row) {
    $d = new DateTime($row['date_arrivee']);
    $fin = new DateTime($row['date_depart']);
    while ($d <= $fin) {
        $key = $d->format('Y-m-d');
        // Ne pas écraser une réservation validée
        if (!isset($jours_bloques[$row['chambre_id']][$key])) {
            $jours_bloques[$row['chambre_id']][$key] = ['statut' => 'en_cours', 'motif' => 'Résa #'.$row['reference'].' (en attente)'];
        }
        $d->modify('+1 day');
    }
}

// ── Pour chaque jour du mois, calculer le statut global ───
// (si aucune chambre sélectionnée : partiel si au moins 1 chambre bloquée, indispo si toutes bloquées)
$nb_chambres = count($chambres_all);
$statuts_par_jour = []; // date => ['statut'=>..., 'chambres_bloquees'=>N, 'motifs'=>[]]

$d = new DateTime($date_debut_mois);
$fin_mois = new DateTime($date_fin_mois);
while ($d <= $fin_mois) {
    $key = $d->format('Y-m-d');
    $chambres_bloquees = 0;
    $motifs = [];
    $has_validee = false;
    $has_en_cours = false;

    foreach ($chambres_all as $ch) {
        if (isset($jours_bloques[$ch['id']][$key])) {
            $chambres_bloquees++;
            $info = $jours_bloques[$ch['id']][$key];
            $motifs[] = ($chambre_id ? '' : $ch['nom'].' : ') . $info['motif'];
            if ($info['statut'] === 'validee') $has_validee = true;
            else $has_en_cours = true;
        }
    }

    if ($chambres_bloquees === 0) {
        $statut = 'disponible';
    } elseif ($chambre_id) {
        // Chambre spécifique : soit bloquée soit dispo
        $statut = $has_validee ? 'indisponible' : 'en_attente';
    } else {
        // Toutes chambres : partiel si certaines dispo, indispo si toutes bloquées
        $statut = ($chambres_bloquees >= $nb_chambres) ? 'indisponible' : 'partiellement';
    }

    $statuts_par_jour[$key] = [
        'statut'   => $statut,
        'nb'       => $chambres_bloquees,
        'motifs'   => $motifs,
        'validee'  => $has_validee,
        'en_cours' => $has_en_cours,
    ];
    $d->modify('+1 day');
}

include(__DIR__ . '/../layouts/header.php');
?>

<style>
.calendrier-container{max-width:1200px;margin:80px auto 60px;padding:0 20px;}
.calendrier-title{font-family:'Cormorant Garamond',serif;font-size:2.5rem;color:var(--vert);margin-bottom:8px;text-align:center;}
.calendrier-subtitle{color:#888;font-size:1rem;margin-bottom:40px;text-align:center;}
.filtres-section{background:#fff;border-radius:12px;padding:24px;margin-bottom:28px;box-shadow:0 4px 20px rgba(0,0,0,.05);}
.filtres-row{display:flex;gap:20px;align-items:center;flex-wrap:wrap;}
.filtre-group{display:flex;align-items:center;gap:8px;}
.filtre-label{font-family:'Jost',sans-serif;font-weight:500;color:var(--vert);font-size:.9rem;}
.filtre-select{padding:8px 12px;border:1px solid rgba(201,168,76,.25);border-radius:6px;font-family:'Jost',sans-serif;min-width:200px;cursor:pointer;}
.filtre-select:focus{outline:none;border-color:var(--or);}
.nav-mois{display:flex;justify-content:center;align-items:center;gap:40px;margin-bottom:28px;}
.btn-nav{background:var(--vert);color:#fff;border:none;padding:10px 22px;border-radius:8px;cursor:pointer;font-family:'Jost',sans-serif;font-weight:500;transition:all .3s;}
.btn-nav:hover{background:var(--vert-clair);transform:translateY(-1px);}
.mois-label{font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:var(--vert);text-transform:capitalize;}
.cal-wrap{background:#fff;border-radius:16px;padding:32px;box-shadow:0 8px 40px rgba(0,0,0,.08);}
.cal-table{width:100%;border-collapse:collapse;}
.cal-table thead th{font-family:'Jost',sans-serif;font-weight:600;color:var(--vert);text-align:center;padding:14px 8px;font-size:.85rem;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid var(--or);}
.cal-day{height:90px;border:1px solid rgba(201,168,76,.1);vertical-align:top;position:relative;cursor:default;transition:background .2s;}
.cal-day:hover{background:rgba(201,168,76,.04);}
.day-num{font-family:'Jost',sans-serif;font-weight:500;font-size:.88rem;color:#333;padding:8px;display:inline-block;}
/* Statuts */
.cal-day.passe{opacity:.45;}
.cal-day.passe .day-num{color:#bbb;}
.cal-day.disponible{background:rgba(40,167,69,.05);}
.cal-day.disponible .day-num{color:#28a745;}
.cal-day.indisponible{background:rgba(220,53,69,.12);}
.cal-day.indisponible .day-num{color:#dc3545;}
.cal-day.en_attente{background:rgba(255,193,7,.12);}
.cal-day.en_attente .day-num{color:#856404;}
.cal-day.partiellement{background:rgba(255,160,0,.08);}
.cal-day.partiellement .day-num{color:#e67e00;}
.cal-day.hors-mois{opacity:.2;}
.cal-day.aujourd-hui .day-num{background:var(--or);color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;margin:4px;}
/* Barre de statut en bas de la cellule */
.day-barre{position:absolute;bottom:0;left:0;right:0;height:4px;}
.barre-disponible{background:rgba(40,167,69,.5);}
.barre-indisponible{background:rgba(220,53,69,.6);}
.barre-en_attente{background:rgba(255,193,7,.7);}
.barre-partiellement{background:linear-gradient(to right,rgba(40,167,69,.5),rgba(220,53,69,.5));}
/* Tooltip au hover */
.day-tooltip{display:none;position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:#1a3a2a;color:#fff;font-size:.72rem;padding:6px 10px;border-radius:6px;white-space:nowrap;z-index:100;max-width:200px;white-space:normal;text-align:center;line-height:1.4;}
.cal-day:hover .day-tooltip{display:block;}
/* Légende */
.legende{display:flex;justify-content:center;gap:28px;margin-top:24px;flex-wrap:wrap;}
.legende-item{display:flex;align-items:center;gap:8px;font-family:'Jost',sans-serif;font-size:.82rem;color:#666;cursor:pointer;padding:5px 10px;border-radius:6px;transition:all .2s;}
.legende-item:hover{background:rgba(201,168,76,.1);transform:translateY(-1px);}
.legende-item.active{font-weight:700;background:rgba(201,168,76,.18);}
.legende-couleur{width:16px;height:16px;border-radius:4px;border:1px solid rgba(0,0,0,.1);}
/* Infos chambre */
.chambre-info-box{background:linear-gradient(135deg,rgba(26,58,42,.05),rgba(201,168,76,.05));border-radius:12px;padding:24px;margin-bottom:28px;}
.chambre-nom{font-family:'Cormorant Garamond',serif;font-size:1.5rem;color:var(--vert);margin-bottom:6px;}
.chambre-details{color:#666;font-size:.9rem;}
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px;}
.stat-box{background:#fff;border-radius:8px;padding:16px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.05);}
.stat-val{font-size:1.8rem;font-weight:600;color:var(--or);}
.stat-lbl{font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:.06em;margin-top:2px;}
/* Légende globale toutes chambres */
.info-banner{background:rgba(26,58,42,.05);border-left:3px solid var(--or);padding:12px 20px;border-radius:0 8px 8px 0;margin-bottom:20px;font-size:.88rem;color:#555;}
@media(max-width:768px){.filtres-row{flex-direction:column;align-items:stretch;}.filtre-select{width:100%;}.cal-day{height:60px;}.day-num{font-size:.75rem;padding:4px;}.legende{gap:12px;}.stats-grid{grid-template-columns:1fr;}}
</style>

<div class="calendrier-container">

    <h1 class="calendrier-title">Calendrier des disponibilités</h1>
    <p class="calendrier-subtitle">Consultez les disponibilités en temps réel — dates bloquées par les réservations</p>

    <!-- Filtres -->
    <div class="filtres-section">
        <div class="filtres-row">
            <div class="filtre-group">
                <label class="filtre-label">Chambre :</label>
                <select class="filtre-select" id="chambreSelect" onchange="changeChambre()">
                    <option value="">Toutes les chambres</option>
                    <?php foreach ($chambres_all as $ch): ?>
                    <option value="<?= $ch['id'] ?>" <?= $chambre_id===$ch['id']?'selected':'' ?>>
                        <?= htmlspecialchars($ch['nom']) ?> — <?= ucfirst($ch['type']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtre-group">
                <label class="filtre-label">Mois :</label>
                <select class="filtre-select" onchange="changeMonth(this.value)">
                    <?php
                    $moisFR=[1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
                             7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
                    for ($m=1;$m<=12;$m++):?>
                    <option value="<?= $m ?>" <?= $month===$m?'selected':'' ?>><?= $moisFR[$m] ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filtre-group">
                <label class="filtre-label">Année :</label>
                <select class="filtre-select" onchange="changeYear(this.value)">
                    <?php for($y=date('Y');$y<=date('Y')+2;$y++):?>
                    <option value="<?= $y ?>" <?= $year===$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor;?>
                </select>
            </div>
        </div>
    </div>

    <?php if ($chambre_selectionnee): ?>
    <!-- Infos chambre sélectionnée -->
    <div class="chambre-info-box">
        <div class="chambre-nom"><?= htmlspecialchars($chambre_selectionnee->nom) ?></div>
        <div class="chambre-details">
            <?= ucfirst($chambre_selectionnee->type) ?> &nbsp;·&nbsp;
            <?= $chambre_selectionnee->superficie_m2 ?> m² &nbsp;·&nbsp;
            Max <?= $chambre_selectionnee->capacite_max ?> personnes &nbsp;·&nbsp;
            <?= number_format($chambre_selectionnee->prix_nuit,0,',',' ') ?> FCFA/nuit
        </div>
        <div class="stats-grid">
            <?php
            $dispo_count = 0; $indispo_count = 0; $attente_count = 0;
            $today = new DateTime(); $today->setTime(0,0,0);
            $d2 = new DateTime($date_debut_mois);
            while ($d2 <= new DateTime($date_fin_mois)) {
                $k = $d2->format('Y-m-d');
                if ($d2 >= $today) {
                    $s = $statuts_par_jour[$k]['statut'] ?? 'disponible';
                    if ($s==='disponible') $dispo_count++;
                    elseif ($s==='indisponible') $indispo_count++;
                    else $attente_count++;
                }
                $d2->modify('+1 day');
            }
            $total_futur = $dispo_count + $indispo_count + $attente_count;
            ?>
            <div class="stat-box"><div class="stat-val"><?= $dispo_count ?></div><div class="stat-lbl">Jours disponibles</div></div>
            <div class="stat-box"><div class="stat-val"><?= $indispo_count ?></div><div class="stat-lbl">Jours bloqués</div></div>
            <div class="stat-box"><div class="stat-val"><?= $total_futur>0?round(($indispo_count+$attente_count)/$total_futur*100):0 ?>%</div><div class="stat-lbl">Taux d'occupation</div></div>
        </div>
    </div>
    <?php else: ?>
    <div class="info-banner">
        Vue globale — les jours <strong>partiellement disponibles</strong> signifient que certaines chambres sont encore libres.
        Sélectionnez une chambre pour voir ses disponibilités précises.
    </div>
    <?php endif; ?>

    <!-- Navigation mois -->
    <div class="nav-mois">
        <button class="btn-nav" onclick="changeMonth(<?= $month==1?12:$month-1 ?>)">← Mois précédent</button>
        <div class="mois-label"><?= $moisFR[$month] ?> <?= $year ?></div>
        <button class="btn-nav" onclick="changeMonth(<?= $month==12?1:$month+1 ?>)">Mois suivant →</button>
    </div>

    <!-- Calendrier -->
    <div class="cal-wrap">
        <table class="cal-table">
            <thead>
                <tr>
                    <th>Dim</th><th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $today_str = date('Y-m-d');
            $premier_jour = new DateTime("$year-$month-01");
            $nb_jours = (int)$premier_jour->format('t');
            $debut_semaine = (int)$premier_jour->format('w'); // 0=dim

            $jour = 1;
            $semaine_count = 0;

            // Calculer combien de semaines
            $total_cases = $debut_semaine + $nb_jours;
            $nb_semaines = ceil($total_cases / 7);

            for ($s = 0; $s < $nb_semaines; $s++):
            ?>
            <tr>
            <?php for ($col = 0; $col < 7; $col++):
                $case_idx = $s * 7 + $col;
                $hors_mois = ($case_idx < $debut_semaine || $jour > $nb_jours);

                if ($hors_mois) {
                    // Jour hors mois
                    if ($case_idx < $debut_semaine) {
                        $prev_day = $nb_jours - ($debut_semaine - $col - 1);
                        $prev_month = $month == 1 ? 12 : $month - 1;
                        $prev_year  = $month == 1 ? $year - 1 : $year;
                        $display = $prev_day;
                    } else {
                        $display = $jour - $nb_jours;
                        $jour++;
                    }
                    echo '<td class="cal-day hors-mois"><div class="day-num">'.$display.'</div></td>';
                    continue;
                }

                $date_str = sprintf('%04d-%02d-%02d', $year, $month, $jour);
                $info = $statuts_par_jour[$date_str] ?? ['statut'=>'disponible','motifs'=>[],'nb'=>0];
                $statut = $info['statut'];
                $passe = ($date_str < $today_str);
                $auj   = ($date_str === $today_str);

                $classes = ['cal-day', $statut];
                if ($passe) $classes[] = 'passe';
                if ($auj)   $classes[] = 'aujourd-hui';

                // Tooltip
                $tooltip = '';
                if ($passe) {
                    $tooltip = 'Date passée';
                } elseif ($statut === 'disponible') {
                    $tooltip = $chambre_id ? 'Disponible' : 'Toutes les chambres disponibles';
                } elseif ($statut === 'indisponible') {
                    $tooltip = $chambre_id ? 'Chambre réservée' : 'Toutes les chambres réservées';
                    if (!empty($info['motifs'])) $tooltip .= ' — ' . implode(', ', array_slice($info['motifs'],0,2));
                } elseif ($statut === 'en_attente') {
                    $tooltip = 'Réservation en attente de validation';
                    if (!empty($info['motifs'])) $tooltip .= ' — ' . $info['motifs'][0];
                } elseif ($statut === 'partiellement') {
                    $dispo_ch = count($chambres_all) - $info['nb'];
                    $tooltip = "$dispo_ch chambre(s) encore disponible(s)";
                }

                $barre_class = 'barre-'.$statut;
                echo '<td class="'.implode(' ',$classes).'">';
                if ($tooltip) echo '<div class="day-tooltip">'.htmlspecialchars($tooltip).'</div>';
                echo '<div class="day-num">'.$jour.'</div>';
                echo '<div class="day-barre '.$barre_class.'"></div>';
                echo '</td>';

                $jour++;
            endfor; ?>
            </tr>
            <?php endfor; ?>
            </tbody>
        </table>

        <!-- Légende -->
        <div class="legende">
            <div class="legende-item" onclick="filtrer('disponible',this)">
                <div class="legende-couleur" style="background:rgba(40,167,69,.15);border-color:rgba(40,167,69,.4);"></div>
                <span>Disponible</span>
            </div>
            <div class="legende-item" onclick="filtrer('en_attente',this)">
                <div class="legende-couleur" style="background:rgba(255,193,7,.2);border-color:rgba(255,193,7,.5);"></div>
                <span>En attente</span>
            </div>
            <?php if (!$chambre_id): ?>
            <div class="legende-item" onclick="filtrer('partiellement',this)">
                <div class="legende-couleur" style="background:linear-gradient(to right,rgba(40,167,69,.3),rgba(220,53,69,.3));border-color:#ccc;"></div>
                <span>Partiellement disponible</span>
            </div>
            <?php endif; ?>
            <div class="legende-item" onclick="filtrer('indisponible',this)">
                <div class="legende-couleur" style="background:rgba(220,53,69,.15);border-color:rgba(220,53,69,.4);"></div>
                <span>Indisponible</span>
            </div>
            <div class="legende-item" onclick="filtrer('tous',this)">
                <div class="legende-couleur" style="background:#eee;border-color:#ccc;"></div>
                <span>Tout afficher</span>
            </div>
        </div>
    </div>
</div>

<script>
function changeChambre() {
    const id  = document.getElementById('chambreSelect').value;
    const url = new URL(window.location);
    id ? url.searchParams.set('chambre_id', id) : url.searchParams.delete('chambre_id');
    url.searchParams.set('month', <?= $month ?>);
    url.searchParams.set('year',  <?= $year ?>);
    window.location.href = url.toString();
}

function changeMonth(m) {
    m = parseInt(m);
    let y = <?= $year ?>;
    if (<?= $month ?> === 12 && m === 1) y++;
    if (<?= $month ?> === 1  && m === 12) y--;
    const url = new URL(window.location);
    url.searchParams.set('month', m);
    url.searchParams.set('year',  y);
    window.location.href = url.toString();
}

function changeYear(y) {
    const url = new URL(window.location);
    url.searchParams.set('year', y);
    window.location.href = url.toString();
}

function filtrer(statut, el) {
    document.querySelectorAll('.legende-item').forEach(function(i){
        i.classList.remove('active');
        i.style.opacity = '0.5';
    });
    el.classList.add('active');
    el.style.opacity = '1';

    document.querySelectorAll('.cal-day').forEach(function(cell) {
        if (cell.classList.contains('hors-mois')) return;
        cell.style.transition = 'opacity 0.2s';
        if (statut === 'tous') {
            cell.style.opacity = '1';
        } else if (cell.classList.contains(statut)) {
            cell.style.opacity = '1';
        } else {
            cell.style.opacity = '0.08';
        }
    });
}
</script>

<?php include(__DIR__ . '/../layouts/footer.php'); ?>