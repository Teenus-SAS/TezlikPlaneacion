<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<!-- < ?php require_once dirname(dirname(__DIR__)) . '/modals/deliverMaterial.php'; ?> -->

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="Teenus SAS">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TezlikPlanner | Store</title>
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
            <!-- Content -->
            <div class="page-content">
                <!-- Page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Almacén</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Entrega y Recibido de Materia Prima y Producto Terminado</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end cardOC">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-success" id="btnExportStore" data-toggle="tooltip" title="Exportar" style="height: 39px"><i class="fas fa-file-excel fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="receiveOC" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-dolly mr-1"></i>Ordenes de Compra (Recibir)
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="deliverOC" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-dolly-flatbed mr-1"></i>Ordenes de Producción (Entregar)
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="receiveOP" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="fas fa-dolly-flatbed mr-1"></i>Ordenes de Producción (Recibir)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="tab-pane cardOC" role="tabpanel" aria-labelledby="pills-activity-tab">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblStore"></table>
                                            </div>
                                        </div>

                                        <!-- Pestaña Ordenes de Producción (Recibir) con sub-pestañas -->
                                        <div class="tab-pane cardOP" id="tabReceiveOP" style="display: none;" role="tabpanel" aria-labelledby="pills-projects-tab">
                                            <!-- Sub-pestañas para Productos Terminados y Devolución Materia Prima -->
                                            <ul class="nav nav-tabs" id="subTabReceiveOP" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="tabPT" data-toggle="pill" href="#panePT" role="tab" aria-controls="panePT" aria-selected="true">
                                                        Productos Terminados
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="tabMP" data-toggle="pill" href="#paneMP" role="tab" aria-controls="paneMP" aria-selected="false">
                                                        Devolución Materia Prima
                                                    </a>
                                                </li>
                                            </ul>

                                            <!-- Contenido de las sub-pestañas -->
                                            <div class="tab-content mt-3">
                                                <div class="tab-pane fade show active" id="panePT" role="tabpanel" aria-labelledby="tabPT">
                                                    <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryPT"></table>
                                                </div>
                                                <div class="tab-pane fade" id="paneMP" role="tabpanel" aria-labelledby="tabMP">
                                                    <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryMP"></table>
                                                </div>
                                            </div>
                                        </div> <!-- /tab-pane cardOP -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/store/tblStore.js"></script>
    <script src="/planning/js/store/store.js"></script>
    <script src="/planning/js/store/exportStore.js"></script>
    <script src="/planning/js/productionOrder/tblProductionOrderPartial.js"></script>
</body>


</html>