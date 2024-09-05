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
    <title>TezlikPlanner | Dashboard</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(__DIR__) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body class="horizontal-navbar">
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin Header -->
        <?php include_once (__DIR__) . '/partials/header.php'; ?>

        <!-- Begin Left Navigation -->
        <?php include_once (__DIR__) . '/partials/nav.php'; ?>

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
                                    <h3 class="mb-1 font-weight-bold text-dark">Dashboard General</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Bienvenido</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Widget  -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Productos Agotados</span>
                                                <h2 class="mb-0 mt-1 text-danger" id="productStockout"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-danger font-weight-bold font-size-23">
                                                    <i class='fas fa-times-circle fs-lg'></i>
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
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Pedidos No Programados</span>
                                                <h2 class="mb-0 mt-1 text-danger" id="ordersNoProgramed"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-rev"></div>
                                                <span class="text-warning font-weight-bold font-size-13">
                                                    <i class='fas fa-calendar-times fs-lg'></i>
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
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Pedidos Sin Materia Prima</span>
                                                <h2 class="mb-0 mt-1 text-info" id="ordersNoMP"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-order"></div>
                                                <span class="text-info font-weight-bold font-size-13">
                                                    <i class="fas fa-calendar-check fs-lg"></i>
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
                            </div>
                            <!-- <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Gastos Generales</span>
                                                <h2 class="mb-0 mt-1" id="generalCost"></h2>
                                            </div>
                                            <div class="text-center">
                                                <div id="t-visitor"></div>
                                                <span class="text-danger font-weight-bold font-size-13">
                                                    <i class='bx bxs-pie-chart-alt-2 fs-lg'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <!-- Row 2-->
                        <div class="row align-items-stretch">
                            <!-- <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Clasificación Inventarios</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Tipo A</p>
                                                        <h4 class="mb-0" id="productsSold"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                        <i class="bx bx-layer fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Tipo B</p>
                                                        <h4 class="mb-0" id="salesRevenue"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                        <i class="bx bx-bar-chart-alt fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Tipo C</p>
                                                        <h4 class="mb-0" id="profitabilityAverage"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-success mr-0 align-self-center">
                                                        <i class="bx bx-chart fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->

                            <!-- Begin total orders vs clients chart -->
                            <div class="col-md-4 col-lg-6" style="height: fit-content;">
                                <div class="card orders-graph">
                                    <div class="chart-container">
                                        <div class="card-header">
                                            <h5 class="card-title">Pedidos x día</h5>
                                        </div>
                                        <div class="card-body pt-2">
                                            <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                            <canvas id="chartOrdersxDay"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Begin OC chart -->
                            <div class="col-md-4 col-lg-3" style="height: fit-content;">
                                <div class="card orders-graph">
                                    <div class="chart-container">
                                        <div class="card-header">
                                            <h5 class="card-title">Ordenes de Compra</h5>
                                        </div>
                                        <div class="card-body pt-2">
                                            <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                            <canvas id="pendingOCChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Begin Inventory classification chart -->
                            <div class="col-md-4 col-lg-3" style="height: fit-content;">
                                <div class="card orders-graph">
                                    <div class="chart-container">
                                        <div class="card-header">
                                            <h5 class="card-title">Entregas a tiempo</h5>
                                        </div>
                                        <div class="card-body pt-2">
                                            <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                            <canvas id="onTimeDeliveryChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Tiempos Fabricación (Prom)</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Alistamiento</p>
                                                        <h4 class="mb-0 number" id="enlistmentTime"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-info mr-0 align-self-center">
                                                        <i class="bx bxs-time fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Operación</p>
                                                        <h4 class="mb-0 number" id="operationTime"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-primary mr-0 align-self-center">
                                                        <i class="bx bxs-time-five fs-lg"></i>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item py-4">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <p class="text-muted mb-2">Tiempo Total Promedio</p>
                                                        <h4 class="mb-0" id="averageTotalTime"></h4>
                                                    </div>
                                                    <div class="avatar avatar-md bg-danger mr-0 align-self-center">
                                                        <i class='bx bx-error-circle fs-lg'></i>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                            <!-- End total revenue chart -->
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-lg-4" style="height: fit-content;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Clasificacion Inventario ABC</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                        <canvas id="chartClasificationABC"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-8" style="height: fit-content;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Clientes con mayor número de pedidos</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- <canvas id="chartTimeProcessProducts"></canvas> -->
                                        <canvas id="chartProductsCost"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costo Mano de Obra (Min)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartWorkForceGeneral"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total Costo </p>
                                                <h4 class="mb-0 font-weight-bold" id="totalCostWorkforce"></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Costo Carga Fabril</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chartFactoryLoadCost"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Tiempo Total</p>
                                                <h4 class="mb-0 font-weight-bold" id="factoryLoadCost"></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- </div> -->
                            <!-- <div class="row"> -->
                            <!-- <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Gastos Generales</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="chart-container">
                                            <canvas id="chartExpensesGenerals"></canvas>
                                            <div class="center-text">
                                                <p class="text-muted mb-1 font-weight-600">Total Gastos </p>
                                                <h4 class="mb-0 font-weight-bold" id="totalCost"></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" style="height: fit-content;">
                                <div class=" card">
                                    <div class="card-header">
                                        <h5 class="card-title">Tiempo Total de Fabricación por Producto (min)</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <canvas id="chartTimeProcessProducts"></canvas>
                                        <div class="center-text">
                                            <p class="text-muted mb-1 font-weight-600"></p>
                                            <h4 class="mb-0 font-weight-bold"></h4>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!-- <script src="/app/js/dashboard/indicatorsGeneral.js"></script>
				<script src="/app/js/dashboard/graphicsGeneral.js"></script> -->
            </div>
        </div>
        <!-- main content End -->
        <!-- footer -->
        <?php include_once dirname(__DIR__) . '/global/partials/footer.php'; ?>
        <!-- <div class="setting-sidebar">
			<div class="card mb-0">
				<div class="card-header">
					<h5 class="card-title dflex-between-center">
						Layouts
						<a href="javascript:void(0)"><i class="mdi mdi-close fs-sm"></i></a>
					</h5>
				</div>
				<div class="card-body">
					<div class="layout">
						<a href="index-horizontal.html">
							<img src="assets/images/horizontal.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Horizontal Layout</h6>
						</a>
					</div>
					<div class="layout">
						<a href="index.html">
							<img src="assets/images/vertical.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Vertical Layout</h6>
						</a>
					</div>
					<div class="layout">
						<a href="layout-dark-sidebar.html">
							<img src="assets/images/dark.png" alt="Lettstart Admin" class="img-fluid" />
							<h6 class="font-size-16">Dark Sidebar</h6>
						</a>
					</div>
				</div>
			</div>
		</div> -->
    </div>
    <!-- Page End -->
    <?php include_once dirname(__DIR__) . '/global/partials/scriptsJS.php'; ?>
    <script src="/planning/js/dashboard/indicatiorsGeneral.js"></script>
    <script src="/planning/js/dashboard/graphicsGeneral.js"></script>
    <script src="/planning/js/dashboard/indicators.js"></script>
    <script src="/planning/js/dashboard/deliveryOnTime.js"></script>
    <script src="/planning/js/dashboard/pendingOC.js"></script>
    <script src="/planning/js/dashboard/ordersPerDay.js"></script>
    <!-- <script src="/planning/js/users/usersAccess.js"></script> -->

    <!-- <script src="/global/js/global/loadContent.js"></script> -->
</body>

</html>