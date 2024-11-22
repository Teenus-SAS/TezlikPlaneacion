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
    <title>TezlikPlanner | Machines</title>
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

                        <div class="row align-items-center cardAreas" style="display: none;">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-cogs mr-1"></i>Áreas</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Áreas de Proceso</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewArea" name="btnNewArea"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnNewImportAreas" name="btnNewImportAreas"><i class="bi bi-cloud-arrow-up-fill mr-1"></i>Importar</button>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center cardProcess">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-cogs mr-1"></i>Procesos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Procesos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewProcess" name="btnNewProcess"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewProcess"><i class="bi bi-cloud-arrow-up-fill mr-1"></i>Importar</button>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center cardMachines" style="display: none;">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-cogs mr-1"></i>Máquinas y Procesos Manuales</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Máquinas y Procesos Manuales</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewMachine" name="btnNewMachine"><i class="bi bi-plus-circle mr-1"></i>Adicionar</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewMachines" name="btnNewImportMachines"><i class="bi bi-cloud-arrow-up-fill mr-1"></i>Importar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Areas -->
                <div class="page-content-wrapper mt--45 mb-5 cardCreateArea">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateArea">
                                            <div class="form-row">
                                                <div class="col-sm-10 floating-label enable-floating-label show-label">
                                                    <input type="text" class="form-control" name="area" id="area">
                                                    <label for="">Nombre</label>
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnCreateArea">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Procesos -->
                <div class="page-content-wrapper mt--45 mb-5 cardCreateProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <form id="formCreateProcess">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-10 floating-label enable-floating-label show-label" style="margin-bottom:0px">
                                                    <label for="">Proceso</label>
                                                    <input type="text" class="form-control" id="process" name="process">
                                                </div>
                                                <div class="col-sm mt-1">
                                                    <button class="btn btn-success" id="btnCreateProcess">Crear Proceso</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProcess" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formProcess">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProcess" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Process</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProcess">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProcess">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Maquinas -->
                <div class="page-content-wrapper mt--45 mb-5 cardCreateMachines">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateMachine">
                                            <div class="form-row">
                                                <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="text" class="form-control" name="machine" id="machine">
                                                    <label>Nombre</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="cost" id="costMachine" data-toggle="tooltip" title="Ingrese el precio de compra">
                                                    <label>Precio</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="depreciationYears" id="depreciationYears">
                                                    <label>Años Depreciación</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <input type="number" class="form-control text-center" name="hoursMachine" id="hoursMachine">
                                                    <label>Horas de Trabajo (día)</label>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <input type="number" class="form-control text-center" name="daysMachine" id="daysMachine">
                                                    <label>Dias de Trabajo (Mes)</label>
                                                </div>

                                                <div class="col-xs-2" style="margin-bottom:0px;margin-top:5px;">
                                                    <button class="btn btn-success" id="btnCreateMachine">Crear Máquina</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportMachines">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportMachines" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formMachines">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileMachines" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Máquinas</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportMachines">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsMachines">Descarga Formato</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportAreas">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportAreas" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row" id="formAreas">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileAreas" accept=".xls,.xlsx">
                                                <label for="fileAreas" class="form-label"> Importar Areas</label>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportAreas">Importar</button>
                                            </div>
                                            <div class="col-xs-2 cardBottons" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsAreas">Descarga Formato</button>
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
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="link-areas" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-diagram-3 mr-1"></i>Áreas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="link-process" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-cogs mr-1"></i>Procesos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="link-machines" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="fas fa-tools mr-1"></i>Máquinas
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="tab-pane cardAreas" style="display: none;">
                                            <table class="fixed-table-loading table table-hover" id="tblAreas"></table>
                                        </div>
                                        <div class="tab-pane cardProcess">
                                            <div class="table-responsive">
                                                <table class="fixed-table-loading table table-hover" id="tblProcess"></table>
                                            </div>
                                        </div>
                                        <div class="tab-pane cardMachines" style="display: none;">
                                            <table class="fixed-table-loading table table-hover" id="tblMachines"></table>
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

    <script src="/planning/js/basic/process/process.js"></script>
    <script src="/planning/js/basic/process/tblProcess.js"></script>
    <script src="/planning/js/basic/machines/tblMachines.js"></script>
    <script src="/planning/js/basic/areas/tblAreas.js"></script>
    <script src="/planning/js/basic/machines/machines.js"></script>
    <script src="/planning/js/basic/areas/areas.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/machines/importMachines.js"></script>
    <script src="/planning/js/basic/process/importProcess.js"></script>
    <script src="/planning/js/basic/areas/importAreas.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>