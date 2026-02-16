<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RelifFlow</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="/assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="/assets/images/favicon.png" />

    <link rel="stylesheet" href="path-to/node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" />
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
                    <a href="/" class="navbar-brand brand-logo">ReliefFlow</a>

                    <a href="/" class="navbar-brand brand-logo-mini" href="/">
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
                        <a class="nav-link" href="/">
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
                                <li class="nav-item"><a class="nav-link" href="/formBesoin">Besoin</a></li>
                                <li class="nav-item"><a class="nav-link" href="/formDons">Dons</a></li>
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
                                                                            <form action="/besoinVille/add" method="POST">
                                                                                <div class="form-group">
                                                                                    <label for="ville">Ville</label>
                                                                                    <input type="text" class="form-control" id="ville" name="ville" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="besoin">Besoin</label>
                                                                                    <input type="text" class="form-control" id="besoin" name="besoin" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="quantite">Quantité</label>
                                                                                    <input type="number" class="form-control" id="quantite" name="quantite" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="pu">Prix Unitaire</label>
                                                                                    <input type="number" class="form-control" id="pu" name="pu" step="0.01" required>
                                                                                </div>
                                                                                <button type="submit" class="btn btn-primary mt-3">Ajouter</button>
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
    <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/js/vendor.bundle.base.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/chart.js/chart.umd.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/off-canvas.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/template.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/settings.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/hoverable-collapse.js"></script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/jquery.cookie.js" type="text/javascript"></script>

    <script nonce="<?= $csp_nonce ?>" src="/assets/js/dashboard.js"></script>
    <script nonce="<?= $csp_nonce ?>">
        var sinistreChartData = <?= json_encode($sinistreChartData ?? []) ?>;
    </script>
    <script nonce="<?= $csp_nonce ?>" src="/assets/js/my_script.js"></script>
    <!-- <script nonce = "<?= $csp_nonce ?>" src="/assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
</body>

</html>