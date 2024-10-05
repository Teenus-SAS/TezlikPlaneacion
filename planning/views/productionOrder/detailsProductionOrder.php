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
	<title>TezlikSoftware | Production Order</title>
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
			<div class="page-content" id="invoice">
				<div class="page-content-wrapper">
					<div class="container-fluid">
						<!-- Row 5 -->
						<div class="row justify-content-center">
							<div class="col-12 col-md-10 mt-3">
								<div class="card">
									<div class="card-body cardHeader" style="padding-right: 35px; padding-left: 35px">
										<!-- <div class="d-flex justify-content-end">
												<button class="btn btn-warning mr-2" id="btnImprimirQuote"><i class="fa fa-print"></i> Imprimir</button>
												<button class="btn btn-danger mr-2" id="btnNewSend"><i class="fa fa-mail-bulk"></i> Enviar</button>
											</div>
											<hr> -->
										<div class="col-sm-12 col-xl-12 d-flex justify-content-end btnPrintPDF">
											<div class="col-xs-2 mt-2 mr-2" id="btnPdf">
												<button class="btn btn-success" onclick="printPDF()">Imprimir PDF</button>
											</div>
										</div>

										<hr>
										<div class=" row">
											<div class="col-sm-3">
												<img src="" id="logo" alt="logo_company" style="width: -webkit-fill-available;">
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
										<hr class="mb-0">
										<div class="row">
											<div class="col-12 mb-3">
												<h4 class=" font-weight-bold text-dark">1. Información del Pedido</h4>
											</div>
											<div class="col-2">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha Inicio</label>
													<input id="txtMinDate" type="text" class="form-control text-center" readonly>
												</div>
											</div>
											<div class="col-2">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Fecha Finalización</label>
													<input id="txtMaxDate" type="text" class="form-control text-center" readonly>
												</div>
											</div>

											<div class="col-2">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Cantidad</label>
													<input id="txtQuantityP" type="text" class="form-control text-center" readonly>
												</div>
											</div>

											<div class="col-6">
												<div class="form-group floating-label enable-floating-label show-label">
													<label for="">Cliente</label>
													<input id="nameClient" type="text" class="form-control text-center" readonly>
												</div>
											</div>

										</div>
										<hr class="mb-0">

										<div class="row py-4">
											<div class="col-10 mb-3">
												<h4 class=" font-weight-bold text-dark">2. Información del Producto</h4>
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

											<?php if ($_SESSION['flag_products_measure'] == 1) { ?>
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
													<input type="number" class="form-control text-center" id="usefulLength" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Ancho Total</label>
													<input type="number" class="form-control text-center" id="totalWidth" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Ventanilla</label>
													<input type="number" class="form-control text-center" id="window" readonly>
												</div>
											<?php } ?>
										</div>
										<hr class="mb-0">
										<div class="row py-4">
											<div class="col-10">
												<h4 class="font-weight-bold text-dark">3. Materiales y Componentes</h4>
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
																<th>Cantidad x Unidad</th>
																<th>Cantidad Total</th>
																<th>Unidad</th>
																<th>Recibido</th>
																<th>Pendiente</th>
																<th id="thActions"></th>
															</tr>
														</thead>
														<tbody id="tblPOMaterialsBody">
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<hr class="mb-0">

										<?php //if ($_SESSION['flag_products_measure'] == 1) { ?>
