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
          <div class="col-lg-8">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Créer des Dons</h4>
                <!-- Model form: manage donation models (name + type) -->
                <form action="<?= BASE_URL ?>/dons/add-multiple" method="POST" id="multiDonsForm">
                  <div id="donRows">
                    <div class="don-row mb-3 row">
                      <div class="col-5">
                        <label>Modèle de don</label>
                        <select name="modele[]" class="form-control modele-select" required>
                          <option value="">-- Choisir un modèle --</option>
                          <?php if (!empty($modelesAll)) { foreach ($modelesAll as $m) { ?>
                            <option value="<?= htmlspecialchars($m['id']) ?>" data-type="<?= htmlspecialchars($m['idTypeDons'] ?? $m['idType'] ?? '') ?>"><?= htmlspecialchars($m['nom']) ?></option>
                          <?php } } ?>
                        </select>
                        <input type="hidden" name="type[]" class="modele-inferred-type" value="">
                      </div>
                      <div class="col-2">
                        <label>Quantité</label>
                        <input type="number" name="quantite[]" class="form-control" required>
                      </div>
                      <div class="col-2">
                        <label>Prix U.</label>
                        <input type="number" step="0.01" name="prix[]" class="form-control">
                      </div>
                      <div class="col-3">
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
      // Event delegation: update hidden type[] when a modele is selected
      document.addEventListener('change', function(e) {
        if (!e.target) return;
        if (e.target.classList && e.target.classList.contains('modele-select')) {
          const sel = e.target;
          const row = sel.closest('.don-row');
          const hidden = row && row.querySelector('.modele-inferred-type');
          const opt = sel.options[sel.selectedIndex];
          if (hidden) hidden.value = opt ? (opt.dataset.type || '') : '';
        }
      });

      addBtn && addBtn.addEventListener('click', function() {
        const template = document.querySelector('.don-row');
        if (!template) return;
        const row = template.cloneNode(true);
        // clear inputs/selects
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelectorAll('select').forEach(s => { if (s.options.length > 0) s.selectedIndex = 0; });
        // ensure hidden type cleared
        row.querySelectorAll('.modele-inferred-type').forEach(h => h.value = '');
        rowsContainer.appendChild(row);
      });
    })();
  </script>
  <!-- <script nonce = "<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/Chart.roundedBarCharts.js"></script> -->
  <!-- End custom js for this page-->
</body>

</html>