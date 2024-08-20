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
                        <div class="row align-items-center">
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
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreatePMeasure">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formCreatePMeasure">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <input type="text" class="form-control text-center" name="referenceProduct" id="referenceProduct">
                                                </div>
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Producto</label>
                                                    <input type="text" class="form-control" name="product" id="product">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show- mb-3 label cardSelect">
                                                    <label for="ProdOrigen">Tipo</label>
                                                    <select class="form-control" name="ProdOrigen" id="ProdOrigen">
                                                        <option value="1">Externo</option>
                                                        <option value="2">Manufacturado</option>
                                                    </select>
                                                </div>
                                                <!-- <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Gramaje</label>
                                                    <input type="number" class="form-control text-center inputsCalc" id="grammage" name="grammage"></input>
                                                </div> -->
                                                <div class="col-sm-1 floating-label enable-floating-label show-label">
                                                    <label for="">Ancho</label>
                                                    <input type="number" class="form-control text-center" id="width" name="width"></input>
                                                </div>
                                                <div class="col-sm-1 floating-label enable-floating-label show-label">
                                                    <label for="">Alto</label>
                                                    <input type="number" class="form-control text-center" id="high" name="high">
                                                </div>
                                                <div class="col-sm-1 floating-label enable-floating-label show-label">
                                                    <label for="">Largo</label>
                                                    <input type="number" class="form-control text-center" id="length" name="length">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Largo Útil</label>
                                                    <input type="number" class="form-control text-center inputsCalc" id="usefulLength" name="usefulLength">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Ancho Total</label>
                                                    <input type="number" class="form-control text-center inputsCalc" id="totalWidth" name="totalWidth">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Ventanilla</label>
                                                    <input type="number" class="form-control text-center" id="window" name="window">
                                                </div>

                                                <div class="col-sm-2 ml-2">
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
                                                    <label for="fileProducts" class="form-label">Importar Medidas</label>
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

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <!-- <div class="card-header">
                                        <h5 class="card-title">Clientes</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblProducts">

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
    <!-- <script src="/planning/js/basic/products/configProducts.js"></script> -->

    <script src="/planning/js/basic/productsMeasures/tblPMeasures.js"></script>
    <script src="/planning/js/basic/productsMeasures/pMeausers.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/productsMeasures/importPMeasures.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/global/js/global/validateImgExt.js"></script>

</body>

</html>