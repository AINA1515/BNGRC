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

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/perfect-scrollbar.min.css" />
</head>

<body class="with-welcome-text">
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="icon-menu"></span>
                    </button>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/" class="navbar-brand brand-logo">ReliefFlow</a>

                    <a href="<?= BASE_URL ?>/" class="navbar-brand brand-logo-mini" href="<?= BASE_URL ?>/">
                        RF
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top">
                <ul class="navbar-nav">
                    <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Bonjour</h1>
                        <h3 class="welcome-sub-text">Statistiques des collectes et distributions de dons pour les sinistrés. </h3>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
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
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/simulation">
                            <i class="mdi mdi-playlist-play menu-icon"></i>
                            <span class="menu-title">Simulation</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="home-tab">
                                <div class="tab-content tab-content-basic">
                                    <div class="tab-pane fade show active" id="besoin" role="tabpanel" aria-labelledby="besoin-tab">
                                        <div class="row">
                                            <div class="col-lg-8 d-flex flex-column">
                                                <div class="row flex-grow">
                                                    <div class="col-12 grid-margin stretch-card">

                                                        <div class="content-wrapper">
                                                            <div class="row justify-content-center mt-5">
                                                                <div class="col-lg-6">
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <h4 class="card-title">Ajouter un Besoin Ville</h4>
                                                                            <?php
                                                                                $inserted = isset($_GET['inserted']) ? (int)$_GET['inserted'] : null;
                                                                                $skipped = isset($_GET['skipped']) ? (int)$_GET['skipped'] : null;
                                                                                $error = isset($_GET['error']) ? $_GET['error'] : null;
                                                                                if ($inserted !== null || $skipped !== null || $error) {
                                                                                    echo '<div class="mb-3">';
                                                                                    if ($error) {
                                                                                        echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($error) . '</div>';
                                                                                    }
                                                                                    if ($inserted !== null) {
                                                                                        echo '<div class="alert alert-success">Insérés: ' . $inserted . '</div>';
                                                                                    }
                                                                                    if ($skipped !== null && $skipped > 0) {
                                                                                        echo '<div class="alert alert-warning">Ignorés: ' . $skipped . ' (voir logs pour détails)</div>';
                                                                                        // show last log lines for quick debug
                                                                                        $logFile = '/tmp/besoin_insert.log';
                                                                                        if (file_exists($logFile)) {
                                                                                            $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                                                                                            $last = array_slice($lines, -30);
                                                                                            echo '<pre style="max-height:300px;overflow:auto;background:#f8f9fa;padding:10px;border:1px solid #ddd;">' . htmlspecialchars(implode("\n", $last)) . '</pre>';
                                                                                        } else {
                                                                                            echo '<div class="small text-muted">Log file not found: ' . htmlspecialchars($logFile) . '</div>';
                                                                                        }
                                                                                    }
                                                                                    echo '</div>';
                                                                                }
                                                                            ?>
                                                                            <form action="<?= BASE_URL ?>/besoinVille/add-multiple" method="POST" id="multiBesoinForm">
                                                                                <div class="form-group">
                                                                                    <label for="ville">Ville</label>
                                                                                    <select class="form-control" id="ville" name="ville" required>
                                                                                        <option value="">-- Choisir une ville --</option>
                                                                                        <?php if (!empty($villes)) { foreach ($villes as $v) { ?>
                                                                                            <option value="<?= htmlspecialchars($v['id']) ?>"><?= htmlspecialchars($v['nom']) ?></option>
                                                                                        <?php } } ?>
                                                                                    </select>
                                                                                </div>

                                                                                <div id="besoinRows">
                                                                                    <div class="besoin-row mb-3 row">
                                                                                        <div class="col-5">
                                                                                            <label>Don</label>
                                                                                            <select name="modeleDon[]" class="form-control" required>
                                                                                                <option value="">-- Choisir un don --</option>
                                                                                                <?php if (!empty($modelesAll)) { foreach ($modelesAll as $m) { ?>
                                                                                                    <option value="<?= htmlspecialchars($m['id']) ?>"><?= htmlspecialchars($m['nom']) ?></option>
                                                                                                <?php } } ?>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-2">
                                                                                            <label>Quantité</label>
                                                                                            <input type="number" name="quantite[]" class="form-control" required>
                                                                                        </div>
                                                                                        <div class="col-2">
                                                                                            <label>P.U</label>
                                                                                            <input type="number" step="0.01" name="pu[]" class="form-control">
                                                                                        </div>
                                                                                        <div class="col-3">
                                                                                            <label>Date</label>
                                                                                            <input type="date" name="date[]" class="form-control" required>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="d-flex gap-2">
                                                                                    <button type="button" class="btn btn-secondary" id="addBesoinRow">Ajouter un don</button>
                                                                                    <button type="submit" class="btn btn-primary">Enregistrer les besoins</button>
                                                                                </div>
                                                                            </form>
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
        (function(){
            const addBtn = document.getElementById('addBesoinRow');
            const rowsContainer = document.getElementById('besoinRows');
            addBtn && addBtn.addEventListener('click', function(){
                const row = document.querySelector('.besoin-row').cloneNode(true);
                // clear values
                    row.querySelectorAll('input').forEach(i=>{ i.value=''; });
                    row.querySelectorAll('input[type="date"]').forEach(d=>d.value='');
                row.querySelectorAll('select').forEach(s=>{ if (s.options.length>0) s.selectedIndex=0; });
                rowsContainer.appendChild(row);
            });
        })();
    </script>
    <!-- <script nonce = "<?= $csp_nonce ?>" src="<?= BASE_URL ?>/assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
</body>

</html>