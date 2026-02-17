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
                                Ajouter un Besoin
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/formDons">
                                Faire un Don
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/formModelDons">
                                Model Don
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/simulation">
                                <i class="mdi mdi-playlist-play menu-icon"></i>
                                <span class="menu-title">Simulation</span>
                            </a>
            </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/distribution">
                                <i class="mdi mdi-cash-multiple menu-icon"></i>
                                <span class="menu-title">Distribution</span>
                            </a>
                        </li>
        </ul>
    </nav>