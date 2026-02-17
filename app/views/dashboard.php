<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ReliefFlow</title>

  <!-- CSS TEMPLATE -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body class="with-welcome-text">

  <div class="container-scroller">

    <!-- NAVBAR TOP -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <button class="navbar-toggler" type="button" data-bs-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <a class="navbar-brand brand-logo ms-3">ReliefFlow</a>
      </div>
    </nav>


    <div class="container-fluid page-body-wrapper">

      <!-- SIDEBAR -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">

          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

          <li class="nav-item nav-category">Gestion</li>

          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#formsMenu">
              <i class="mdi mdi-form-select menu-icon"></i>
              <span class="menu-title">Formulaires</span>
              <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="formsMenu">
              <ul class="nav flex-column sub-menu">

                <li class="nav-item">
                  <a class="nav-link" href="<?= BASE_URL ?>/formBesoin">
                    Besoin
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="<?= BASE_URL ?>/formDons">
                    Dons
                  </a>
                </li>

              </ul>
            </div>
          </li>

        </ul>
      </nav>



      <!-- MAIN PANEL -->
      <div class="main-panel">
        <div class="content-wrapper">

          <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#besoins">Besoins</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#dons">Dons</a>
            </li>
          </ul>


          <div class="tab-content">

            <!-- TAB BESOINS -->
            <div class="tab-pane fade show active" id="besoins">

              <div class="row">

                <div class="col-lg-8">
                  <div class="card">
                    <div class="card-body">

                      <h4>Liste besoins</h4>

                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>Ville</th>
                            <th>Besoin</th>
                            <th>Date</th>
                            <th>Quantité</th>
                            <th>P.U</th>
                            <th>P.Total</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php if (!empty($besoinVilles)): foreach ($besoinVilles as $b): ?>
                              <tr>
                                <td><?= htmlspecialchars($b['nomVille']) ?></td>
                                <td><?= htmlspecialchars($b['nomDon']) ?></td>
                                <td><?= htmlspecialchars(isset($b['date_']) ? $b['date_'] : '') ?></td>
                                <td><?= htmlspecialchars(number_format((int)($b['quantite'] ?? 0), 0, '.', ' ')) ?></td>
                                <td><?= htmlspecialchars(number_format((float)($b['prixUnitaire'] ?? 0), 2, '.', ' ')) ?></td>
                                <?php $bpu = (float)($b['prixUnitaire'] ?? 0);
                                $btotal = $bpu * ((int)($b['quantite'] ?? 0)); ?>
                                <td class="text-end"><?= htmlspecialchars(number_format($btotal, 2, '.', ' ')) ?></td>
                              </tr>
                            <?php endforeach;
                          else: ?>
                            <tr>
                              <td colspan="6" class="text-center">Aucune donnée</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>

                    </div>
                  </div>
                </div>


                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <h4>Sinistres</h4>
                      <canvas id="sinistreChart"></canvas>
                    </div>
                  </div>
                </div>

              </div>
            </div>



            <!-- TAB DONS -->
            <div class="tab-pane fade" id="dons">

              <div class="card">
                <div class="card-body">

                  <h4>Liste dons</h4>

                  <div class="mb-3">
                    <form action="<?= BASE_URL ?>/type/add" method="POST" class="d-flex gap-2 align-items-center">
                      <input type="text" name="name" class="form-control" placeholder="Nouveau type de don" required>
                      <button class="btn btn-sm btn-outline-primary" type="submit">Ajouter le type</button>
                    </form>
                  </div>

                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Quantité</th>
                        <th>P.U</th>
                        <th>P.Total</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php if (!empty($dons)): foreach ($dons as $d): ?>
                          <tr>
                            <td><?= htmlspecialchars($d['nom']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($d['typeDon']) ?></span></td>
                            <td><?= htmlspecialchars($d['date_'] ?? '') ?></td>
                            <td><?= htmlspecialchars(number_format((int)($d['quantite'] ?? 0), 0, '.', ' ')) ?></td>
                            <td><?= htmlspecialchars(number_format((float)($d['prixUnitaire'] ?? 0), 2, '.', ' ')) ?></td>
                            <?php $dpu = (float)($d['prixUnitaire'] ?? 0);
                            $dtotal = $dpu * ((int)($d['quantite'] ?? 0)); ?>
                            <td class="text-end"><?= htmlspecialchars(number_format($dtotal, 2, '.', ' ')) ?></td>
                          </tr>
                        <?php endforeach;
                      else: ?>
                        <tr>
                          <td colspan="4" class="text-center">Aucun don</td>
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

  <!-- container-scroller --> <!-- plugins:js -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script> <!-- endinject --> <!-- Plugin js for this page -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/chart.js/chart.umd.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/progressbar.js/progressbar.min.js"></script> <!-- End plugin js for this page --> <!-- inject:js -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/off-canvas.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/template.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/settings.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/hoverable-collapse.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/todolist.js"></script> <!-- endinject --> <!-- Custom js for this page-->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/jquery.cookie.js" type="text/javascript"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/dashboard.js"></script>
  <script nonce="<?= $csp_nonce ?>">
    var sinistreChartData = <?= json_encode($sinistreChartData ?? []) ?>;
  </script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/my_script.js"></script> <!-- <script nonce = "<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/Chart.roundedBarCharts.js"></script> --> <!-- End custom js for this page-->
  <script nonce="<?= $csp_nonce ?>">
    (function() {
      const btn = document.getElementById('simulateBtn');
      if (!btn) return;
      const cancelBtn = document.getElementById('cancelSimBtn');
      btn.addEventListener('click', function() {
        btn.disabled = true;
        const original = btn.innerHTML;
        btn.innerHTML = 'Simulation en cours...';
        // simple animation: dots
        let dots = 0;
        const interval = setInterval(() => {
          btn.innerHTML = 'Simulation en cours' + '.'.repeat(dots % 4);
          dots++;
        }, 400);
        const statusEl = document.getElementById('simulateStatus');
        if (statusEl) statusEl.innerText = '';
        fetch('<?= BASE_URL ?>/simulate')
          .then(r => r.json())
          .then(data => {
            clearInterval(interval);
            btn.innerHTML = 'Appliquer la simulation';
            btn.disabled = false;
            if (cancelBtn) cancelBtn.style.display = 'inline-block';
            // apply simulated values into table
            if (data && Array.isArray(data.result) && data.result.length > 0) {
              data.result.forEach(row => {
                let tr = document.querySelector('tr[data-besoin-id="' + row.id + '"]');
                if (!tr) {
                  // fallback: find by ville+don text
                  const rows = Array.from(document.querySelectorAll('table.table tbody tr'));
                  tr = rows.find(r => {
                    const villeText = r.children[0] && r.children[0].innerText.trim();
                    const donText = r.children[1] && r.children[1].innerText.trim();
                    return villeText === row.idVille.toString() || donText === row.idDons.toString() || (villeText + "|" + donText) === ((row.idVille || '') + "|" + (row.idDons || ''));
                  });
                }
                if (tr) {
                  const donneeEl = tr.querySelector('.bs-donnee');
                  const restantEl = tr.querySelector('.bs-restant');
                  const apresEl = tr.querySelector('.bs-apres');
                  // store simulated values on the row for cancel
                  tr.dataset.simDonnee = row.sim_donnee;
                  tr.dataset.simRestant = row.sim_restant;
                  // format numbers for readability
                  const fmtInt = (v) => (typeof v === 'number' ? v : parseInt(v || 0, 10));
                  const fmtPrice = (v) => (typeof v === 'number' ? v : parseFloat(v || 0));
                  if (donneeEl) donneeEl.innerText = new Intl.NumberFormat('fr-FR', {
                    maximumFractionDigits: 0
                  }).format(fmtInt(row.sim_donnee));
                  if (restantEl) restantEl.innerText = new Intl.NumberFormat('fr-FR', {
                    maximumFractionDigits: 0
                  }).format(fmtInt(row.sim_restant));
                  if (apresEl) {
                    const initial = parseInt(tr.dataset.initial || '0', 10);
                    apresEl.innerText = new Intl.NumberFormat('fr-FR', {
                      maximumFractionDigits: 0
                    }).format(Math.max(0, initial - (row.sim_donnee || 0)));
                  }
                  // highlight change
                  tr.style.transition = 'background-color 0.6s ease';
                  tr.style.backgroundColor = '#fff3cd';
                  setTimeout(() => tr.style.backgroundColor = '', 1200);
                }
              });
            } else {
              if (statusEl) statusEl.innerText = 'Aucun résultat de simulation';
            }
          })
          .catch(err => {
            clearInterval(interval);
            btn.disabled = false;
            btn.innerHTML = original;
            alert('Erreur lors de la simulation');
          });
      });

      // Cancel simulation: revert all rows to initial zeros
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
          // hide cancel
          cancelBtn.style.display = 'none';
          // reset all rows to the pre-simulation view: Donnee = 0, Restant = original group total
          document.querySelectorAll('tr[data-besoin-id]').forEach(tr => {
            const donneeEl = tr.querySelector('.bs-donnee');
            const restantEl = tr.querySelector('.bs-restant');
            const realRestant = tr.dataset.realRestant || tr.dataset.initial || '0';
            const apresEl = tr.querySelector('.bs-apres');
            const initial = parseInt(tr.dataset.initial || '0', 10);
            if (donneeEl) donneeEl.innerText = new Intl.NumberFormat('fr-FR', {
              maximumFractionDigits: 0
            }).format(0);
            if (restantEl) restantEl.innerText = new Intl.NumberFormat('fr-FR', {
              maximumFractionDigits: 0
            }).format(parseInt(realRestant, 10) || 0);
            if (apresEl) apresEl.innerText = new Intl.NumberFormat('fr-FR', {
              maximumFractionDigits: 0
            }).format(Math.max(0, initial - 0));
            delete tr.dataset.simDonnee;
            delete tr.dataset.simRestant;
          });
        });
      }
    })();
  </script>
</body>

</html>