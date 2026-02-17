<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulation - ReliefFlow</title>

    <!-- CSS TEMPLATE -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body class="with-welcome-text">

    <div class="container-scroller">
        <?php
        include(ROOT_PATH . '/public/pages/header.php'); // Include the header file
        ?>
            <!-- MAIN PANEL -->
            <div class="main-panel">
                <div class="content-wrapper">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">

                                    <h4>Simulation d'affectation des dons</h4>

                                    <form id="simModeForm" class="d-flex justify-content-end mb-2 gap-2 align-items-center" onsubmit="return false;">
                                        <label for="simMode" class="me-2 mb-0">Mode :</label>
                                        <select id="simMode" class="form-select w-auto" style="min-width:180px">
                                            <option value="priorite">Priorité (plus ancien)</option>
                                            <option value="min">Plus petit besoin</option>
                                            <option value="proportionnel">Proportionnel</option>
                                        </select>
                                        <button id="simulateBtn" class="btn btn-warning" type="button">Simuler l'affectation des dons</button>
                                        <button id="cancelSimBtn" class="btn btn-outline-secondary" style="display:none" type="button">Annuler la simulation</button>
                                    </form>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Valeurs initiales</h5>
                                            <table class="table table-hover table-primary bg-primary bg-opacity-10 rounded-3">
                                                <thead>
                                                    <tr>
                                                        <th>Ville</th>
                                                        <th>Besoin</th>
                                                        <th>Date</th>
                                                        <th>Besoin</th>
                                                        <th>Stock dons</th>
                                                        <th>P.U</th>
                                                        <th>P.Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($besoinVilles)): foreach ($besoinVilles as $b): ?>
                                                            <tr data-besoin-id="<?= htmlspecialchars($b['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                                <td><?= htmlspecialchars($b['nomVille'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                                                <td><?= htmlspecialchars($b['nomDon'] ?? $b['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                                                <td><?= htmlspecialchars(isset($b['date_']) ? $b['date_'] : '', ENT_QUOTES, 'UTF-8') ?></td>
                                                                <td><?= htmlspecialchars(number_format((int)($b['quantite'] ?? 0), 0, '.', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                                                                <?php
                                                                // Calcul du stock dons initial pour ce besoin (somme des quantités dans la table dons)
                                                                $stock = 0;
                                                                if (isset($b['idModeleDons'])) {
                                                                    if (!isset($__stockMapDonsModel)) {
                                                                        // Génère la map stock dons par modèle une seule fois
                                                                        $__stockMapDonsModel = [];
                                                                        $donsRows = \app\models\DonsModel::getAllDonations();
                                                                        foreach ($donsRows as $don) {
                                                                            $k = isset($don['idModeleDons']) ? $don['idModeleDons'] : '0';
                                                                            $__stockMapDonsModel[$k] = ($__stockMapDonsModel[$k] ?? 0) + (int)($don['quantite'] ?? 0);
                                                                        }
                                                                    }
                                                                    $stockKey = $b['idModeleDons'];
                                                                    $stock = $__stockMapDonsModel[$stockKey] ?? 0;
                                                                } else {
                                                                    $stock = 0;
                                                                }
                                                                ?>
                                                                <td class="bs-stock"><?= htmlspecialchars(number_format($stock, 0, '.', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                                                                <td><?= htmlspecialchars(number_format((float)($b['prixUnitaire'] ?? 0), 2, '.', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                                                                <?php $bpu = (float)($b['prixUnitaire'] ?? 0);
                                                                $btotal = $bpu * ((int)($b['quantite'] ?? 0)); ?>
                                                                <td class="text-end"><?= htmlspecialchars(number_format($btotal, 2, '.', ' '), ENT_QUOTES, 'UTF-8') ?></td>
                                                            </tr>
                                                        <?php endforeach;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">Aucune donnée</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Après simulation</h5>
                                            <table class="table table-hover table-warning bg-warning bg-opacity-25 rounded-3">
                                                <thead>
                                                    <tr>
                                                        <th>Besoin restant</th>
                                                        <th>Stock final</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($besoinVilles)): foreach ($besoinVilles as $b): ?>
                                                            <tr data-besoin-id="<?= htmlspecialchars($b['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-id-modele="<?= htmlspecialchars($b['idModeleDons'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                                <td class="bs-apres">0</td>
                                                                <td class="bs-stock-final">0</td>
                                                            </tr>
                                                        <?php endforeach;
                                                    else: ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center">Aucune donnée</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </div>

    <!-- container-scroller --> <!-- plugins:js -->
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/js/vendor.bundle.base.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/chart.js/chart.umd.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/off-canvas.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/template.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/settings.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/hoverable-collapse.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/todolist.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/dashboard.js"></script>

    <script nonce="<?= $csp_nonce ?>">
        (function() {
            // Initialisation du tableau "Après simulation" avec les valeurs initiales
            function initSimulationTable() {
                // Récupère les besoins et leur stock initial par modèle
                let stockParModele = {};
                document.querySelectorAll('div.col-md-6')[0]?.querySelectorAll('tr[data-besoin-id]')?.forEach(tr => {
                    const idModele = tr.getAttribute('data-id-modele');
                    const stockCell = tr.querySelector('.bs-stock');
                    let stock = 0;
                    if (stockCell) stock = parseInt(stockCell.innerText.replace(/\s/g, '') || '0', 10);
                    if (typeof stockParModele[idModele] === 'undefined') stockParModele[idModele] = stock;
                });
                document.querySelectorAll('div.col-md-6')[1]?.querySelectorAll('tr[data-besoin-id]')?.forEach(tr => {
                    const idModele = tr.getAttribute('data-id-modele');
                    const besoinCell = tr.querySelector('.bs-apres');
                    const stockOpCell = tr.querySelector('.bs-restant');
                    const stockFinalCell = tr.querySelector('.bs-stock-final');
                    // Besoin restant initial = besoin initial
                    const besoinInit = document.querySelector('div.col-md-6 tr[data-besoin-id="' + tr.getAttribute('data-besoin-id') + '"] td:nth-child(4)');
                    let besoinVal = besoinInit ? besoinInit.innerText.replace(/\s/g, '') : '0';
                    besoinVal = parseInt(besoinVal || '0', 10);
                    if (besoinCell) besoinCell.innerText = besoinVal;
                    if (stockOpCell) stockOpCell.innerText = stockParModele[idModele] ?? 0;
                    if (stockFinalCell) stockFinalCell.innerText = stockParModele[idModele] ?? 0;
                });
            }

            document.addEventListener('DOMContentLoaded', initSimulationTable);

            const btn = document.getElementById('simulateBtn');
            if (!btn) return;
            const cancelBtn = document.getElementById('cancelSimBtn');
            btn.addEventListener('click', function() {
                btn.disabled = true;
                const original = btn.innerHTML;
                btn.innerHTML = 'Simulation en cours...';
                let dots = 0;
                const interval = setInterval(() => {
                    btn.innerHTML = 'Simulation en cours' + '.'.repeat(dots % 4);
                    dots++;
                }, 400);
                const mode = document.getElementById('simMode')?.value || 'priorite';
                fetch('<?= BASE_URL ?>/simulate?mode=' + encodeURIComponent(mode))
                    .then(r => r.json())
                    .then(data => {
                        clearInterval(interval);
                        btn.innerHTML = 'Appliquer la simulation';
                        btn.disabled = false;
                        if (cancelBtn) cancelBtn.style.display = 'inline-block';
                        if (data && Array.isArray(data.result) && data.result.length > 0) {
                            // Calcul dynamique du stock restant par modèle (après toutes les distributions)
                            let stockRestantParModele = {};
                            let stockFinalParModele = {};
                            // On commence par le stock initial par modèle (somme des dons)
                            if (data && Array.isArray(data.result)) {
                                data.result.forEach(row => {
                                    if (typeof row.idModeleDons !== 'undefined') {
                                        if (typeof stockRestantParModele[row.idModeleDons] === 'undefined') {
                                            // Stock initial pour ce modèle (somme de tous les dons de ce modèle)
                                            stockRestantParModele[row.idModeleDons] = 0;
                                            if (Array.isArray(data.dons)) {
                                                data.dons.forEach(don => {
                                                    if (don.idModeleDons == row.idModeleDons) {
                                                        stockRestantParModele[row.idModeleDons] += parseInt(don.quantite || 0, 10);
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                                // On applique la distribution pour chaque besoin (dans l'ordre)
                                // Calculer la somme des allocations par modèle (une seule fois par modèle)
                                let sommeAllocParModele = {};
                                data.result.forEach(row => {
                                    if (typeof row.idModeleDons !== 'undefined') {
                                        sommeAllocParModele[row.idModeleDons] = (sommeAllocParModele[row.idModeleDons] || 0) + (row.sim_donnee || 0);
                                    }
                                });
                                // Afficher la même valeur de stock restant pour tous les besoins d'un même modèle
                                data.result.forEach(row => {
                                    let tr = document.querySelectorAll('div.col-md-6')[1]?.querySelector('tr[data-besoin-id="' + row.id + '"]');
                                    if (tr) {
                                        const restantEl = tr.querySelector('.bs-restant');
                                        const apresEl = tr.querySelector('.bs-apres');
                                        let stockInitial = stockRestantParModele[row.idModeleDons] ?? 0;
                                        let stockRestant = stockInitial - (sommeAllocParModele[row.idModeleDons] || 0);
                                        if (restantEl) restantEl.innerText = stockRestant;
                                        // besoin restant = besoin initial - dons affectés
                                        const besoinRestant = Math.max(0, (row.initial || 0) - (row.sim_donnee || 0));
                                        if (apresEl) apresEl.innerText = besoinRestant;
                                        tr.style.transition = 'background-color 0.6s ease';
                                        tr.style.backgroundColor = '#fff3cd';
                                        setTimeout(() => tr.style.backgroundColor = '', 1200);
                                    }
                                });
                                // Calcul du stock final par modèle (stock initial - somme des allocations)
                                Object.keys(stockRestantParModele).forEach(idModele => {
                                    stockFinalParModele[idModele] = stockRestantParModele[idModele] - (sommeAllocParModele[idModele] || 0);
                                });
                                // Afficher la valeur de stock final (sim_restant) renvoyée par le backend pour chaque besoin
                                data.result.forEach(row => {
                                    let tr = document.querySelectorAll('div.col-md-6')[1]?.querySelector('tr[data-besoin-id="'+row.id+'"]');
                                    if (tr) {
                                        const stockFinalEl = tr.querySelector('.bs-stock-final');
                                        if (stockFinalEl) stockFinalEl.innerText = row.sim_restant;
                                    }
                                });
                            }
                        }
                    })
                    .catch(err => {
                        clearInterval(interval);
                        btn.disabled = false;
                        btn.innerHTML = original;
                        alert('Erreur lors de la simulation');
                    });
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    cancelBtn.style.display = 'none';
                    document.querySelectorAll('tr[data-besoin-id]').forEach(tr => {
                        const donneeEl = tr.querySelector('.bs-donnee');
                        const restantEl = tr.querySelector('.bs-restant');
                        const realRestant = tr.dataset.realRestant || tr.dataset.initial || '0';
                        const apresEl = tr.querySelector('.bs-apres');
                        const initial = parseInt(tr.dataset.initial || '0', 10);
                        if (donneeEl) donneeEl.innerText = '0';
                        if (restantEl) restantEl.innerText = realRestant;
                        if (apresEl) apresEl.innerText = Math.max(0, initial - 0);
                        delete tr.dataset.simDonnee;
                        delete tr.dataset.simRestant;
                    });
                });
            }
        })();
    </script>
</body>

</html>