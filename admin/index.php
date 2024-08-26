<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Admin | Dashboard</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />
    <?php include_once dirname(__DIR__) . '/global/partials/scriptsCSS.php'; ?>
</head>


<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once (__DIR__) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once (__DIR__) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Dashboard Administrador</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Bienvenido</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Productos</span>
                                                <h2 class="mb-0 mt-1" id="products"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-info font-weight-bold font-size-23">
                                                    <i class='bx bx-grid-alt fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Empresas</span>
                                                <h2 class="mb-0 mt-1" id="companies"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-danger font-weight-bold font-size-13">
                                                    <i class='bx bxs-buildings fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios</span>
                                                <h2 class="mb-0 mt-1" id="users"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-user"></div>
                                                <span class="text-info font-weight-bold font-size-13">
                                                    <i class='bx bxs-user-detail fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios en Sesión</span>
                                                <h2 class="mb-0 mt-1" id="usersSession"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-visitor"></div>
                                                <span class="text-success font-weight-bold font-size-13">
                                                    <i class='bx bxs-user-plus fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2-->
                    </div>
                </div>
                <!-- <script src="/app/js/dashboard/indicatorsGeneral.js"></script> -->
                <!-- <script src="/app/js/dashboard/graphicsGeneral.js"></script> -->
                <script src="/admin/js/dashboard/dashboardIndicatorsGeneral.js"></script>
            </div>
        </div>
        <!-- main content End -->

        <!-- footer -->
        <?php include_once  dirname(__DIR__) . '/global/partials/footer.php'; ?>

    </div>
    <!-- Page End -->

    <?php include_once dirname(__DIR__) . '/global/partials/scriptsJS.php'; ?>

    <script src="/global/js/global/logout.js"></script>

</body>

</html>