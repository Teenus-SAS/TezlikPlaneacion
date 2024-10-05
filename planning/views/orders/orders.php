<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php //require_once dirname(dirname(__DIR__)) . '/modals/createOrders.php'; 
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
    <title>TezlikPlanner | Orders</title>
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
                <div class="page-title-box" style="padding-bottom: 10px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-clipboard-check mr-1"></i>Pedidos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Pedido</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-7">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-success">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-calendar-event mr-1" id="lblTotal"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-warning">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-file-earmark-x mr-1" id="lblCompleted"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-danger">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-arrow-down mr-1" id="lblLate"></i></span>
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
                                    <button class="btn btn-warning" id="btnNewOrder" name="btnNewOrder"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewOrder"><i class="bi bi-cloud-arrow-up"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddOrders">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateOrder">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="dateOrder">Fecha</label>
                                                    <input class="form-control text-center" type="date" name="dateOrder" id="dateOrder" min="<?php echo date('Y-m-d'); ?>" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="minDate">Fecha Min Entrega</label>
                                                    <input class="form-control text-center" type="date" name="minDate" id="minDate" min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2">
                                                    <label for="">Fecha Max Entrega</label>
                                                    <input class="form-control text-center" type="date" name="maxDate" id="maxDate" min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="refProduct">Referencia</label>
                                                    <select class="form-control" name="idProduct" id="refProduct"></select>
                                                </div>

                                                <div class="col-sm-6 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="selectNameProduct">Producto</label>
                                                    <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="">Cantidad Disponible Inv</label>
                                                    <input type="text" class="form-control text-center" id="inptQuantity" name="inptQuantity" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="originalQuantity">Cantidad requerida</label>
                                                    <input type="number" class="form-control text-center" id="originalQuantity" name="originalQuantity">
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Cliente</label>
                                                    <select class="form-control client" name="idClient" id="client"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show- mb-3 label">
                                                    <label for="">Vendedor</label>
                                                    <select class="form-control" name="idSeller" id="seller"></select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-success" id="btnCreateOrder">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportOrder">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportOrder" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formOrders">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileOrder" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Pedido</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportOrder">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsOrder">Descarga Formato</button>
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
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblOrder">

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
        <!-- main content End -->
        <!-- footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/admin/clients/configClients.js"></script>
    <script src="/planning/js/general/seller/configSellers.js"></script>
    <script src="../planning/js/orders/tblOrder.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="../planning/js/orders/orders.js"></script>
    <script src="../planning/js/orders/importOrder.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>