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
                                    <h3 class="mb-1 font-weight-bold text-dark">Almacen</h3>
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

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Navigation Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <!-- Ordenes de Compra -->
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="receiveOC" data-toggle="pill" href="#tabOrdersCompra" role="tab" aria-controls="tabOrdersCompra" aria-selected="true">
                                            <i class="fas fa-dolly mr-1"></i>Ordenes de Compra
                                        </a>
                                    </li>
                                    <!-- Ordenes de Producción -->
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="productionOrders" data-toggle="pill" href="#tabOrdersProduction" role="tab" aria-controls="tabOrdersProduction" aria-selected="false">
                                            <i class="fas fa-dolly-flatbed mr-1"></i>Ordenes de Producción
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3">
                            <!-- Tab Ordenes de Compra -->
                            <div class="tab-pane fade show active" id="tabOrdersCompra" role="tabpanel" aria-labelledby="receiveOC">
                                <div class="table-responsive">
                                    <table class="fixed-table-loading table table-hover" id="tblStore">
                                        <!-- Contenido de la tabla tblStore -->
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Ordenes de Producción -->
                            <div class="tab-pane fade" id="tabOrdersProduction" role="tabpanel" aria-labelledby="productionOrders">
                                <ul class="nav nav-pills mb-3" id="pills-orders-production" role="tablist">
                                    <!-- Recibir -->
                                    <li class="nav-item">
                                        <a class="nav-link active" id="receiveProduction" data-toggle="pill" href="#tabReceiveProduction" role="tab" aria-controls="tabReceiveProduction" aria-selected="true">
                                            Recibir
                                        </a>
                                    </li>
                                    <!-- Entregar -->
                                    <li class="nav-item">
                                        <a class="nav-link" id="deliverProduction" data-toggle="pill" href="#tabDeliverProduction" role="tab" aria-controls="tabDeliverProduction" aria-selected="false">
                                            Entregar
                                        </a>
                                    </li>
                                </ul>
                                <!-- Contenido de las pestañas internas de Ordenes de Producción -->
                                <div class="tab-content">
                                    <!-- Sub-tab Recibir -->
                                    <div class="tab-pane fade show active" id="tabReceiveProduction" role="tabpanel" aria-labelledby="receiveProduction">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblStore">
                                                <!-- Contenido de la tabla tblStore para Recibir -->
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Sub-tab Entregar -->
                                    <div class="tab-pane fade" id="tabDeliverProduction" role="tabpanel" aria-labelledby="deliverProduction">
                                        <ul class="nav nav-pills mb-3" id="pills-deliver-production" role="tablist">
                                            <!-- Productos Terminados -->
                                            <li class="nav-item">
                                                <a class="nav-link active" id="finishedProducts" data-toggle="pill" href="#tabFinishedProducts" role="tab" aria-controls="tabFinishedProducts" aria-selected="true">
                                                    Productos Terminados
                                                </a>
                                            </li>
                                            <!-- Devolución Materia Prima -->
                                            <li class="nav-item">
                                                <a class="nav-link" id="rawMaterialReturn" data-toggle="pill" href="#tabRawMaterialReturn" role="tab" aria-controls="tabRawMaterialReturn" aria-selected="false">
                                                    Devolución Materia Prima
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- Sub-sub-tab Productos Terminados -->
                                            <div class="tab-pane fade show active" id="tabFinishedProducts" role="tabpanel" aria-labelledby="finishedProducts">
                                                <div class="table-responsive">
                                                    <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryPT">
                                                        <!-- Contenido de la tabla tblPartialsDeliveryPT -->
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Sub-sub-tab Devolución Materia Prima -->
                                            <div class="tab-pane fade" id="tabRawMaterialReturn" role="tabpanel" aria-labelledby="rawMaterialReturn">
                                                <div class="table-responsive">
                                                    <table class="fixed-table-loading table table-hover" id="tblPartialsDeliveryMP">
                                                        <!-- Contenido de la tabla tblPartialsDeliveryMP -->
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- End of Ordenes de Producción Tab -->
                        </div> <!-- End of Tab Content -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content end -->

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