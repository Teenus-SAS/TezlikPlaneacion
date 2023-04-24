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
    <title>Tezlik - Admin | Companies Licences</title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
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
            <!-- content -->
            <div class="page-content">
                <!-- page header -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-5 col-xl-6">
                                <div class="page-title">
                                    <h3 class="mb-1 font-weight-bold text-dark">Actualización Licencias</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Actualización e Información de Licencias</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <div class="form-inline justify-content-sm-end">
                                    <button class="btn btn-warning" id="newCompanyLicense">Nueva Licencia</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-content-wrapper mt--45 mb-5 cardCreateLicense">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddLicense">
                                            <div class="row align-items-center">
                                                <div class="col-sm">
                                                    <div class="form-group m-0">
                                                        <label for="company">Empresa</label>
                                                        <select name="company" class="form-control" id="company"></select>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group m-0">
                                                        <label for="license_start">Inicio Licencia</label>
                                                        <input id="license_start" name="license_start" type="date" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group m-0">
                                                        <label for="license_end">Final Licencia</label>
                                                        <input id="license_end" name="license_end" type="date" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group m-0">
                                                        <label for="quantityUsers">Usuarios</label>
                                                        <input id="quantityUsers" name="quantityUsers" type="number" class="form-control text-center">
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-group m-0">
                                                        <label for="">Tipo de Plan</label>
                                                        <select name="plan" id="plan" class="form-control"></select>
                                                    </div>
                                                </div>
                                                <div class="col-xs">
                                                    <button class="btn btn-primary" id="btnAddLicense" style="margin-top: 25px">Crear</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblCompaniesLicense">
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
    <!-- Page End -->

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>

    <script src="/admin/js/plans/configPlans.js"></script>
    <script src="/admin/js/companies/configCompanies.js"></script>
    <script src="/admin/js/licenses/tblCompaniesLicense.js"></script>
    <script src="/admin/js/licenses/companiesLicense.js"></script>
</body>

</html>