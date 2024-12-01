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
    <title>TezlikPlanner | Products</title>
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
                <div class="page-title-box" style="padding-bottom: 15px;">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="bi bi-archive mr-1"></i>Inventarios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Productos Terminados y Materia Prima</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-info">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-cube mr-1" id="lblTotal"></i> </span>
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
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-plus mr-1" id="lblAverage"></i> </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-primary">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-hourglass-bottom mr-1" id="lblConcentration"></i></span>
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
                        <div class="row cardProducts">
                            <div class="col-sm-7 col-xl-12 mt-4 form-inline justify-content-sm-end">
                                <!-- <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewProduct"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div> -->
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewProducts"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row cardMaterials" style="display: none;">
                            <div class="col-sm-7 col-xl-12 mt-4 form-inline justify-content-sm-end">
                                <!-- <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewMaterial"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div> -->
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewMaterials"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="page-content-wrapper mt--45 mb-5 cardCreateProduct">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateProduct">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="referenceProduct">Referencia</label>
                                                    <select class="form-control refProduct" name="idProduct" id="refProduct"></select>
                                                </div>
                                                <div class="col-sm-8 floating-label enable-floating-label show-label">
                                                    <label for="">Nombre Producto</label>
                                                    <select class="form-control selectNameProduct" name="idProduct" id="selectNameProduct"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Existencias</label>
                                                    <input type="number" class="form-control text-center" id="pQuantity" name="quantity">
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label">
                                                    <label for="formFile" class="form-label">Subir imagen</label>
                                                    <input class="form-control" type="file" id="formFile">
                                                </div>
                                                <div class="col-sm-2 mt-1">
                                                    <button type="text" class="btn btn-success" id="btnCreateProduct">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProducts">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProduct" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formProducts">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProducts" accept=".xls,.xlsx">
                                                <label for="fileProducts" class="form-label"> Importar</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProducts">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProducts">Descargar Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Materiales -->
                <div class="page-content-wrapper mt--45 mb-5 cardRawMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateMaterial">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <select class="form-control refMaterial" name="refMaterial" id="refMaterial"></select>
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Materia Prima</label>
                                                    <select class="form-control material" name="material" id="material"></select>
                                                </div>
                                                <?php if ($_SESSION['flag_products_measure'] == '1') { ?>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                        <label for="grammage">Gramaje</label>
                                                        <input type="number" class="form-control text-center" id="grammage" name="grammage">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Unidad</label>
                                                    <input type="text" class="form-control text-center" id="units" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="mQuantity">Existencias</label>
                                                    <input type="number" class="form-control text-center" id="mQuantity" name="quantity">
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-info" type="submit" id="btnCreateMaterial" name="btnCreateMaterial">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportMaterials" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formMaterials">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileMaterials" accept=".xls,.xlsx">
                                                <label for="fileMaterials" class="form-label">Importar Materia Prima</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportMaterials">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsMaterials">Descarga Formato</button>
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
                                        <a class="nav-link active selectNavigation" id="sProducts" data-toggle="pill" href="#pills-activity" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-cube mr-1"></i>Productos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="sMaterials" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="fas fa-flask mr-1"></i>Materia Prima
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="tab-pane cardProducts">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblProducts">
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>Total: </td>
                                                            <td id="totalQuantity"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane cardMaterials" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblRawMaterials">
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>Total: </td>
                                                            <td id="totalQuantity"></td>
                                                            <?php if ($_SESSION['flag_products_measure']) { ?>
                                                                <td id="totalGrammage"></td>
                                                            <?php } ?>
                                                            <td id="totalTransit"></td>
                                                            <td id="totalReserved"></td>
                                                            <td id="totalStock"></td>
                                                            <td id="totalDay"></td>
                                                        </tr>
                                                    </tfoot>

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

    <script>
        flag_products_measure = "<?= $_SESSION['flag_products_measure'] ?>";
        viewRawMaterial = 1;
    </script>
    <!-- <script src="/global/js/global/configMagnitudes.js"></script> -->
    <!-- <script src="/global/js/global/configUnits.js"></script> -->
    <!-- <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script> -->
    <script src="/planning/js/basic/invMaterials/tblRawMaterials.js"></script>
    <script src="/planning/js/basic/invMaterials/invMaterials.js"></script>
    <script src="/planning/js/basic/products/tblProducts.js"></script>
    <script src="/planning/js/basic/products/products.js"></script>
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/products/importProducts.js"></script>
    <script src="/planning/js/basic/invMaterials/importRawMaterials.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateImgExt.js"></script>

</body>

</html>