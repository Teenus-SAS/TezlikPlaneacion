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
    <title>TezlikPlanner | Productos</title>
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
                        <div class="row align-items-center cardPMeasure">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Productos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewPMeasure" name="btnNewPMeasure"><i class="bi bi-person-plus-fill"></i> Adicionar Producto</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewPMeasure"><i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center cardMaterials" style="display: none;">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Materias Primas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creacion de materias prima</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewMaterial" name="btnNewMaterial"><i class="bi bi-plus-circle"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewMaterials" name="btnNewImportMaterials"><i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="page-content-wrapper mt--45 mb-5 cardCreatePMeasure">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formCreatePMeasure">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mb-3 cardSelect">
                                                    <label for="prodOrigin">Origen</label>
                                                    <select class="form-control" name="origin" id="prodOrigin">
                                                        <option selected disabled>Seleccionar</option>
                                                        <option value="1">COMERCIALIZADO</option>
                                                        <option value="2">MANUFACTURADO</option>
                                                    </select>
                                                </div>
                                                <?php if ($_SESSION['flag_products_measure'] == '1') { ?>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-3 cardSelect productType">
                                                        <label for="idProductType">Tipo</label>
                                                        <select class="form-control" name="idProductType" id="idProductType">
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <input type="text" class="form-control text-center" name="referenceProduct" id="referenceProduct">
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Descripción Producto</label>
                                                    <input type="text" class="form-control" name="product" id="product">
                                                </div>
                                                <?php if ($_SESSION['flag_products_measure'] == '1') { ?>
                                                    <div class="col-sm-1 floating-label enable-floating-label show-label inputs inputsMeasures">
                                                        <label for="width">Ancho</label>
                                                        <input type="number" class="form-control text-center" id="width" name="width"></input>
                                                    </div>
                                                    <div class="col-sm-1 floating-label enable-floating-label show-label inputsMeasures inputs">
                                                        <label for="high">Fuelle</label>
                                                        <input type="number" class="form-control text-center" id="high" name="high">
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label inputsMeasures">
                                                        <label for="length">Largo Total</label>
                                                        <input type="number" class="form-control text-center" id="length" name="length">
                                                    </div>
                                                    <div class="col-sm-1 floating-label enable-floating-label show-label inputs inputsMeasures">
                                                        <label for="usefulLength">Largo Útil</label>
                                                        <input type="number" class="form-control text-center" id="usefulLength" name="usefulLength">
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label inputsMeasures">
                                                        <label for="totalWidth">Ancho Total</label>
                                                        <input type="number" class="form-control text-center" id="totalWidth" name="totalWidth">
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label inputsMeasures">
                                                        <label for="window" id="lblWindow">Ventanilla</label>
                                                        <input type="number" class="form-control text-center" id="window" name="window">
                                                    </div>
                                                    <div class="col-sm-1 floating-label enable-floating-label show-label inputsMeasures">
                                                        <label for="inks">Tintas</label>
                                                        <input type="number" class="form-control text-center" id="inks" name="inks">
                                                    </div>
                                                <?php } ?>

                                                <div class="col-sm-1 ml-2 mt-1">
                                                    <button class="btn btn-success" id="btnCreatePMeasure">Crear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportPMeasure">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportProducts" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formProducts">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileProducts" accept=".xls,.xlsx">
                                                    <label for="fileProducts" class="form-label">Importar Productos</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportProducts">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProducts">Descarga Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                                                <?php if ($_SESSION['flag_products_measure'] == '1') { ?>
                                                    <div class="col-md-2 floating-label enable-floating-label show-label">
                                                        <label for="materialType">Tipo Material</label>
                                                        <select class="form-control" id="materialType" name="materialType"></select>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-md-2 floating-label enable-floating-label show-label">
                                                    <label for="refRawMaterial">Referencia</label>
                                                    <input type="text" class="form-control" id="refRawMaterial" name="refRawMaterial">
                                                </div>
                                                <div class="col-md-6 floating-label enable-floating-label show-label">
                                                    <label for="nameRawMaterial">Nombre Materia Prima</label>
                                                    <input type="text" class="form-control" id="nameRawMaterial" name="nameRawMaterial">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Magnitud</label>
                                                    <select class="form-control magnitudes" id="magnitudes" name="magnitude"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Unidad</label>
                                                    <select class="form-control units" id="units" name="unit">
                                                        <option disabled selected>Seleccionar</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="costMaterial">Costo</label>
                                                    <input type="number" class="form-control text-center" id="costMaterial" name="costMaterial">
                                                </div>

                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-info" type="submit" id="btnCreateMaterial" name="btnCreateMaterial">Guardar</button>
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
                                                <label for="formFile" class="form-label">Importar Materia Prima</label>
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

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="link-products" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-diagram-3 mr-1"></i>Productos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="link-materials" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-diagram-3 mr-1"></i>Materias Primas
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive tab-pane cardPMeasure">
                                            <table class="fixed-table-loading table table-hover" id="tblProducts">

                                            </table>
                                        </div>
                                        <div class="table-responsive tab-pane cardMaterials" style="display: none;">
                                            <table class="fixed-table-loading table table-hover" id="tblRawMaterials">
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td id="totalCost"></td>
                                                        <td></td>
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
        <!-- main content End -->
        <!-- footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <script>
        flag_products_measure = "<?= $_SESSION['flag_products_measure'] ?>";
    </script>
    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/global/js/global/configMagnitudes.js"></script>
    <script src="/planning/js/basic/rawMaterials/configMaterialType.js"></script>
    <script src="/global/js/global/configUnits.js"></script>
    <script src="/planning/js/basic/productsType/configProductType.js"></script>
    <script src="/planning/js/basic/rawMaterials/tblRawMaterials.js"></script>
    <script src="/planning/js/basic/rawMaterials/rawMaterials.js"></script>
    <!-- <script src="/planning/js/basic/productsType/productType.js"></script>
    <script src="/planning/js/basic/productsType/tblProductsType.js"></script>
    <script src="/planning/js/basic/productsType/importPTypes.js"></script> -->
    <script src="/planning/js/basic/productsMeasures/tblPMeasures.js"></script>
    <script src="/planning/js/basic/productsMeasures/pMeausers.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/planning/js/basic/rawMaterials/importRawMaterials.js"></script>
    <script src="/planning/js/basic/productsMeasures/importPMeasures.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/global/js/global/validateImgExt.js"></script>

</body>

</html>