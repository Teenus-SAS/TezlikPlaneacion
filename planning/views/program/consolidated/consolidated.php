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
    <title>Tezlik - Planning | Consolidated</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once dirname(dirname(dirname(__DIR__))) . '/partials/nav.php'; ?>

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
                                    <h3 class="mb-1 font-weight-bold text-dark">Programa de Consolidados</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <!-- <button class="btn btn-warning" id="btnNewConsolidated" name="btnNewConsolidated">Programar</button> -->
                                    <!-- <button class="btn btn-info ml-3" id="btnImportNewConsolidated" name="btnNewImportConsolidated">Importar Consolidado</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardCreateConsolidated">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formCreateConsolidated">
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="">Maquina</label>
                                                    <select class="form-control" id="idMachine" name="idMachine">
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label for="">Pedido</label>
                                                    <select class="form-control" id="order" name="order">
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="">Producto</label>
                                                    <select class="form-control" id="selectNameProduct" name="idProduct"></select>
                                                    <select class="form-control" id="refProduct" style="display:none"></select>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label for="">Cantidad</label>
                                                    <input type="text" class="form-control text-center number" id="quantity" name="quantity">
                                                </div>
                                                <button class="btn btn-info" type="submit" id="btnCreateConsolidated" name="btnCreateConsolidated" style="width: 100px;height:50%; margin-top: 34px; margin-left: 20px">Crear</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportConsolidated">
                    <div class="container-fluid">
                        <div class="row">
                            <form id="formImportConsolidated" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileConsolidated" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label">Importar Consolidado</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportConsolidated">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsConsolidated">Descarga Formato</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> -->

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="row card-header">
                                        <div class="col">
                                            <h5 class="card-title">Consolidado</h5>
                                        </div>
                                        <div class="col-lg-2 form-group floating-label enable-floating-label show-label">
                                            <label for="">Numero De Semanas</label>
                                            <input type="number" class="form-control text-center" id="numWeek" name="numWeek">
                                        </div>
                                        <div class="col-lg-2 form-group floating-label enable-floating-label show-label">
                                            <button class="btn btn-info" type="submit" id="btnCalcConsolidated" name="btnCalcConsolidated" style="margin-top: 6px;">Calcular</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblConsolidated">

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
        <?php include_once  dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/footer.php'; ?>
    </div>

    <?php include_once dirname(dirname(dirname(dirname(__DIR__)))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/users/usersAccess.js"></script>

    <script src="../planning/js/program/consolidated/tblConsolidated.js"></script>
    <script src="/planning/js/program/consolidated/consolidated.js"></script>
</body>

</html>