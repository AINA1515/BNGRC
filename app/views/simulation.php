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

    <!-- NAVBAR TOP -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <button class="navbar-toggler" type="button" data-bs-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <a class="navbar-brand brand-logo ms-3">ReliefFlow - Simulation</a>
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

          <div class="row">

            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">

                  <h4>Simulation d'affectation des dons</h4>

                  <div class="d-flex justify-content-end mb-2 gap-2">
                    <button id="simulateBtn" class="btn btn-warning">Simuler l'affectation des dons</button>
                    <button id="cancelSimBtn" class="btn btn-outline-secondary" style="display:none">Annuler la simulation</button>
                  </div>

                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Ville</th>
                        <th>Besoin</th>
                        <th>Date</th>
                        <th>depart(besoin)</th>
                        <th>donation</th>
                        <th>Restant(dons)</th>
                        <th>Après(besoins)</th>
                        <th>P.U</th>
                        <th>P.Total(besoin)</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php if (!empty($besoinVilles)): foreach ($besoinVilles as $b): ?>
                          <tr data-besoin-id="<?= htmlspecialchars($b['id'] ?? '') ?>" data-real-donnee="<?= htmlspecialchars($b['donnee']) ?>" data-real-restant="<?= htmlspecialchars($b['restant']) ?>" data-initial="<?= htmlspecialchars($b['quantite']) ?>">
                            <td><?= htmlspecialchars($b['nomVille']) ?></td>
                            <td><?= htmlspecialchars($b['nomDon']) ?></td>
                            <td class="bs-date"><?= htmlspecialchars(isset($b['date_']) ? $b['date_'] : '') ?></td>
                            <td class="bs-depart"><?= htmlspecialchars(number_format((int)($b['quantite'] ?? 0), 0, '.', ' ')) ?></td>
                            <td class="bs-donnee">0</td>
                            <td class="bs-restant"><?= htmlspecialchars(number_format((int)($b['restant'] ?? 0), 0, '.', ' ')) ?></td>
                            <td class="bs-apres"><?php $initial = (int)($b['quantite'] ?? 0);
                                                      $realDon = min((int)($b['donnee'] ?? 0), $initial);
                                                      echo htmlspecialchars(number_format($initial - $realDon, 0, '.', ' ')); ?></td>
                            <td><?= htmlspecialchars(number_format((float)($b['prixUnitaire'] ?? 0), 2, '.', ' ')) ?></td>
                            <?php $bpu = (float)($b['prixUnitaire'] ?? 0);
                            $btotal = $bpu * ((int)($b['quantite'] ?? 0)); ?>
                            <td class="text-end"><?= htmlspecialchars(number_format($btotal, 2, '.', ' ')) ?></td>
                          </tr>
                        <?php endforeach;
                      else: ?>
                        <tr>
                          <td colspan="9" class="text-center">Aucune donnée</td>
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
    (function(){
      const btn = document.getElementById('simulateBtn');
      if (!btn) return;
      const cancelBtn = document.getElementById('cancelSimBtn');
      btn.addEventListener('click', function(){
        btn.disabled = true;
        const original = btn.innerHTML;
        btn.innerHTML = 'Simulation en cours...';
        let dots = 0;
        const interval = setInterval(()=>{ btn.innerHTML = 'Simulation en cours' + '.'.repeat(dots%4); dots++; }, 400);
        fetch('<?= BASE_URL ?>/simulate')
          .then(r => r.json())
          .then(data => {
            clearInterval(interval);
            btn.innerHTML = 'Appliquer la simulation';
            btn.disabled = false;
            if (cancelBtn) cancelBtn.style.display = 'inline-block';
            if (data && Array.isArray(data.result) && data.result.length>0) {
              data.result.forEach(row => {
                let tr = document.querySelector('tr[data-besoin-id="'+row.id+'"]');
                if (tr) {
                  const donneeEl = tr.querySelector('.bs-donnee');
                  const restantEl = tr.querySelector('.bs-restant');
                  const apresEl = tr.querySelector('.bs-apres');
                  tr.dataset.simDonnee = row.sim_donnee;
                  tr.dataset.simRestant = row.sim_restant;
                  if (donneeEl) donneeEl.innerText = row.sim_donnee;
                  if (restantEl) restantEl.innerText = row.sim_restant;
                  if (apresEl) {
                    const initial = parseInt(tr.dataset.initial || '0', 10);
                    apresEl.innerText = Math.max(0, initial - (row.sim_donnee || 0));
                  }
                  tr.style.transition = 'background-color 0.6s ease';
                  tr.style.backgroundColor = '#fff3cd';
                  setTimeout(()=> tr.style.backgroundColor = '', 1200);
                }
              });
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
        cancelBtn.addEventListener('click', function(){
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
