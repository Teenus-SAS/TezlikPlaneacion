<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');

$fechaActual = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Planning | Offices</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">

    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(__DIR__)) . '/partials/nav.php'; ?>

        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-2">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Despachos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Listado de Despachos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-10 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mt-3">
                                    <button class="btn btn-warning" id="btnOpenSearchDate" name="btnOpenSearchDate">Buscar Fecha</button>
                                </div>
                                <!-- <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewOffices">Importar Despachos</button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardSearchDate">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formSearchDate">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-2">
                                                    <label for="firtsDate" class="form-label text-dark">Fecha Inicial</label>
                                                    <input class="form-control dateOrders" id="firtsDate" type="date">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="lastDate" class="form-label text-dark">Fecha Final</label>
                                                    <input class="form-control dateOrders" id="lastDate" type="date">
                                                </div>
                                                <div class="col-xs-2" style="margin-top:33px">
                                                    <button type="text" class="btn btn-info" id="btnSearchDate">Buscar Fecha</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- page content -->
                <!-- <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblOffices">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="materials" data-toggle="pill" href="#pills-activity" role="tab" aria-controls="pills-activity" aria-selected="true">
                                        <i class="bi bi-truck mr-1"></i>x Entregar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="planCicles" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-check-circle mr-1"></i>Entregado
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="card-header">
                                        <h5 class="card-title">Materias Primas</h5>
                                    </div> -->
                                    <div class="tab-pane cardProductsMaterials">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblOffices">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane cardPlanCicles" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblOffices">

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
        </div>
        <!-- main content End -->
        <!-- footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <!-- <script src="/planning/js/basic/products/configProducts.js"></script> -->
    <!-- <script src="/planning/js/admin/clients/configClients.js"></script> -->
    <script src="/planning/js/offices/configOffices.js"></script>
    <script src="/planning/js/offices/tblOffices.js"></script>
    <script src="/planning/js/offices/offices.js"></script>
    <!-- <script src="../global/js/import/import.js"></script> -->
    <!-- <script src="../planning/js/offices/importOffices.js"></script> -->
    <!-- <script src="../global/js/import/file.js"></script> -->
</body>

</html>