<!-- 											<div class="row py-4">
												<div class="col-12 mb-3">
													<h5 class="font-weight-bold text-dark">3. Acabados Especiales</h5>
												</div>

												<div class="col-2 form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Manija Trenzada</label>
													<input id="txtReferenceP" type="text" class="form-control text-center" readonly>
												</div>


												<div class="col-2 form-group floating-label enable-floating-label show-label">
													<label for="numberWorkers">Refuerzo Boca</label>
													<input id="txtNameP" type="text" class="form-control text-center" readonly>
												</div>

												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Cordon</label>
													<input type="number" class="form-control text-center" id="width" readonly></input>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Fondo Diamante</label>
													<input type="number" class="form-control text-center" id="high" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Otro</label>
													<input type="number" class="form-control text-center" id="length" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Manija Troq Sencilla</label>
													<input type="number" class="form-control text-center inputsCalc" name="usefulLength" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Refuerzo Base</label>
													<input type="number" class="form-control text-center inputsCalc" name="totalWidth" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">No Troq Manija</label>
													<input type="number" class="form-control text-center inputsCalc" name="totalWidth" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Fondo Cosido</label>
													<input type="number" class="form-control text-center" id="window" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Cant Refuerzo Base</label>
													<input type="number" class="form-control text-center" id="window" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Cant Refuerzo Lateral MTQ</label>
													<input type="number" class="form-control text-center" id="window" readonly>
												</div>
												<div class="col-sm-2 floating-label enable-floating-label show-label">
													<label for="">Cant Manijas Trenz</label>
													<input type="number" class="form-control text-center" id="window" readonly>
												</div>
											</div>
											<hr> -->
										<?php //} ?>

										<div class="row py-4">
											<div class="col-10">
												<h4 class="font-weight-bold text-dark">4. Proceso de Producción</h4>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="table-responsive">
													<table class="fixed-table-loading table table-hover">
														<thead class="thead-light">
															<tr>
																<th>No</th>
																<th>Proceso</th>
																<th>Maquina</th>
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

										<hr class="mb-0">

										<div class="cardExcOP">
											<div class="row py-4">
												<div class="col-10">
													<h4 class="font-weight-bold text-dark">5. Ejecución Producción</h4>
												</div>
											</div>
											<!-- formAddOPPArtial -->
											<form id="formAddOPPArtial">
												<div class="form-row">
													<div class="col-sm-3 floating-label enable-floating-label show-label">
														<label for="startDateTime">Inicio</label>
														<input type="datetime-local" class="form-control text-center" id="startDateTime" name="startDate">
													</div>
													<div class="col-sm-3 floating-label enable-floating-label show-label">
														<label for="endDateTime">Finalización</label>
														<input type="datetime-local" class="form-control text-center" id="endDateTime" name="endDate">
													</div>
													<!-- <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
														<label for="operator">Operario</label>
														<?php
														//$id_user = $_SESSION['idUser'];
														//$name = $_SESSION['name'];
														//$lastname = $_SESSION['lastname'];
														?>
														<input type="text" class="form-control" name="operator" id="operator" readonly value="<?php echo "$name $lastname" ?>">
													</div> -->
													<div class=" col-sm-2 floating-label enable-floating-label show-label">
														<label for="waste">Averias (Und)</label>
														<input type="number" class="form-control text-center" id="waste" name="waste">
													</div>
													<div class="col-sm-2 floating-label enable-floating-label show-label">
														<label for="quantityProduction">Total (Und)</label>
														<input type="number" class="form-control text-center" id="quantityProduction" name="partialQuantity">
													</div>
													<?php if ($_SESSION['flag_products_measure'] == 1) { ?>
														<div class="col-sm-2 floating-label enable-floating-label show-label">
															<label for="quantityKgProduction">Cantidad (Kg)</label>
															<input type="number" class="form-control text-center" id="quantityKgProduction" name="quantityKgProduction">
														</div>
													<?php } ?>
													<div class="col-sm-2 floating-label enable-floating-label show-label">
														<button class="btn btn-info mt-1" id="btnDeliverPartialOP">Entregar</button>
													</div>
												</div>
											</form>
											<div class="row">
												<div class="col-12">
													<div class="table-responsive">
														<table class="fixed-table-loading table table-hover text-center" id="tblPartialsDelivery">

														</table>
													</div>
												</div>
											</div>
										</div>

										<hr class="mb-0">

										<div class="cardExcOP">
											<div class="row py-4">
												<div class="col-10">
													<h4 class="font-weight-bold text-dark">6. Devolución Materia Prima No Usada en Producción</h4>
												</div>
											</div>
											<form id="formAddOPMP">
												<div class="form-row">
													<div class="col-sm-2 floating-label enable-floating-label show-label" style="margin-bottom:20px">
														<label for="material">Referencia</label>
														<select class="form-control refMaterial" name="refMaterial" id="refMaterial">
														</select>
													</div>
													<div class="col-sm-7 floating-label enable-floating-label show-label" style="margin-bottom:20px">
														<label for="material">Materia Prima</label>
														<select class="form-control material" name="idMaterial" id="material">
														</select>
													</div>
													<!-- <div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:20px">
														<label for="operator">Operario</label>
														<?php
														//$id_user = $_SESSION['idUser'];
														//$name = $_SESSION['name'];
														//$lastname = $_SESSION['lastname'];
														?>
														<input type="text" class="form-control" name="operator" id="operator" readonly value="<?php echo "$name $lastname" ?>">
													</div> -->
													<div class="col-sm-2 floating-label enable-floating-label show-label">
														<label for="">Cantidad</label>
														<input type="number" class="form-control text-center" id="quantityMP" name="quantity">
													</div>
													<div class="col-sm-1 floating-label enable-floating-label show-label">
														<button class="btn btn-info mt-1" id="btnAddOPMP">Entregar</button>
													</div>
												</div>
											</form>
											<div class="row">
												<div class="col-12">
													<div class="table-responsive">
														<table class="fixed-table-loading table table-hover text-center" id="tblOPMaterial">

														</table>
													</div>
												</div>
											</div>
										</div>

										<hr class="mb-0">

										<div class="row cardCloseOP">
											<div class="col-sm-12 mt-4">
												<button class="btn btn-warning w-100 d-block" id="btnCloseOP"><b>CERRAR ORDEN DE PRODUCCIÓN</b></button>
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
		<script>
			viewRawMaterial = 3;
		</script>
		<script src="/planning/js/basic/rawMaterials/configRawMaterials.js"></script>
		<script src="/planning/js/productionOrder/opPartial.js"></script>
		<script src="/planning/js/productionOrder/opMaterial.js"></script>
		<script src="/planning/js/productionOrder/detailsProductionOrder.js"></script>
		<script src="/global/js/global/companyData.js"></script>
		<script src="/global/js/global/printPdf.js"></script>
</body>

</html>