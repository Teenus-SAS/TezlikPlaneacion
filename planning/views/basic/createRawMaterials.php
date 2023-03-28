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
    <title>Tezlik - Planning | Materials</title>
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
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Materias Primas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Materias Primas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="btnNewMaterial" name="btnNewMaterial">Nueva Materia Prima</button>
                                    <button class="btn btn-info ml-3" id="btnImportNewMaterials" name="btnNewImportMaterials">Importar Materias Primas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardRawMaterials">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateMaterial">
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="refRawMaterial">Referencia</label>
                                                    <input type="text" class="form-control" id="refRawMaterial" name="refRawMaterial">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="nameRawMaterial">Nombre Materia Prima</label>
                                                    <input type="text" class="form-control" id="nameRawMaterial" name="nameRawMaterial">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="validationCustom02">Categorias</label>
                                                    <select class="form-control" id="category" name="category">
                                                        <option value="" selected disabled>Seleccionar</option>
                                                        <option value="1">Insumos</option>
                                                        <option value="2">Materiales</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-2">
                                                    <label for="unityRawMaterial">Unidad</label>
                                                    <input type="text" class="form-control text-center" id="unityRawMaterial" name="unityRawMaterial">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="validationCustom05">Cantidad En Inventario</label>
                                                    <input type="text" class="form-control text-center number" id="quantity" name="quantity">
                                                </div>

                                                <button class="btn btn-info" type="submit" id="btnCreateMaterial" name="btnCreateMaterial" style="width: 100px;height:50%; margin-top: 34px; margin-left: 20px">Crear</button>
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
                            <form id="formImportMaterials" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileMaterials" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label">Importar Materia Prima</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportMaterials">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsMaterials">Descarga Formato</button>
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
                                        <h5 class="card-title">Materias Primas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblRawMaterials">

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
        <!-- Main content end -->

        <!-- Footer -->
        <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
    </div>
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/users/usersAccess.js"></script>

    <!-- <script src="/global/js/global/number.js"></script> -->
    <script src="/planning/js/basic/rawMaterials/tblRawMaterials.js"></script>
    <script src="/planning/js/basic/rawMaterials/rawMaterials.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/rawMaterials/importRawMaterials.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>