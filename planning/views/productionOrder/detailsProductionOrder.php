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
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Tezlik - Cost | Details Production Order</title>
	<link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />
	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
	<style>
		@media print {
			.invoice table th {
				white-space: nowrap;
				font-weight: 400;
				font-size: 10px;
			}

			.dtitle {
				font-size: 10px;
			}

			.invoice table tfoot tr:last-child td {
				color: #0d6efd;
				font-size: 1em;
			}

			.invoice table tfoot td {
				background: 0 0;
				border-bottom: none;
				white-space: nowrap;
				text-align: right;
				padding: 10px 20px;
				font-size: 1em;
				border-top: 1px solid #aaa;
			}
		}
	</style>
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
			<!-- Content -->
			<div class="page-content">
				<div class="page-content-wrapper">
					<div class="container-fluid">
						<!-- Row 5 -->
						<!-- <div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-body align-items-center" style="padding-right: 100px; padding-left: 100px">
										<div id="invoice">
											<div class="invoice py-4">
												<div class="row mb-4">
													<div class="col-sm-12 col-xl-8">
														<div class="page-title">
															<h3 class="mb-1 font-weight-bold text-dark">Orden de Producción</h3>
														</div>
													</div>
													<div class="col-sm-2 col-xl-2">
														<p> [Número de Orden]</p>
													</div>
												</div>
												<div class="row mb-4">
													<div class="col-sm-12 col-xl-6">
														<p class="font-weight-bold text-dark">Fecha de Emisión: </p>
													</div>
													<div class="w-100"></div>
													<div class="col-sm-12 col-xl-6 py-2">
														<p class="font-weight-bold text-dark">Fecha de Inicio de Producción: </p>
													</div>
													<div class="col-sm-12 col-xl-6">
														<p class="font-weight-bold text-dark">Fecha Estimada de Finalización: </p>
													</div>
												</div>
												<div class="row mb-4">
													<div class="col-sm-12 col-xl-8 py-2">
														<div class="page-title">
															<h4 class="mb-1 font-weight-bold text-dark">1. Información del Producto</h4>
														</div>
													</div>
													<div class="w-100"></div>
													<div class="col-sm-4 col-xl-4">
														<p> [Código del Producto]</p>
													</div>
													<div class="col-sm-4 col-xl-4">
														<p> [Nombre del Producto]</p>
													</div>
													<div class="col-sm-4 col-xl-4">
														<p> [Cantidad a Producir]</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> -->
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-body">
										<div class="row justify-content-around">
											<div class="col-6">
												<div class="page-title">
													<h3 class="mb-1 font-weight-bold text-dark">Orden de Producción</h3>
												</div>
											</div>
											<div class="col-2">
												<p> [Número de Orden]</p>
											</div>
										</div>
										<div class="row justify-content-around">
											<div class="col-6">
												<p class="font-weight-bold text-dark">Fecha de Emisión: </p>
											</div>
											<div class="w-100"></div>
											<div class="col-sm-12 col-xl-6 py-2">
												<p class="font-weight-bold text-dark">Fecha de Inicio de Producción: </p>
											</div>
											<div class="col-sm-12 col-xl-6">
												<p class="font-weight-bold text-dark">Fecha Estimada de Finalización: </p>
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
		</div>
		<?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
	</div>
	<!-- Page End -->

	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>

</body>

</html>