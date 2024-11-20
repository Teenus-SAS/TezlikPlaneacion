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
    <title>TezlikPlanner | Programming</title>
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
                        <div class="row align-items-center cardProgramming">
                            <div class="col-sm-5 col-xl-4">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Programa de Producción</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-8 form-inline justify-content-sm-end" id="divBottons">
                                <div class="col-sm-7 col-xl-3 form-inline justify-content-sm-end p-0 mr-2 cardBottonsGeneral">
                                    <button class="btn btn-warning w-100" id="btnNewProgramming" name="btnNewProgramming">Programar</button>
                                </div>
                                <div class="col-sm-7 col-xl-2 floating-label enable-floating-label show-label form-inline justify-content-sm-end mb-0 p-0 mr-2 cardBottons cardBottonsGeneral">
                                    <div id="machines1" class="w-100">
                                        <label>Máquina</label>
                                        <select class="form-control idMachine w-100" id="searchMachine">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-2 floating-label enable-floating-label show-label form-inline justify-content-sm-end mb-0 p-0 mr-2 cardSimulation cardBottonsGeneral" style="display: none;">
                                    <div class="col-xs-4 w-100">
                                        <label for="simulationType">Escenarios</label>
                                        <select name="simulationType" id="simulationType" class="form-control text-center w-100">
                                            <option disabled>Seleccionar</option>
                                            <option value="1" selected>1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-xl-2 form-inline justify-content-sm-end p-0 mr-2 cardSaveBottons cardBottonsGeneral">
                                    <button class="btn btn-primary w-100" id="btnSavePrograming" name="btnSavePrograming">Guardar</button>
                                </div>
                                <div class="col-sm-7 col-xl-2 form-inline p-0 cardAddOP cardBottonsGeneral" style="display: none;">
                                    <button class="btn btn-info w-100" id="btnAddOP" name="btnAddOP">Crear OP</button>
                                </div>
                            </div>

                        </div>
                        <div class="row align-items-center cardDashboardProgramming" style="display: none;">
                            <div class="col-sm-5 col-xl-4">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Indicadores</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active"></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-8 form-inline justify-content-sm-end">

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
                                                        <label for="">Cliente</label>
                                                        <input type="text" class="form-control text-center" id="client" name="client" readonly>
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
                                                        <button class="form-control text-center" type="number" name="quantityMP" id="quantityMP" data-toggle="tooltip" readonly></button>
                                                    </div>
                                                    <div class="col-sm-2 floating-label enable-floating-label show-label mb-2 p-1">
                                                        <label for="">Cantidad a Fabricar</label>
                                                        <input type="number" class="form-control text-center input" id="quantity" name="quantity">
                                                    </div>
                                                    <?php if ($_SESSION['flag_type_program'] == 1) { ?>
                                                        <div class="col-sm-3 floating-label enable-floating-label show-label mb-2 p-1 date cardFormProgramming2">
                                                            <label for="">Fecha Inicial</label>
                                                            <input type="date" class="form-control text-center" inline="true" id="minDate" name="minDate" readonly min="<?php
                                                                                                                                                                        //date_default_timezone_set('America/Bogota');
                                                                                                                                                                        echo date('Y-m-d'); ?>">
                                                        </div>
                                                        <div class="col-sm-3 floating-label enable-floating-label show-label" id="process" style="display: none;">
                                                            <label for="">Proceso</label>
                                                            <select class="form-control input selects" id="idProcess" name="idProcess">
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 floating-label enable-floating-label show-label" id="machines1" style="display: none;">
                                                            <label for="">Maquina</label>
                                                            <select class="form-control input selects" id="idMachine" name="idMachine">
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-1 mt-2">
                                                            <button class="btn btn-info" type="submit" id="btnCreateProgramming" name="btnCreateProgramming">Crear</button>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($_SESSION['flag_type_program'] == 0) { ?>
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
                                                                                                                                                                                    //date_default_timezone_set('America/Bogota');
                                                                                                                                                                                    echo date('Y-m-d'); ?>">
                                                        </div>
                                                        <div class="col-sm-3 floating-label enable-floating-label show-label date">
                                                            <label for="">Fecha Final</label>
                                                            <input type="datetime-local" class="form-control text-center" id="maxDate" name="maxDate" readonly min="<?php
                                                                                                                                                                    //date_default_timezone_set('America/Bogota');
                                                                                                                                                                    echo date('Y-m-d'); ?>">
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
                            <?php } ?>
                        </form>
                    </div>
                </div>

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="link-table" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-diagram-3 mr-1"></i>Programación
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="link-dashboard" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="fas fa-cogs mr-1"></i>Indicadores
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12 cardProgramming">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblProgramming">
                                                <tbody id="tblProgrammingBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 cardDashboardProgramming" style="display: none;">
                                <div class="card">
                                    <div class="card-body">

                                        <!-- Begin indicators and Graph Programation -->

                                        <div class="page-content-wrapper mt-3">
                                            <div class="container-fluid">
                                                <!-- Widget  -->
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-3">
                                                        <div class="card staffAvailableCard">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Personal Disponible</span>
                                                                        <h2 class="mb-0 mt-1 text-info" id="staffAvailableIndicator"></h2>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div id="t-rev"></div>
                                                                        <span class="text-danger font-weight-bold font-size-23">
                                                                            <i class='fas fa-users fs-lg' style="color: #7bb520;"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-3">
                                                        <div class="card machinesAvailableCard">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Máquinas Disponibles</span>
                                                                        <h2 class="mb-0 mt-1 text-info" id="machinesAvailableIndicator"></h2>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div id="t-rev"></div>
                                                                        <span class="text-warning font-weight-bold font-size-13">
                                                                            <i class='fas fa-cogs fs-lg'></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-3">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Moldes Disponibles</span>
                                                                        <h2 class="mb-0 mt-1 text-info" id="ordersNoMP"></h2>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div id="t-order"></div>
                                                                        <span class="text-info font-weight-bold font-size-13">
                                                                            <i class="fas fa-grip-vertical fs-lg"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-6 col-xl-3">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="media align-items-center">
                                                                    <div class="media-body">
                                                                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Pedidos en Despacho</span>
                                                                        <h2 class="mb-0 mt-1 text-warning" id="ordersDelivered"></h2>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div id="t-user"></div>
                                                                        <span class="text-success font-weight-bold font-size-13">
                                                                            <i class='fas fa-truck-loading fs-lg'></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <!-- Row 2-->
                                                <div class="row align-items-stretch">

                                                    <!-- Begin total orders vs clients chart -->
                                                    <div class="col-md-4 col-lg-8">
                                                        <div class="card orders-graph staffAvailableCardChart">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Personal Disponible y Requerido</h5>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="chart-container">
                                                                    <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                                                    <canvas id="staffAvailableChart"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Begin OC chart -->
                                                    <div class="col-md-4 col-lg-4">
                                                        <div class="card orders-graph machinesAvailableCardChart">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Disponibilidad de Máquinas</h5>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="chart-container">
                                                                    <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                                                    <canvas id="chartMachinesAvailable"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Begin Inventory classification chart -->
                                                    <!-- <div class="col-md-4 col-lg-4" style="height: fit-content;">
                                                        <div class="card orders-graph">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Moldes No Disponibles</h5>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="chart-container">
                                                                    <canvas id="moldUnavailable"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <!-- End total revenue chart -->
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 col-lg-12">
                                                        <div class="card orders-graph">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Capacidad de Producción</h5>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="chart-container">
                                                                    <canvas id="chartMachineCapacityProgrammed"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-4 col-lg-8">
                                                        <div class="card orders-graph">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Clientes con mayor número de pedidos</h5>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="chart-container">
                                                                    <canvas id="chartOrdersClients"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Begin total orders vs clients chart -->
                                        <!-- <div class="col-md-4 col-lg-9" style="height: fit-content;">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title"> </h5>
                                                </div>
                                                <div class="card-body pt-2">
                                                    <canvas id="chartProductsCost"></canvas>
                                                </div>
                                            </div>
                                        </div> -->
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
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="/global/js/global/lastText.js"></script>

    <script>
        flag_type_program = "<?= $_SESSION['flag_type_program'] ?>";
    </script>
    <script src="/planning/js/program/programming/tblProgramming.js"></script>
    <script src="/planning/js/program/programming/programming.js"></script>
    <script src="/planning/js/program/programming/configProgramming.js"></script>
    <script src="/planning/js/program/dashboard/dashboard.js"></script>
    <script src="/planning/js/program/dashboard/staffAvailable.js"></script>
    <script src="/planning/js/program/dashboard/machinesAvailable.js"></script>
    <script src="/planning/js/program/dashboard/machinesCapacityProgrammed.js"></script>
</body>

</html>