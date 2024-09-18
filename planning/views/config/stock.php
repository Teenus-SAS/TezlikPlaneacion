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
    <title>TezlikPlanner | Stock</title>
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
                        <div class="tab-pane cardGeneralSN cardProducts">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Tiempos Producción Productos</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Creación de Tiempos Producción Productos</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewPStock" name="btnNewPStock"><i class="bi bi-plus-circle"></i> Nuevo</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewPStock"><i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane cardGeneralSN cardMaterials" style="display: none;">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-xl-6">
                                    <div class="page-title">
                                        <h3 class="mb-1 font-weight-bold text-dark">Tiempos Entrega Materia Prima Proveedores</h3>
                                        <ol class="breadcrumb mb-3 mb-md-0">
                                            <li class="breadcrumb-item active">Creación de Tiempos de Entrega Materia Prima</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                    <div class="col-xs-2 mr-2">
                                        <button class="btn btn-warning" id="btnNewRMStock" name="btnNewRMStock"><i class="bi bi-plus-circle"></i> Nuevo</button>
                                    </div>
                                    <div class="col-xs-2 py-2 mr-2">
                                        <button class="btn btn-info" id="btnImportNewRMStock"><i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Products -->
                <div class="page-content-wrapper mt--45 mb-5 cardGeneralSN cardCreatePStock" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formCreatePStock">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                                </div>
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Producto</label>
                                                    <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                                </div>

                                                <div class="col-xs-1 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Tiempo Min Producción</label>
                                                    <input type="number" class="form-control text-center" id="pMin" name="min">
                                                </div>

                                                <div class="col-xs-1 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Tiempo Max Producción</label>
                                                    <input type="number" class="form-control text-center" id="pMax" name="max">
                                                </div>

                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnCreatePStock">Crear Stock</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardGeneralSN cardImportPStock" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportPStock" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formPStock">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="filePStock" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Stock Productos</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportPStock">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsPStock">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Stock Material -->
                <div class="page-content-wrapper mt--45 mb-5 cardGeneralSN cardCreateRMStock" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formCreateRMStock">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardSelect" style="margin-bottom:20px">
                                                    <label for="refMaterial">Referencia</label>
                                                    <select class="form-control" name="refMaterial" id="refMaterial"></select>
                                                </div>
                                                <div class="col-sm-10 floating-label enable-floating-label show-label cardSelect" style="margin-bottom:20px">
                                                    <label for="">Materia Prima</label>
                                                    <select class="form-control" name="material" id="material"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="refMaterial">Referencia</label>
                                                    <input type="text" class="form-control" id="referenceMName" name="referenceMName" readonly>
                                                </div>
                                                <div class="col-sm-10 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px">
                                                    <label for="">Materia Prima</label>
                                                    <input type="text" class="form-control" id="materialName" name="materialName" readonly>
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Proveedores</label>
                                                    <select class="form-control" name="idProvider" id="client"></select>
                                                </div>
                                                <!-- <div class="col-sm-4 floating-label enable-floating-label show-label cardDescription" style="margin-bottom:20px; display: none;">
                                                    <label for="">Proveedores</label>
                                                    <input type="text" class="form-control" id="providerName" name="providerName" readonly>
                                                </div> -->

                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Tiempo Min Despacho (días)</label>
                                                    <input type="number" class="form-control text-center" id="rMMin" name="min">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Tiempo Max Despacho (días)</label>
                                                    <input type="number" class="form-control text-center" id="rMMax" name="max">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Cantidad Min de Venta</label>
                                                    <input type="number" class="form-control text-center" id="rMQuantity" name="quantity">
                                                </div>
                                                <div class="col-sm-1 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Medida</label>
                                                    <input type="text" class="form-control text-center" id="abbreviation" name="abbreviation" readonly>
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnCreateRMStock">Crear Stock</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardGeneralSN cardImportRMStock" style="display: none;">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportRMStock" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formRMStock">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileRMStock" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Stock Materiales</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportRMStock">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsRMStock">Descarga Formato</button>
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
                                        <a class="nav-link active selectNavigation" id="nProducts" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-flask mr-1"></i>Productos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="nMaterials" data-toggle="pill" href="#pills-projects" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-arrow-repeat mr-1"></i>Materiales
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="tab-pane cardGeneralSN cardProducts">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover text-center" id="tblPStock" name="tblPStock">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane cardGeneralSN cardMaterials" style="display: none;">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover text-center" id="tblRMStock" name="tblRMStock">

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
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <script>
        viewRawMaterial = 2;
    </script>
    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/planning/js/admin/clients/configClients.js"></script>
    <script src="/planning/js/config/PStock/PStock.js"></script>
    <script src="/planning/js/config/RMStock/RMStock.js"></script>
    <script src="/planning/js/config/PStock/tblPStock.js"></script>
    <script src="/planning/js/config/RMStock/tblRMStock.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/planning/js/config/PStock/importPStock.js"></script>
    <script src="/planning/js/config/RMStock/importRMStock.js"></script>
    <script src="/global/js/import/file.js"></script>

</body>

</html>