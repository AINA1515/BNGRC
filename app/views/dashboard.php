<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ReliefFlow</title>

  <!-- CSS TEMPLATE -->
  <link rel="stylesheet" href="/assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="/assets/css/style.css">
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
            <a class="nav-link" href="#">
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
                  <a class="nav-link" href="/formBesoin">
                    Besoin
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="/formDons">
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
                            <th>Départ</th>
                            <th>Donnée</th>
                            <th>Restant</th>
                            <th>P.U</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php if (!empty($besoinVilles)): foreach ($besoinVilles as $b): ?>
                              <tr>
                                <td><?= htmlspecialchars($b['nomVille']) ?></td>
                                <td><?= htmlspecialchars($b['nomDon']) ?></td>
                                <td><?= htmlspecialchars($b['quantite']) ?></td>
                                <td><?= htmlspecialchars($b['donnee']) ?></td>
                                <td><?= htmlspecialchars($b['restant']) ?></td>
                                <td><?= htmlspecialchars($b['prixUnitaire']) ?></td>
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

                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>P.U</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php if (!empty($dons)): foreach ($dons as $d): ?>
                          <tr>
                            <td><?= htmlspecialchars($d['nom']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($d['typeDon']) ?></span></td>
                            <td><?= htmlspecialchars($d['quantite']) ?></td>
                            <td><?= $d['prixUnitaire'] ?></td>
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
  <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/js/vendor.bundle.base.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script> <!-- endinject --> <!-- Plugin js for this page -->
  <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/chart.js/chart.umd.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/progressbar.js/progressbar.min.js"></script> <!-- End plugin js for this page --> <!-- inject:js -->
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/off-canvas.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/template.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/settings.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/hoverable-collapse.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/todolist.js"></script> <!-- endinject --> <!-- Custom js for this page-->
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/jquery.cookie.js" type="text/javascript"></script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/dashboard.js"></script>
  <script nonce="<?= $csp_nonce ?>">
    var sinistreChartData = <?= json_encode($sinistreChartData ?? []) ?>;
  </script>
  <script nonce="<?= $csp_nonce ?>" src="/assets/js/my_script.js"></script> <!-- <script nonce = "<?= $csp_nonce ?>" src="/assets/js/Chart.roundedBarCharts.js"></script> --> <!-- End custom js for this page-->
</body>

</html>