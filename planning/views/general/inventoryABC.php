<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');
?>
<?php require_once dirname(dirname(__DIR__)) . '/modals/createSale.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
    <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Planning | Inventory ABC</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">

    <!-- Begin Page -->
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
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Inventario ABC</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-secondary" id="btnNewInventoryABC" name="btnNewInventoryABC" style="display: none;">Dias de Ventas</button>
                                </div>
                                <!--<div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewinventory_abc">Importar Venta</button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardInventoryABC">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formInventoryABC">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:10px!important">
                                                    <input type="number" class="text-center form-control" name="a" id="a" max="100">
                                                    <label for="">a</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:10px!important">
                                                    <input type="number" class="text-center form-control" name="b" id="b" max="100">
                                                    <label for="">b</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:10px!important">
                                                    <input type="number" class="text-center form-control" name="c" id="c" max="100">
                                                    <label for="">c</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:7px">
                                                    <button class="btn btn-success" id="btnSaveInventoryABC">Guardar</button>
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
                                            <table class="fixed-table-loading table-hover" id="tblInventoryABC">

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
    <script>
        inventory_abc = "<? $_SESSION['inventory_abc'] ?>";
    </script>
    <script src="/planning/js/general/inventoryABC/tblinventoryABC.js"></script>
    <script src="/planning/js/general/inventoryABC/inventoryABC.js"></script>
</body>

</html>