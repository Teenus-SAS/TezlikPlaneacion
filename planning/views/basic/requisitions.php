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
    <title>TezlikPlanner | Requisitions</title>
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
                <div class="page-title-box" style="padding-bottom: 10px;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-5 col-xl-7">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-file-upload mr-1"></i>Compras</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Requisiciones para Materia Prima</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-5">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-info">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: x-small;"><i class="bi bi-exclamation-circle mr-1" id="lblPending"></i> </span>
                                                        <!-- <h2 class="mb-0 mt-1 text-white text-center" style="font-size: large;"><i class="bi bi-exclamation-circle mr-1"></i>3</h2> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-danger">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: x-small;"><i class="bi bi-hourglass-bottom mr-1" id="lblDelayed"></i> </span>
                                                        <!-- <h2 class="mb-0 mt-1 text-white text-center" style="font-size: large;"><i class="bi bi-hourglass-bottom mr-1"></i>10</h2> -->
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-warning">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: x-small;"><i class="bi bi-arrow-clockwise mr-1" id="lblProcess"></i></span>
                                                        <!-- <h2 class="mb-0 mt-1 text-white text-center" style="font-size: large;"><i class="bi bi-arrow-clockwise mr-1"></i>2</h2> -->
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-success">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: x-small;"><i class="bi bi-hourglass-bottom mr-1" id="lblReceived"></i> </span>
                                                        <!-- <h2 class="mb-0 mt-1 text-white text-center" style="font-size: large;"><i class="bi bi-hourglass-bottom mr-1"></i>10</h2> -->
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

                <div class="page-content-wrapper mt--45 mb-5">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-7 col-xl-12 mt-4 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewRequisition" name="btnNewRequisition"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewRequisitions" name="btnNewImportRequisitions"><i class="bi bi-cloud-arrow-up"></i></button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-success" id="btnOpenSearchDate" name="btnOpenSearchDate"> <i class="bi bi-search"></i></button>
                                </div>
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
                                                    <button type="text" class="btn btn-info" id="btnSearchDate">Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddRequisitionsMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddRequisitionMaterials">
                                            <div class="form-row">
                                                <div class="col-sm-3 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="refMaterial">Referencia</label>
                                                    <select class="form-control refMaterial" name="refMaterial" id="refMaterial"></select>
                                                </div>
                                                <div class="col-sm-9 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="">Materia Prima</label>
                                                    <select class="form-control material" name="idMaterial" id="material"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="refMaterial">Referencia</label>
                                                    <input type="text" class="form-control" id="referenceMName" name="referenceMName" readonly>
                                                </div>
                                                <div class="col-sm-10 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="">Materia Prima</label>
                                                    <input type="text" class="form-control" id="materialName" name="materialName" readonly>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Proveedor</label>
                                                    <select class="form-control client" name="idProvider" id="clientMP"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Precio de Venta</label>
                                                    <input class="form-control text-center" type="text" id="priceSaleMP" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Cantidad Min Venta</label>
                                                    <input class="form-control text-center" type="text" id="rMQuantity" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Tiempo Prom Entrega (días)</label>
                                                    <input class="form-control text-center" type="number" id="rMAverage" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Fecha Solicitud</label>
                                                    <input class="form-control" type="date" name="applicationDate" id="applicationDateMP" min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Fecha Entrega</label>
                                                    <input class="form-control" type="date" name="deliveryDate" id="deliveryDateMP">
                                                </div>
                                                <!-- <div class="w-100"></div> -->
                                                <div class="col-sm-2 cardRequired floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Cantidad Requerida</label>
                                                    <input class="form-control text-center" type="text" name="requiredQuantity" id="requiredQuantityMP" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Cantidad Solicitada</label>
                                                    <input class="form-control text-center" type="number" name="requestedQuantity" id="requestedQuantityMP">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Orden de Compra</label>
                                                    <input class="form-control text-center" type="text" name="purchaseOrder" id="purchaseOrderMP">
                                                </div>
                                                <div class="col-sm" style="margin-top:12px">
                                                    <button class="btn btn-success" id="btnAddRequisitionMP">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddRequisitionsProducts">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddRequisitionProducts">
                                            <div class="form-row">
                                                <div class="col-sm-3 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="refProduct">Referencia</label>
                                                    <select class="form-control" name="refProduct" id="refProduct"></select>
                                                </div>
                                                <div class="col-sm-9 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="selectNameProduct">Producto</label>
                                                    <select class="form-control" name="idProduct" id="selectNameProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="refProduct">Referencia</label>
                                                    <input type="text" class="form-control" id="referencePName" name="referencePName" readonly>
                                                </div>
                                                <div class="col-sm-10 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="selectNameProduct">Producto</label>
                                                    <input type="text" class="form-control" id="productName" name="productName" readonly>
                                                </div>
                                                <div class="col-sm-8 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Proveedor</label>
                                                    <select class="form-control client" name="idProvider" id="clientPT"></select>
                                                </div>
                                                <!-- <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Cantidad Min Venta</label>
                                                    <input class="form-control text-center" type="text" id="rMQuantity" readonly>
                                                </div> -->
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Tiempo Prom Entrega (días)</label>
                                                    <input class="form-control text-center" type="number" id="ptAverage" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Fecha Solicitud</label>
                                                    <input class="form-control" type="date" name="applicationDate" id="applicationDatePT" min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Fecha Entrega</label>
                                                    <input class="form-control" type="date" name="deliveryDate" id="deliveryDatePT">
                                                </div>
                                                <!-- <div class="w-100"></div> -->
                                                <div class="col-sm-2 cardRequired floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Cantidad Requerida</label>
                                                    <input class="form-control text-center" type="text" name="requiredQuantity" id="requiredQuantityPT" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Cantidad Solicitada</label>
                                                    <input class="form-control text-center" type="number" name="requestedQuantity" id="requestedQuantityPT">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Orden de Compra</label>
                                                    <input class="form-control text-center" type="text" name="purchaseOrder" id="purchaseOrderPT">
                                                </div>
                                                <div class="col-sm" style="margin-top:12px">
                                                    <button class="btn btn-success" id="btnAddRequisitionPT">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportRequisitions">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportRequisitions" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formRequisitions">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileRequisitions" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Requisiciones</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportRequisitions">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsRequisitions">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="pending" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="bi bi-clock-history mr-1" id="pending-icon"></i>Pendiente
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="done" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-check-square-fill mr-1" id="done-icon"></i>Ejecutado
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="tab-pane cardProductsMaterials"> -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblRequisitions">
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Total: </td>
                                                        <td id="lblTotalQRequired"></td>
                                                        <td id="lblTotalQRequested"></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- </div> -->
                                    <!-- <div class="tab-pane cardPlanCicles" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblRequisitions">

                                                </table>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->


    <script>
        viewRawMaterial = 2;
    </script>
    <script src="/planning/js/admin/clients/configClients.js"></script>
    <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/planning/js/basic/requisitions/tblRequisitions.js"></script>
    <script src="/planning/js/basic/requisitions/requisitions.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/requisitions/importRequisitions.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>