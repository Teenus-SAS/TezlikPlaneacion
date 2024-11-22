<?php
if (!isset($_SESSION)) {
    session_start();
    if (sizeof($_SESSION) == 0)
        header('location: /');
}
if (sizeof($_SESSION) == 0)
    header('location: /');

$fechaActual = date("Y-m-d");
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
    <title>TezlikPlanner | Offices</title>
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
                            <div class="col-sm-5 col-xl-5">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Despachos</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Listado de Despachos</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-7">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3" style="padding-right: 0px;">
                                        <div class="card bg-success">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-calendar-event mr-1" id="lblTotal"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-warning">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="bi bi-file-earmark-x mr-1" id="lblCompleted"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-4" style="padding-right: 0px;">
                                        <div class="card bg-danger">
                                            <div class="card-body" style="padding: 10px;">
                                                <div class="media text-white">
                                                    <div class="media-body" style="text-align: center;">
                                                        <span class="text-uppercase font-size-12 font-weight-bold" style="font-size: smaller;"><i class="fas fa-arrow-down mr-1" id="lblLate"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-1" style="padding-right: 0px;">
                                        <button class="btn btn-warning" id="btnOpenSearchDate" name="btnOpenSearchDate"><i class="fas fa-search"></i></button>
                                    </div>
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

                <!-- page content -->
                <!-- <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblOffices">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active selectNavigation" id="deliver" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-activity" aria-selected="true">
                                            <i class="bi bi-truck mr-1"></i>Entregar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link selectNavigation" id="delivered" data-toggle="pill" href="javascript:;" role="tab" aria-controls="pills-projects" aria-selected="false">
                                            <i class="bi bi-check-square-fill mr-1"></i>Entregado
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="fixed-table-loading table table-hover" id="tblOffices">

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

    <script src="/planning/js/offices/configOffices.js"></script>
    <script src="/planning/js/offices/tblOffices.js"></script>
    <script src="/planning/js/offices/offices.js"></script>
</body>

</html>