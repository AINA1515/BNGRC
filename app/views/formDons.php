<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>RelifFlow</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/typicons/typicons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/assets/js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/images/favicon.png" />

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendors/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" />
</head>

<body class="with-welcome-text">
  <div class="container-scroller">

    <?php
    include(ROOT_PATH . '/public/pages/header.php'); // Include the header file
    ?><div class="main-panel">
      <div class="content-wrapper">
        <div class="row justify-content-center mt-5">
          <div class="col-lg-6">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Créer des Dons</h4>
                <!-- Model form: manage donation models (name + type) -->
                <div class="mb-4">
                  <h5>Créer un modèle de don</h5>
                  <?php if (!empty($_GET['model_added'])): ?>
                    <div class="alert alert-success">Modèle ajouté avec succès.</div>
                  <?php endif; ?>
                  <form action="<?= BASE_URL ?>/dons/add-model" method="POST" class="row g-2 align-items-end">
                    <div class="col-6">
                      <label>Nom du modèle</label>
                      <input type="text" name="model_nom" class="form-control" required>
                    </div>
                    <div class="col-4">
                      <label>Type</label>
                      <select name="model_type" class="form-control" required>
                        <option value="">-- Choisir un type --</option>
                        <?php if (!empty($types)) {
                          foreach ($types as $t) { ?>
                            <option value="<?= htmlspecialchars($t['id']) ?>"><?= htmlspecialchars($t['nom']) ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                    <div class="col-2">
                      <button class="btn btn-sm btn-primary" type="submit">Ajouter</button>
                    </div>
                  </form>
                </div>
                <br>
                  <hr>
                  <br>
                <form action="<?= BASE_URL ?>/dons/add-multiple" method="POST" id="multiDonsForm">
                  <div id="donRows">
                    <div class="don-row mb-3 row">
                      <div class="col-4">
                        <label>Modèle</label>
                        <select name="modele[]" class="form-control modele-select" required>
                          <option value="">-- Choisir un modèle --</option>
                          <?php if (!empty($modelesAll)) {
                            foreach ($modelesAll as $m) { ?>
                              <option value="<?= htmlspecialchars($m['id']) ?>"><?= htmlspecialchars($m['nom']) ?></option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                      <div class="col-3">
                        <label>Type</label>
                        <select name="type[]" class="form-control" required>
                          <option value="">-- Choisir un type --</option>
                          <?php if (!empty($types)) {
                            foreach ($types as $t) { ?>
                              <option value="<?= htmlspecialchars($t['id']) ?>"><?= htmlspecialchars($t['nom']) ?></option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                      <div class="col-2">
                        <label>Quantité</label>
                        <input type="number" name="quantite[]" class="form-control" required>
                      </div>
                      <div class="col-1">
                        <label>Prix U.</label>
                        <input type="number" step="0.01" name="prix[]" class="form-control">
                      </div>
                      <div class="col-2">
                        <label>Date</label>
                        <input type="datetime-local" name="date[]" class="form-control">
                      </div>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" id="addDonRow">Ajouter un don</button>
                    <button type="submit" class="btn btn-primary">Créer les dons</button>
                  </div>
                </form>

                <hr />
                <h5>Ajouter un type de don</h5>
                <?php if (!empty($_GET['type_added'])): ?>
                  <div class="alert alert-success">Type ajouté avec succès.</div>
                <?php endif; ?>
                <form action="<?= BASE_URL ?>/type/add" method="POST" class="mt-2">
                  <div class="input-group">
                    <input type="text" name="name" class="form-control" placeholder="Nom du type" required>
                    <button class="btn btn-outline-primary" type="submit">Ajouter</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/">
          <i class="mdi mdi-grid-large menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item nav-category">Navigations</li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
          <i class="menu-icon mdi mdi-card-text-outline"></i>
          <span class="menu-title">Forms</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="form-elements">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/formBesoin">Besoin</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/formDons">Dons</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </nav>
  <!-- partial -->
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-sm-12">
          <div class="home-tab">
            <div class="d-sm-flex align-items-center justify-content-between border-bottom">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active ps-0" id="besoin-tab" data-bs-toggle="tab" href="#besoin" role="tab" aria-controls="besoin" aria-selected="true">Besoins</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link ps-0" id="dons-tab" data-bs-toggle="tab" href="#dons" role="tab" aria-controls="dons" aria-selected="false">Dons</a>
                </li>
              </ul>
            </div>
            <div class="tab-content tab-content-basic">
              <div class="tab-pane fade show active" id="besoin" role="tabpanel" aria-labelledby="besoin-tab">
                <div class="row">
                  <div class="col-lg-8 d-flex flex-column">
                    <div class="row flex-grow">
                      <div class="col-12 grid-margin stretch-card">

                        <!-- Ajout d'une liste de ville avec leurs besoin-->
                        <div class="card">
                          <div class="card-body">
                            <h4 class="card-title">Liste des villes avec leurs besoin</h4>
                            <div class="table-responsive">
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>Ville</th>
                                    <th>Besoin</th>
                                    <th>Quantite</th>
                                    <th>P.U</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php if (isset($besoinVilles)) {
                                    for ($i = 0; $i < count($besoinVilles); $i++) {
                                      $besoinVille = $besoinVilles[$i]; ?>


                                      <tr>
                                        <td><?= $besoinVille['ville'] ?></td>
                                        <td><?= $besoinVille['besoin'] ?></td>
                                        <td class="text-success"> <?= $besoinVille['quantite'] ?></td>
                                        <td><label class="badge badge-info"><?= $besoinVille['pu'] ?></label></td>
                                      </tr>
                                    <?php }
                                  } else { ?>
                                    <tr>
                                      <td>Ville 1</td>
                                      <td>Argent</td>
                                      <td class="text-danger"> 100 <i class="ti-arrow-down"></i></td>
                                      <td><label class="badge badge-danger">Pending</label></td>
                                    </tr>
                                  <?php } ?>
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

              <div class="tab-pane fade" id="dons" role="tabpanel" aria-labelledby="dons-tab">
                <div class="row">
                  <div class="col-lg-8 d-flex flex-column">
                    <div class="row flex-grow">
                      <div class="col-12 grid-margin stretch-card">

                        <!-- Ajout d'une liste de ville avec leurs besoin-->
                        <div class="card">
                          <div class="card-body">
                            <h4 class="card-title">Liste des villes avec leurs besoin</h4>
                            <div class="table-responsive">
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>Ville</th>
                                    <th>Besoin</th>
                                    <th>Quantite</th>
                                    <th>P.U</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php if (isset($besoinVilles)) {
                                    for ($i = 0; $i < count($besoinVilles); $i++) {
                                      $besoinVille = $besoinVilles[$i]; ?>


                                      <tr>
                                        <td><?= $besoinVille['ville'] ?></td>
                                        <td><?= $besoinVille['besoin'] ?></td>
                                        <td class="text-success"> <?= $besoinVille['quantite'] ?></td>
                                        <td><label class="badge badge-info"><?= $besoinVille['pu'] ?></label></td>
                                      </tr>
                                    <?php }
                                  } else { ?>
                                    <tr>
                                      <td>Ville 1</td>
                                      <td>Argent</td>
                                      <td class="text-danger"> 100 <i class="ti-arrow-down"></i></td>
                                      <td><label class="badge badge-danger">Pending</label></td>
                                    </tr>
                                  <?php } ?>
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

              <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="row">
                    <div class="col-lg-12 grid-margin stretch-card">
                      <div class="card">
                        <div class="card-body">
                          <h4 class="card-title">Nombre de sinistres par ville</h4>
                          <canvas id="sinistreChart"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <footer class="footer">
      <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash.</span>
        <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2023. All rights reserved.</span>
      </div>
    </footer>
    <!-- partial -->
  </div>
  <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/chart.js/chart.umd.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/vendors/progressbar.js/progressbar.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/off-canvas.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/template.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/settings.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/hoverable-collapse.js"></script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/jquery.cookie.js" type="text/javascript"></script>

  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/dashboard.js"></script>
  <script nonce="<?= $csp_nonce ?>">
    var sinistreChartData = <?= json_encode($sinistreChartData ?? []) ?>;
  </script>
  <script nonce="<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/my_script.js"></script>
  <script nonce="<?= $csp_nonce ?>">
    (function() {
      const addBtn = document.getElementById('addDonRow');
      const rowsContainer = document.getElementById('donRows');
      addBtn && addBtn.addEventListener('click', function() {
        const row = document.querySelector('.don-row').cloneNode(true);
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelectorAll('select').forEach(s => {
          if (s.options.length > 0) s.selectedIndex = 0;
        });
        rowsContainer.appendChild(row);
      });
    })();
  </script>
  <!-- <script nonce = "<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/Chart.roundedBarCharts.js"></script> -->
  <!-- End custom js for this page-->
</body>

</html>