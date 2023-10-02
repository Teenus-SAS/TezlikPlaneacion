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
                            <div class="col-sm-5 col-xl-3">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Programa de Producción</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-8 form-inline justify-content-sm-end">
                                <div class="col-sm-3" id="machines">
                                    <label class="font-weight-bold text-dark">Maquina</label>
                                    <select class="form-control idMachine" id="searchMachine">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-1 form-inline justify-content-sm-end">
                                <div class="col-xs-2 mr-2 mt-3">
                                    <button class="btn btn-warning" id="btnNewProgramming" name="btnNewProgramming">Programar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateProgramming">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <form id="formCreateProgramming">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-sm-2 programmingSelect floating-label enable-floating-label show-label" id="orders">
                                                    <label for="">No. Pedido</label>
                                                    <select class="form-control" id="order" name="order">
                                                    </select>
                                                </div>
                                                <div class="col-sm-6 programmingSelect floating-label enable-floating-label show-label" id="products">
                                                    <label for="">Producto</label>
                                                    <select class="form-control input" id="selectNameProduct" name="idProduct"></select>
                                                    <select class="form-control" id="refProduct" style="display:none"></select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Cantidad Pedido</label>
                                                    <input type="text" class="form-control text-center" id="quantityOrder" name="quantityOrder" readonly>
                                                </div>
                                                <div class="col-sm-4 floating-label enable-floating-label show-label" id="machines">
                                                    <label for="">Maquina</label>
                                                    <select class="form-control idMachine input" id="idMachine" name="idMachine">
                                                    </select>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label">
                                                    <label for="">Cantidad a Fabricar</label>
                                                    <input type="text" class="form-control text-center number input" id="quantity" name="quantity">
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label date">
                                                    <label for="">Fecha Inicial</label>
                                                    <input type="datetime-local" class="form-control text-center" inline="true" id="minDate" name="minDate" readonly>
                                                </div>
                                                <div class="col-sm-2 floating-label enable-floating-label show-label date">
                                                    <label for="">Fecha Final</label>
                                                    <input type="datetime-local" class="form-control text-center number" id="maxDate" name="maxDate" readonly>
                                                </div>
                                                <div class="col-sm mt-2">
                                                    <button class="btn btn-info" type="submit" id="btnCreateProgramming" name="btnCreateProgramming">Crear</button>
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
                                        <h5 class="card-title">Programación</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="tblProgramming">

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

    <!-- <style>
        td {
            cursor: move;
        }
    </style> -->
    <script src="/global/js/global/lastText.js"></script>
    <script src="../planning/js/program/programming/tblProgramming.js"></script>
    <!--  <script src="/planning/js/basic/products/configProducts.js"></script> -->
    <script src="/planning/js/orders/configOrders.js"></script>
    <script src="/planning/js/basic/machines/configMachines.js"></script>
    <script src="/planning/js/program/programming/programming.js"></script>
    <script src="/planning/js/program/programming/configProgramming.js"></script>
    <script src="../global/js/global/validateExt.js"></script>
    <script src="/global/js/global/convertDate.js"></script>
</body>

</html>