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
    <title>Tezlik - Planning | Product Materials</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Ficha Técnica Productos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active" id="comment">Asignación de materias primas al producto</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnCreateProduct">Adicionar Nueva Materia Prima</button>
                                </div>
                                <!-- <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-secondary" id="btnCreateProductInProcess">Adicionar Producto En Proceso</button>
                                </div> -->
                                <div class="col-xs-2 py-2 dropleft show mr-2">
                                    <a class="btn btn-info dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Importar Productos*Materia Prima
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="btnImport">
                                        <a class="dropdown-item import" id="1" href="javascript:void(0)">Materias Prima</a>
                                        <!-- <a class="dropdown-item import" id="2" href="javascript:void(0)">Productos en Proceso</a> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateRawMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                <label for="">Referencia</label>
                                                <select class="form-control refProduct" name="refProduct" id="refProduct"></select>
                                            </div>
                                            <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                <label for="">Producto</label>
                                                <select class="form-control selectNameProduct" name="selectNameProduct" id="selectNameProduct"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardAddProductInProccess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formAddProductInProccess">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-10 floating-label enable-floating-label show-label">
                                                    <label for="">Producto En Proceso</label>
                                                    <select class="form-control" name="idProduct" id="product"></select>
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnAddProductInProccess">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <div class="page-content-wrapper mt--45 mb-5 cardAddMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <form id="formAddMaterials">
                                            <div class="form-row">
                                                <div class="col-sm-7 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Materia Prima</label>
                                                    <select class="form-control" name="material" id="material"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <select class="form-control" id="units" name="unit"></select>
                                                    <label for="">Unidad</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Cantidad</label>
                                                    <input class="form-control text-center number" type="text" name="quantity" id="quantity">
                                                </div>
                                                <div class="col-xs-1 mt-1">
                                                    <button class="btn btn-success" id="btnAddMaterials">Adicionar Materia Prima</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImport">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProductMaterial" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProductsMaterials" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Productos*Materia Prima</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProductsMaterials">Importar</button>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProductsMaterials">Descarga Formato</button>
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
                            <div class="col-12 cardTableConfigMaterials">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Materias Primas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblConfigMaterials" name="tblConfigMaterials">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-12 cardTableProductsInProcess">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Productos En Proceso</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblProductsInProcess" name="tblProductsInProcess">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
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
    <script src="/planning/js/users/usersAccess.js"></script>

    <script src="/global/js/global/configUnits.js"></script>
    <script src="/planning/js/config/productMaterials/tblConfigMaterials.js"></script>
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/planning/js/config/productMaterials/productMaterials.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/config/productMaterials/importProductMaterials.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>