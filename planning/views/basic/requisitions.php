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
    <title>Tezlik - Planning | Requisitions</title>
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
                            <div class="col-sm-5 col-xl-2">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Requisiciones</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Creación de Requisiciones</li>
                                    </ol>
                                </div>
                            </div>
                            <!-- <div class="col-sm-7 col-xl-10 form-inline justify-content-sm-end">
                                <div class="col-sm-2">
                                    <label for="firtsDate" class="form-label text-dark">Fecha Inicial</label>
                                    <input class="form-control dateOrders" id="firtsDate" type="date">
                                </div>
                                <div class="col-sm-2">
                                    <label for="lastDate" class="form-label text-dark">Fecha Final</label>
                                    <input class="form-control dateOrders" id="lastDate" type="date">
                                </div>
                            </div> -->
                            <div class="col-sm-7 col-xl-10 mt-4 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2">
                                    <button class="btn btn-warning" id="btnNewRequisition" name="btnNewRequisition">Nueva Requisicion</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-info" id="btnImportNewRequisitions" name="btnNewImportRequisitions">Importar Requisiciones</button>
                                </div>
                                <div class="col-xs-2 py-2 mr-2">
                                    <button class="btn btn-secondary" id="btnOpenSearchDate" name="btnOpenSearchDate">Buscar Fecha</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardSearchDate">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formSearchDate">
                                    <div class="card">
                                        <div class="card-body pt-3">
                                            <div class="form-row">
                                                <div class="col-sm-2">
                                                    <label for="firtsDate" class="form-label text-dark">Fecha Inicial</label>
                                                    <input class="form-control dateOrders" id="firtsDate" type="date">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="lastDate" class="form-label text-dark">Fecha Final</label>
                                                    <input class="form-control dateOrders" id="lastDate" type="date">
                                                </div>
                                                <div class="col-xs-2" style="margin-top:33px">
                                                    <button type="text" class="btn btn-info" id="btnSearchDate">Buscar Fecha</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardAddRequisitions">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddRequisition">
                                            <div class="form-row">
                                                <!-- <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:20px">
                                                    <label for="">Referencia</label>
                                                    <select class="form-control refProduct" name="idProduct" id="refProduct"></select>
                                                </div> -->
                                                <div class="col-sm-8 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Material</label>
                                                    <select class="form-control" name="idMaterial" id="material"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Fecha Solicitud</label>
                                                    <input class="form-control" type="date" name="applicationDate" id="applicationDate">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:5px">
                                                    <label for="">Fecha Entrega</label>
                                                    <input class="form-control" type="date" name="deliveryDate" id="deliveryDate">
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2" style="margin-bottom:5px">
                                                    <label for="">Cantidad</label>
                                                    <input class="form-control text-center" type="number" name="quantity" id="quantity">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label mt-2" style="margin-bottom:5px">
                                                    <label for="">Orden de Compra</label>
                                                    <input class="form-control text-center" type="text" name="purchaseOrder" id="purchaseOrder">
                                                </div>
                                                <div class="col-sm" style="margin-top:12px">
                                                    <button class="btn btn-success" id="btnAddRequisition">Asignar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardImportRequisitions">
                    <div class="container-fluid">
                        <div class="row">
                            <form class="col-12" id="formImportRequisitions" enctype="multipart/form-data">
                                <div class="card">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="col-sm-6 floating-label enable-floating-label show-label drag-area" style="margin-bottom:10px!important">
                                                <input class="form-control" type="file" id="fileRequisitions" accept=".xls,.xlsx">
                                                <label for="formFile" class="form-label"> Importar Requisiciones</label>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-success" id="btnImportRequisitions">Importar</button>
                                            </div>
                                            <div class="col-xs-2" style="margin-top:7px">
                                                <button type="text" class="btn btn-info" id="btnDownloadImportsRequisitions">Descarga Formato</button>
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
                                        <h5 class="card-title">Requisiciones</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblRequisitions">

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
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <script src="../global/js/global/number.js"></script>
    <script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
    <script src="/planning/js/basic/requisitions/tblRequisitions.js"></script>
    <script src="/planning/js/basic/requisitions/requisitions.js"></script>
    <script src="../global/js/import/import.js"></script>
    <script src="/planning/js/basic/requisitions/importRequisitions.js"></script>
    <script src="../global/js/import/file.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
</body>

</html>