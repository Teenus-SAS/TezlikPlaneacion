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
    <title>Tezlik - Planning | Product Process</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Ficha Técnica Procesos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Asignación de procesos al producto</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnCreateProcess">Nuevo Proceso</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewProductProcess">Importar Procesos</button>
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

                <div class="page-content-wrapper mt--45 mb-5 cardAddProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddProcess">
                                            <div class="form-row">
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Proceso</label>
                                                    <select class="form-control" name="idProcess" id="idProcess"></select>
                                                </div>
                                                <div class="col-sm-3 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Maquina</label>
                                                    <select class="form-control" name="idMachine" id="idMachine"></select>
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="" class="text-center">t.alistamiento (min)</label>
                                                    <input class="form-control text-center number time" type="text" name="enlistmentTime" id="enlistmentTime">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="" class="text-center">t.operacion (min)</label>
                                                    <input class="form-control text-center number time" type="text" name="operationTime" id="operationTime">
                                                </div>
                                                <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="" class="text-center">t.total (min)</label>
                                                    <input class="form-control text-center number" type="text" name="totalTime" id="totalTime" disabled>
                                                </div>
                                                <div class="col-xs-2 mt-1">
                                                    <button class="btn btn-success" id="btnAddProcess">Adicionar</button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- <div class="alert alert-warning" role="alert">
                                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportProductsProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportProductProcess" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileProductsProcess" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Productos*Procesos</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportProductsProcess">Importar</button>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsProductsProcess">Descarga Formato</button>
                                            </div>
                                        </div>
                                        <!-- <div class="alert alert-warning mt-2" role="alert">
                                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                        </div> -->
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
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblConfigProcess" name="tblConfigProcess">
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Total:</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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

    <script src="/planning/js/config/productProcess/tblConfigProcess.js"></script>
    <script src="/planning/js/basic/products/configProducts.js"></script>
    <script src="/planning/js/basic/process/configProcess.js"></script>
    <script src="/planning/js/basic/machines/configMachines.js"></script>
    <script src="/planning/js/config/productProcess/productProcess.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/config/productProcess/importProductProcess.js"></script>
    <script src="../global/js/import/file.js"></script>
</body>

</html>