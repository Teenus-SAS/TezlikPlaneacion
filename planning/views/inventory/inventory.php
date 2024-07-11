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
    <meta name="author" content="MatrrDigital">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Planning | Inventory</title>
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
                <div class="page-title-box" style="padding-bottom: 15px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Inventarios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Análisis de inventario</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-7">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-info">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-exclamation-circle mr-1" id="lblTotal"></i></span>
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
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-arrow-clockwise mr-1" id="lblRotation"></i></span>
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
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-hourglass-bottom mr-1" id="lblCoverage"></i> </span>
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
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-hourglass-bottom mr-1" id="lblAvailable"></i> </span>
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
                        <div class="row">
                            <div class="col-sm-7 col-xl-12 mt-4 form-inline justify-content-sm-end">
                                <?php if ($_SESSION['inventory_abc'] == '1') { ?>
                                    <div class="col-xs-4 cardBtnAddMonths  mr-2">
                                        <button class="btn btn-warning" id="btnInvetoryABC" name="btnInvetoryABC"><i class="bi bi-folder mr-1"></i>Reclasificación Inv ABC</button>
                                    </div>
                                <?php } ?>
                                <!-- <div class="col-xs-2">
                                    <button class="btn btn-info" id="btnImportNewInventory"><i class="bi bi-cloud-download mr-1"></i></button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddMonths">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formInventoryABC">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="cantMonths">Meses a Analizar:</label>
                                                    <input type="number" class="form-control text-center" id="cantMonths" name="cantMonths" style="width:200px" value="<?php echo $_SESSION['months'] ?>">
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnAddMonths">Calcular</button>
                                                </div>
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
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" page-content-wrapper mt--45 mb-5 cardImportInventory">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportInventory" enctype=" multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formInventory">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileInventory" accept=" .xls,.xlsx">
                                                    <label class="form-label"> Importar Inventario</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportInventory">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsInventory">Descarga Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardInventoryABC">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formInventoryABC">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- page content -->
                <!-- <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                                                <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row justify-content-around">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblInventories">

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
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">

                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="products" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-cube mr-1"></i>Inventario Productos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="materials" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="fas fa-flask mr-1"></i>Inventario Materia Prima
                                        </a>
                                    </li>
                                </ul>


                            </div>
                        </div>
                        <!-- <div class="row cardTblInventoryABC" style="display: none;">
                            <div class="col-12">
                                <div class="card"> 
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblInventoryABC">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="pills-activity-tab">
                                            <div class="table-responsive">
                                                <div class="table-responsive">
                                                    <table class="fixed-table-loading table table-hover" id="tblInventories">

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
        </div>
        <!-- main content End -->
        <!-- footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/general/inventoryABC/tblinventoryABC.js"></script>
    <script src="/planning/js/general/inventoryABC/inventoryABC.js"></script>
    <script src="../planning/js/inventory/tblInventory.js"></script>
    <script src="../planning/js/inventory/inventory.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="../planning/js/inventory/importInventory.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>