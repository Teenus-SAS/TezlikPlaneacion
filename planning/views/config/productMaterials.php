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
                            <div class="col-sm-5 col-xl-4">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Ficha Técnica Productos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active" id="comment">Asignación de materias primas al producto</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-8 col-xl-8">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnCreateProduct">Adicionar Nueva Materia Prima</button>
                                    <button class="btn btn-secondary ml-3" id="btnCreateProductInProcess">Adicionar Producto En Proceso</button>
                                    <!-- <button class="btn btn-info ml-3" id="btnImportNewProductsMaterials">Importar</button> -->
                                    <div class="dropleft show ml-3">
                                        <a class="btn btn-info dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Importar
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="btnImport">
                                            <a class="dropdown-item import" id="1" href="javascript:void(0)">Materias Prima</a>
                                            <a class="dropdown-item import" id="2" href="javascript:void(0)">Productos en Proceso</a>
                                        </div>
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
                                        <div class="gridx2">
                                            <label for="">Referencia</label>
                                            <label for="">Producto</label>
                                            <select class="form-control" name="refProduct" id="refProduct"></select>
                                            <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddProductInProccess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formAddProductInProccess">
                                        <div class="card-body">
                                            <div class="gridx2p">
                                                <div class="form-group" style="margin-bottom:0px">
                                                    <label for="">Producto En Proceso</label>
                                                    <select class="form-control" name="idProduct" id="product"></select>
                                                </div>
                                                <div class="form-group" style="margin-bottom:0px;margin-top:33px">
                                                    <button class="btn btn-success" id="btnAddProductInProccess">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddMaterials">
                                            <div class="gridx4pm">
                                                <label for="">Materia Prima</label>
                                                <label for="">Cantidad</label>
                                                <label for="">Unidad</label>
                                                <label for=""></label>
                                                <select class="form-control" name="material" id="material"></select>
                                                <input class="form-control text-center number" type="text" name="quantity" id="quantity">
                                                <input class="form-control text-center" type="text" name="unity" id="unity" disabled>
                                                <button class="btn btn-success" id="btnAddMaterials">Adicionar Materia Prima</button>
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
                            <form id="formImport" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="file" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label" id="txtFile"></label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImport">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImports">Descarga Formato</button>
                                                </div>
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
                            <div class="col-12 cardTableProductsInProcess">
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
    <script src="/planning/js/users/usersAccess.js"></script>

    <!-- <script src="/global/js/global/number.js"></script> -->
    <script src="/planning/js/config/productMaterials/tblConfigMaterials.js"></script>
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/planning/js/config/productMaterials/productMaterials.js"></script>
    <script src="/planning/js/config/productMaterials/productsInProcess.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/config/productMaterials/importProductMaterials.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>