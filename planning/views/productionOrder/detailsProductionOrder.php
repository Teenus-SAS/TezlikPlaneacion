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
						<div class="row justify-content-center">
							<div class="col-12 col-md-10 mt-3">
								<div class="card">
									<div class="card-body" style="padding-right: 35px; padding-left: 35px">
										<div class="toolbar hidden-print noImprimir">
											<div class="d-flex justify-content-end">
												<button class="btn btn-warning mr-2" id="btnImprimirQuote"><i class="fa fa-print"></i> Imprimir</button>
												<button class="btn btn-danger mr-2" id="btnNewSend"><i class="fa fa-mail-bulk"></i> Enviar</button>
											</div>
											<hr>
										</div>

										<div class="row">
											<div class="col-md-10 mt-3 mb-3 text-right">
												<h3 class="mb-1 font-weight-bold text-dark">Orden de Producción No.</h3>
												<p id="txtEDate"></p>
											</div>
											<div class="col-md-2 mt-2">
												<h1 id="txtNumOrder"></h1>
											</div>
										</div>						

										<div class="row">
											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha de Inicio de Producción<span class="text-danger">*</span></label>
													<input id="txtMinDate" type="text" class="form-control text-center">
												</div>
											</div>
											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha de Finalización<span class="text-danger">*</span></label>
													<input id="txtMaxDate" type="text" class="form-control text-center">
												</div>
											</div>

											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Cantidad a Producir<span class="text-danger">*</span></label>
													<input id="txtQuantityP" type="text" class="form-control text-center">
												</div>
											</div>

										</div>
										<hr>

										<div class="row py-4">
											<div class="col-10 mb-3">
												<h5 class=" font-weight-bold text-dark">1. INFORMACIÓN DEL PRODUCTO</h5>
											</div>
											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Referencia<span class="text-danger">*</span></label>
													<input id="txtReferenceP" type="text" class="form-control text-center">
												</div>
											</div>
											<div class="col-8">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Producto<span class="text-danger">*</span></label>
													<input id="txtNameP" type="text" class="form-control text-center">
												</div>
											</div>
										</div>
										<!-- <div class="row">
											<div class="col-4">
												<p class="font-weight-bold text-dark" id="txtReferenceP"> </p>
											</div>
											<div class="col-4">
												<p class="font-weight-bold text-dark" id="txtNameP"></p>
											</div>
											<div class="col-4">
												<p class="font-weight-bold text-dark" id="txtQuantityP"></p>
											</div>
										</div> -->
										<hr>
										<div class="row py-4">
											<div class="col-10">
												<h5 class="font-weight-bold text-dark">2. Materiales y Componentes</h5>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="table-responsive">
													<table class="fixed-table-loading table table-hover text-center">
														<thead class="thead-light">
															<tr>
																<th>Código</th>
																<th>Descripción</th>
																<th>Cantidad por Unidad de Producto</th>
																<th>Cantidad Total</th>
																<th>Unidad</th>
																<th>Recibido</th>
																<th>Pendiente</th>
															</tr>
														</thead>
														<tbody id="tblPOMaterialsBody">
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<hr>
										<div class="row py-4">
											<div class="col-10">
												<h5 class="font-weight-bold text-dark">3. Instrucciones de Producción</h5>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="table-responsive">
													<table class="fixed-table-loading table table-hover">
														<thead class="thead-light">
															<tr>
																<th>No</th>
																<th>Proceso de Producción</th>
																<th>Fecha Inicio</th>
																<th>Fecha Final</th>
															</tr>
														</thead>
														<tbody id="tblPOProcessBody">
														</tbody>
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
			</div>
			<!-- Main content end -->

			<!-- Footer -->
		</div>
		<?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
	</div>
	<!-- Page End -->

	<?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
	<script src="/planning/js/productionOrder/detailsProductionOrder.js"></script>
</body>

</html>