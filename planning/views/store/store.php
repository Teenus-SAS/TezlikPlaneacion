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
                        <!-- Pestañas principales -->
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="mainTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation active" id="tabOC" data-toggle="pill" href="#paneOC" role="tab" aria-controls="paneOC" aria-selected="true">
                                            <i class="fas fa-dolly mr-1"></i>Ordenes de Compra
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="tabOP" data-toggle="pill" href="#paneOP" role="tab" aria-controls="paneOP" aria-selected="false">
                                            <i class="fas fa-dolly-flatbed mr-1"></i>Ordenes de Producción
                                        </a>
                                    </li>
                                </ul>

                                <!-- Contenido de las pestañas principales -->
                                <div class="tab-content m-0 pt-0" id="mainTabContent">
                                    <!-- Ordenes de Compra -->
                                    <div class="tab-pane fade show active" id="paneOC" role="tabpanel" aria-labelledby="tabOC">
                                        <div class="card">
                                            <div class="card-body p-0">
                                                <!-- Ordenes de Compra -->
                                                <div class="tab-pane fade show active" id="paneReceiveOC" role="tabpanel" aria-labelledby="subTabReceiveOC">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="fixed-table-loading table table-hover" id="tblReceiveOC"></table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <ul class="nav nav-tabs mt-3 ml-3" id="subTabOC" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active selectNavigation" id="subTabReceiveOC" data-toggle="pill" href="#paneReceiveOC" role="tab" aria-controls="paneReceiveOC" aria-selected="false">
                                                            Recibir
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link selectNavigation" id="subTabDeliverOC" data-toggle="pill" href="#paneDeliverOC" role="tab" aria-controls="paneDeliverOC" aria-selected="true">
                                                            Entregar
                                                        </a>
                                                    </li>
                                                </ul> -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ordenes de Producción -->
                                    <div class="tab-pane fade" id="paneOP" role="tabpanel" aria-labelledby="tabOP">
                                        <div class="card">
                                            <div class="card-body">
                                                <!-- Sub-pestañas para Ordenes de Producción -->
                                                <ul class="nav nav-tabs mt-3 ml-3" id="subTabOP" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="subTabReceiveOP" data-toggle="pill" href="#paneReceiveOP" role="tab" aria-controls="paneReceiveOP" aria-selected="false">
                                                            Recibir
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="subTabDeliverOP" data-toggle="pill" href="#paneDeliverOP" role="tab" aria-controls="paneDeliverOP" aria-selected="true">
                                                            Entregar
                                                        </a>
                                                    </li>
                                                </ul>

                                                <!-- Contenido de las sub-pestañas de Ordenes de Producción -->
                                                <div class="tab-content" id="subTabOPContent">
                                                    <!-- Recibir -->
                                                    <div class="tab-pane fade show active" id="paneReceiveOP" role="tabpanel" aria-labelledby="subTabReceiveOP">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table class="fixed-table-loading table table-hover" id="tblDeliverOP"></table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Entregar -->
                                                    <div class="tab-pane fade" id="paneDeliverOP" role="tabpanel" aria-labelledby="subTabDeliverOP">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <!-- Sub-pestañas para Entregar -->
                                                                <ul class="nav nav-tabs" id="subTabDeliver" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="subTabPT" data-toggle="pill" href="#panePT" role="tab" aria-controls="panePT" aria-selected="true">
                                                                            Productos Terminados
                                                                        </a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="subTabMP" data-toggle="pill" href="#paneMP" role="tab" aria-controls="paneMP" aria-selected="false">
                                                                            Devolución Materia Prima
                                                                        </a>
                                                                    </li>
                                                                </ul>

                                                                <!-- Contenido de las sub-pestañas de Entregar -->
                                                                <div class="tab-content mt-3">
                                                                    <div class="tab-pane fade show active" id="panePT" role="tabpanel" aria-labelledby="subTabPT">
                                                                        <div class="table-responsive">
                                                                            <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryPT"></table>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="paneMP" role="tabpanel" aria-labelledby="subTabMP">
                                                                        <div class="table-responsive">
                                                                            <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryMP"></table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- /subTabOPContent -->
                                            </div> <!-- /card-body -->
                                        </div> <!-- /card -->
                                    </div> <!-- /paneOP -->
                                </div> <!-- /mainTabContent -->
                            </div> <!-- /col-12 -->
                        </div> <!-- /row -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script>
        op_to_store = "<?= $_SESSION['op_to_store'] ?>";
    </script>
    <script src="/planning/js/store/tblStore.js"></script>
    <script src="/planning/js/store/store.js"></script>
    <script src="/planning/js/store/exportStore.js"></script>
    <script src="/planning/js/productionOrder/tblProductionOrderPartial.js"></script>
</body>




</html>