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

										<!-- <div class="row">
											

										</div> -->
										<div class="row">
											<div class="col-sm-3">
												<div id="imgClient">
													<img src="data:image/png;base64,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" width="100px">
												</div>
											</div>
											<div class="col-sm-9">
												<div class="row">
													<div class="col-md-10 text-right">
														<h3 class="mb-1 font-weight-bold text-dark">Orden Producción No.</h3>
													</div>
													<div class="col-md-2">
														<h3 class="text-center" id="txtNumOP">001</h3>
													</div>

													<div class="col-md-10 text-right">
														<h3 class="mb-1 font-weight-bold text-dark">Pedido No.</h3>
													</div>
													<div class="col-md-2">
														<h3 class="text-center" id="txtNumOrder"></h3>
													</div>
													<div class="col-md-12 text-right">
														<p id="txtEDate"></p>
													</div>
												</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha de Inicio de Producción</label>
													<input id="txtMinDate" type="text" class="form-control text-center" readonly>
												</div>
											</div>
											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha de Finalización</label>
													<input id="txtMaxDate" type="text" class="form-control text-center" readonly>
												</div>
											</div>

											<div class="col-4">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Cantidad a Producir</label>
													<input id="txtQuantityP" type="text" class="form-control text-center" readonly>
												</div>
											</div>

											<div class="col-12">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="">Cliente</label>
													<input id="nameClient" type="text" class="form-control text-center" readonly>
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
													<label for="numberWorkers">Referencia</label>
													<input id="txtReferenceP" type="text" class="form-control text-center" readonly>
												</div>
											</div>
											<div class="col-8">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Producto</label>
													<input id="txtNameP" type="text" class="form-control text-center" readonly>
												</div>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Ancho</label>
												<input type="number" class="form-control text-center" id="width" readonly></input>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Alto</label>
												<input type="number" class="form-control text-center" id="high" readonly>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Largo</label>
												<input type="number" class="form-control text-center" id="length" readonly>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Largo Útil</label>
												<input type="number" class="form-control text-center inputsCalc" name="usefulLength" readonly>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Ancho Total</label>
												<input type="number" class="form-control text-center inputsCalc" name="totalWidth" readonly>
											</div>
											<div class="col-sm-2 floating-label enable-floating-label show-label">
												<label for="">Ventanilla</label>
												<input type="number" class="form-control text-center" id="window" readonly>
											</div>
										</div>
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