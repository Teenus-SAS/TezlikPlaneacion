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
    <title>TezlikPlanner | Nómina Producción</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Nómina Producción</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Nómina</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewEmployee" name="btnNewEmployee"><i class="bi bi-person-plus-fill"></i> Adicionar Empleado</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewEmployee"><i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateEmployee">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formCreateEmployee">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Nombres</label>
                                                    <input type="text" class="form-control text-center" id="firstname" name="firstname">
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Apellidos</label>
                                                    <input type="text" class="form-control text-center" id="lastname" name="lastname">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Área</label>
                                                    <select type="text" class="form-control" id="idArea" name="idArea"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Proceso</label>
                                                    <select type="text" class="form-control" id="idProcess" name="idProcess"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Posición</label>
                                                    <input type="text" class="form-control text-center" id="position" name="position">
                                                </div>
                                                <!-- <div class="col-sm-6 floating-label enable-floating-label show-label drag-area mt-4">
                                                    <input class="form-control" type="file" id="formFile">
                                                    <label for="formFile" class="form-label"> Cargar imagen</label>
                                                </div> -->
                                                <div class="col-sm mt-4">
                                                    <button class="btn btn-success" id="btnCreateEmployee">Crear</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportEmployees">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportEmployees" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row" id="formEmployees">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="fileEmployees" accept=".xls,.xlsx">
                                                    <label for="fileEmployees" class="form-label">Importar Personal</label>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportEmployees">Importar</button>
                                                </div>
                                                <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsEmployees">Descarga Formato</button>
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
                                            <table class="fixed-table-loading table table-hover" id="tblEmployees">

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

    <script src="/planning/js/basic/areas/configAreas.js"></script>
    <script src="/planning/js/basic/process/configProcess.js"></script>
    <script src="/planning/js/basic/payroll/tblPayroll.js"></script>
    <script src="/planning/js/basic/payroll/payroll.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/payroll/importPayroll.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/global/js/global/validateImgExt.js"></script>

</body>

</html>