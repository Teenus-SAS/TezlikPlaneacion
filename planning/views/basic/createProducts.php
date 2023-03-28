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
    <title>Tezlik - Planning | Products</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Productos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Productos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewProduct">Nuevo Producto</button>
                                    <button class="btn btn-info ml-3" id="btnImportNewProducts">Importar Productos</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateProduct">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateProduct">
                                            <div class="form-row">
                                                <div class="col-md-3">
                                                    <label for="">Referencia</label>
                                                    <input type="text" class="form-control" name="referenceProduct" id="referenceProduct">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="">Nombre Producto</label>
                                                    <input type="text" class="form-control" name="product" id="product">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="">Molde</label>
                                                    <select class="form-control" name="idMold" id="idMold"></select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-row py-4">
                                                        <div class="col-xs">
                                                            <label for="">Cantidad en inventario</label>
                                                            <input type="text" class="form-control text-center number" id="quantity" name="quantity">
                                                        </div>
                                                        <div class="col-4">
                                                            <label for="productsCategories">Categoría</label>
                                                            <select id="productsCategories" name="productsCategories" type="number" class="form-control"></select>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="col-6">
                                                            <label for="formFile" class="form-label"> Cargar imagen producto</label>
                                                            <input class="form-control" type="file" id="formFile">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button type="text" class="btn btn-success" id="btnCreateProduct" style="width: 150px;height:50%; margin-top: 32px; margin-left: 20px">Crear Producto</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-5" id="preview"></div>
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
                            <form id="formImportProduct" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileProducts" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Productos</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportProducts">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProducts">Descarga Formato</button>
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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Productos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblProducts">

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

    <!-- Page End -->
    <!-- <script src="/global/js/global/number.js"></script> -->
    <script src="/planning/js/basic/products/tblProducts.js"></script>
    <script src="/planning/js/basic/invMold/configInvMold.js"></script>
    <script src="/planning/js/general/category/configCategories.js"></script>
    <script src="/planning/js/basic/products/products.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/products/importProducts.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateImgExt.js"></script>

</body>

</html>