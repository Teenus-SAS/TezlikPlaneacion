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
    <title>Tezlik - Admin | Users</title>
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
                                    <h3 class="mb-1 font-weight-bold text-dark">Usuarios</h3>
                                    <ol class="breadcrumb mb-3 mb-md-0">
                                        <li class="breadcrumb-item active">Usuarios por ultima fecha de logueo</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-7 col-xl-6">
                                <!-- <div class="form-inline justify-content-sm-end">
                                    <div class="card my-0 mx-3">
                                        <div class="card-body p-2">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input statusCompanies" id="status" checked>
                                                <label class="custom-control-label text-secondary" for="status"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-warning" id="btnNewCompany">Nueva empresa</button>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardAddProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="formAddProcess">
                                            <div class="gridx6pp">
                                                <label for="">Proceso</label>
                                                <label for="">Maquina</label>
                                                <label for="" class="text-center">t.alistamiento (min)</label>
                                                <label for="" class="text-center">t.operacion (min)</label>
                                                <label for="" class="text-center">t.total (min)</label>
                                                <label for=""></label>
                                                <select class="form-control" name="idProcess" id="idProcess"></select>
                                                <select class="form-control" name="idMachine" id="idMachine"></select>
                                                <input class="form-control text-center" type="text" name="enlistmentTime" id="enlistmentTime">
                                                <input class="form-control text-center" type="text" name="operationTime" id="operationTime">
                                                <input class="form-control text-center" type="text" name="totalTime" id="totalTime" disabled>
                                                <button class="btn btn-success" id="btnAddProcess">Adicionar</button>
                                            </div>
                                        </form>
                                        <div class="alert alert-warning mt-3" role="alert">
                                            Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="page-content-wrapper mt--45 mb-5 cardImportProductsProcess">
                    <div class="container-fluid">
                        <div class="row">
                            <form id="formImportProductProcess" enctype="multipart/form-data">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body pt-3 pb-0">
                                            <div class="gridx4ip">
                                                <div class="form-group floating-label enable-floating-label show-label mt-3 drag-area" style="margin-top:0px!important">
                                                    <input class="form-control" type="file" id="fileProductsProcess" accept=".xls,.xlsx">
                                                    <label for="formFile" class="form-label"> Importar Productos*Procesos</label>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-success" id="btnImportProductsProcess">Importar</button>
                                                </div>
                                                <div class="form-group floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:7px">
                                                    <button type="text" class="btn btn-info" id="btnDownloadImportsProductsProcess">Descarga Formato</button>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning" role="alert">
                                                Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> -->

                <!-- page content -->
                <div class="page-content-wrapper mt--45">
                    <div class="container-fluid">
                        <!-- Row 5 -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center" id="tblUsersLog" name="tblUsersLog">
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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

    <script src="/admin/js/users/usersLog.js"></script>
    <script src="/admin/js/users/tblUsersLog.js"></script>
</body>

</html>