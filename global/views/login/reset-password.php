<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Reset-Password </title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-white text-left p-5 mt-3 center col-md-6">
            <div class="clearfix" style="text-align: center;">
                <img src="/assets/images/logo/logo_tezlik1.png" height="55" class="" alt="logo tezlik">
            </div>
            <h5 class="mt-4">Cambio de Contraseña</h5>
            <!-- <p class="text-muted mb-4">Login</p> -->
            <form id="frmChangePasword" name="frmChangePasword" novalidate>
                <div class="form-group floating-label">
                    <input type="password" class="form-control" name="inputNewPass" id="inputNewPass" />
                    <label for="inputNewPass">Nueva Contraseña</label>
                    <div class="validation-error d-none font-size-13">
                        <p>Este campo es requerido</p>
                    </div>
                </div>
                <div class="form-group floating-label">
                    <input type="password" class="form-control" name="inputNewPass1" id="inputNewPass1" />
                    <label for="inputNewPass1">Confirmar Contraseña</label>
                    <div class="validation-error d-none font-size-13">
                        <p>Este campo es requerido</p>
                    </div>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-primary btn-block" data-effect="wave" type="submit" id="btnChangePass">Cambiar Contraseña
                    </button>
                </div>
                <div class="clearfix text-center">
                    <a href="/" class="text-primary">Volver al login</a>
                </div>
            </form>
        </div>
    </div>

    <!--BEGIN BASE JS-->
    <script src="assets/js/vendor.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <!--BEGIN PAGE LEVEL JS-->
    <script src="assets/js/utils/colors.js"></script>
    <script src="assets/libs/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="assets/libs/jquery-validation/js/additional-methods.min.js"></script>

    <!--BEGIN PAGE JS-->
    <script src="assets/js/app.js"></script>
    <script src="/global/js/login/reset-password.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

</body>


</html>