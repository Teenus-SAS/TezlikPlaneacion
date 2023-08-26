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
    <title>Tezlik - Planning | Cicles Machine</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Ciclos Maquina</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Plan de Ciclos Máquinas</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewPlanCiclesMachine" name="btnNewPlanCiclesMachine">Nuevo Ciclo Máquina</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewPlanCiclesMachine" name="btnImportNewPlanCiclesMachine">Importar Ciclos Máquina</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreatePlanCiclesMachine">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreatePlanCiclesMachine">
                                            <div class="form-row">
                                                <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <select class="form-control" name="selectNameProduct" id="selectNameProduct"></select>
                                                    <label for="">Producto</label>
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <select class="form-control idMachine" name="idMachine" id="idMachine"></select>
                                                    <label for="">Maquina</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <input type="text" class="form-control text-center number" name="ciclesHour" id="ciclesHour">
                                                    <label for="">Ciclo x Hora</label>
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnCreatePlanCiclesMachine">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportPlanCiclesMachine">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formImportPlanCiclesMachine" enctype="multipart/form-data">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                    <input class="form-control" type="file" id="filePlanCiclesMachine" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Máquinas</label>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportPlanCiclesMachine">Importar</button>
                                                </div>
                                                <div class="col-xs-2" style="margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsPlanCiclesMachine">Descarga Formato</button>
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
                                    <div class="card-header">
                                        <h5 class="card-title">Ciclos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblPlanCiclesMachine">

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
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <!-- <script src="/global/js/global/number.js"></script> -->
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/basic/machines/configMachines.js"></script>
    <script src="/planning/js/config/planCiclesMachine/tblPlanCiclesMachine.js"></script>
    <script src="/planning/js/config/planCiclesMachine/planCiclesMachine.js"></script>
    <script src="/global/js/import/file.js"></script>
    <script src="/global/js/import/import.js"></script>
    <script src="/planning/js/config/planCiclesMachine/importPlanCiclesMachine.js"></script>
</body>

</html>