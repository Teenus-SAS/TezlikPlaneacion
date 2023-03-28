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
    <title>Tezlik - Planning | Perfil</title>
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
                <div class="container py-5">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="picture-container">
                                        <div class="picture">
                                            <img id="avatar" src="" class="img-fluid" style="width: 100px;" />
                                            <input class="form-control" type="file" id="formFile">
                                        </div>
                                    </div>
                                    <h5 class="my-3" id="profileName"></h5>
                                </div>
                            </div>
                            <div class="card companyData">
                                <div class="card-body">
                                    <div class="picture-container mb-4">
                                        <div class="pictureC">
                                            <img id="logo" src="" class="img-fluid" style="width: 400px;" />
                                            <input class="form-control" type="file" id="formFileC">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">Compañia</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="company"></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">NIT</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="nit"></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">Ciudad</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="city"></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">Pais</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="country"></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">Telefono</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="phone"></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p class="font-weight-bold mb-0">Dirección</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="text-muted mb-0" id="address"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form id="formSaveProfile">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="" id="idUser" name="idUser" hidden>
                                            <div class="col-sm-3">
                                                <label class="form-label">Nombres *</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control text-center firstname" placeholder="" aria-label="First name" id="firstname" name="nameUser">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="form-label">Apellidos *</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control text-center" placeholder="" aria-label="Last name" id="lastname" name="lastnameUser">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="form-label">Cargo *</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control text-center" placeholder="" aria-label="Position" id="position" name="position" disabled>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="email" class="form-label">Email *</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="email" class="form-control text-center" id="email" name="emailUser">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="form-label">Nueva Contraseña</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="password" class="form-control text-center" placeholder="" aria-label="Password" id="password" name="password">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="form-label">Confirmar Contraseña</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="password" class="form-control text-center" placeholder="" aria-label="Confirm Password" id="conPassword" name="conPassword">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-end mb-2">
                                            <button type="button" class="btn btn-primary" id="btnSaveProfile">Actualizar Usuario</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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

    <script src="/global/js/global/companyData.js"></script>
    <!-- <script src="/global/js/global/searchData.js"></script> -->
    <script src="/global/js/global/loadImg.js"></script>
    <script src="/cost/js/profile/profile.js"></script>
</body>

</html>