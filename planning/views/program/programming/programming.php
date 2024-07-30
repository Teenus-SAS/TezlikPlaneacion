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
    <title>Tezlik - Planning | Programming</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Programa de Producción</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-4 form-inline justify-content-sm-end cardBottons">
                                <div id="machines1">
                                    <label class="font-weight-bold text-dark">Maquina</label>
                                    <select class="form-control idMachine" id="searchMachine">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-1 form-inline justify-content-sm-end mt-3 cardBottons">
                                <button class="btn btn-warning" id="btnNewProgramming" name="btnNewProgramming">Programar</button>
                            </div>
                            <div class="col-sm-7 col-xl-1 form-inline justify-content-sm-end mt-3 cardSaveBottons">
                                <button class="btn btn-primary" id="btnSavePrograming" name="btnSavePrograming">Guardar Planeación</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateProgramming">
                    <div class="container-fluid">
                        <form id="formCreateProgramming">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body" style="padding-bottom:0px">
                                            <div class="form-group mb-3">
                                                <h5 class="card-title text-secondary fw-bold">Datos del pedido</h5>
                                                <hr class="mt-0 mb-2">
                                                <div class="row mt-3">
                                                    <div class="col-sm-2 programmingSelect floating-label enable-floating-label show-label mb-2 p-1" id="orders">
                                                        <label for="">No. Pedido</label>
                                                        <select class="form-control text-center" id="order" name="order">
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2 programmingSelect floating-label enable-floating-label show-label mb-2 p-1" id="products">
                                                        <label for="">Referencia</label>
                                                        <select class="form-control input slctProduct" id="refProduct" name="idProduct"></select>
                                                    </div>
                                                    <div class="col-sm-7 programmingSelect floating-label enable-floating-label show-label mb-2 p-1" id="products">
                                                        <label for="">Producto</label>
                                                        <select class="form-control input slctProduct" id="selectNameProduct" name="idProduct"></select>
                                                    </div>
                                                    <div class="col-sm-1" id="classification" style="display: flex; flex-direction: column; align-items: center;">
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-2 p-1">
                                                        <label for="">Cantidad Requerida</label>
                                                        <input type="text" class="form-control text-center" id="quantityOrder" name="quantityOrder" readonly>
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-2 p-1">
                                                        <label for="">Cantidad Pendiente Fabricar</label>
                                                        <input type="text" class="form-control text-center" id="quantityMissing" name="quantityMissing" readonly>
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-2 p-1">
                                                        <label for="">MP Disponible</label>
                                                        <input type="text" class="form-control text-center" id="quantityMP" name="quantityMP" readonly>
                                                    </div>

                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-2 p-1">
                                                        <label for="">Cantidad a Fabricar</label>
                                                        <input type="number" class="form-control text-center input" id="quantity" name="quantity">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row cardFormProgramming2">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body" style="padding-bottom:0px">
                                            <div class="form-group mb-3">
                                                <h5 class="card-title text-secondary fw-bold">Programación de fabricación</h5>
                                                <hr class="mt-0 mb-2">
                                                <div class="row mt-3">
                                                    <div class="col-sm-3 floating-label enable-floating-label show-label" id="process">
                                                        <label for="">Proceso</label>
                                                        <select class="form-control input selects" id="idProcess" name="idProcess">
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label" id="machines1">
                                                        <label for="">Maquina</label>
                                                        <select class="form-control input selects" id="idMachine" name="idMachine">
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 floating-label enable-floating-label show-label date">
                                                        <label for="">Fecha Inicial</label>
                                                        <input type="datetime-local" class="form-control text-center" inline="true" id="minDate" name="minDate" readonly min="<?php
                                                                                                                                                                                date_default_timezone_set('America/Bogota');
                                                                                                                                                                                echo date('Y-m-d', strtotime('+1 day')); ?>">
                                                    </div>
                                                    <div class="col-sm-3 floating-label enable-floating-label show-label date">
                                                        <label for="">Fecha Final</label>
                                                        <input type="datetime-local" class="form-control text-center number" id="maxDate" name="maxDate" readonly min="<?php
                                                                                                                                                                        date_default_timezone_set('America/Bogota');
                                                                                                                                                                        echo date('Y-m-d', strtotime('+1 day')); ?>">
                                                    </div>
                                                    <div class="col-sm-1 mt-2">
                                                        <button class="btn btn-info" type="submit" id="btnCreateProgramming" name="btnCreateProgramming">Crear</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                                        <h5 class="card-title">Programación</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblProgramming">
                                                <tbody id="tblProgrammingBody"></tbody>
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
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="/global/js/global/lastText.js"></script>
    <script src="/planning/js/program/programming/tblProgramming.js"></script>
    <!-- <script src="/planning/js/orders/configOrders.js"></script> -->
    <!-- <script src="/planning/js/basic/process/configProcess.js"></script> -->
    <!-- <script src="/planning/js/basic/machines/configMachines.js"></script> -->
    <script src="/planning/js/program/programming/programming.js"></script>
    <script src="/planning/js/program/programming/configProgramming.js"></script>
</body>

</html>