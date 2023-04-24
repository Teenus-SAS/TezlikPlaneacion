<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - pass </title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
</head>

<body>
    <div class="backhome">
        <a href="index.html" class="avatar avatar-sm bg-primary text-white"><i class="bx bx-home-alt fs-sm"></i></a>
    </div>
    <!-- Begin Page -->
    <div class="page-wrapper">
        <!-- Begin main content -->
        <div class="main-content">
            <!-- content -->
            <div class="page-content">
                <div class="container d-flex justify-content-center align-items-center vh-100">
                    <div class="bg-white text-left p-5 mt-3 center col-md-6">
                        <div class="clearfix" style="text-align: center;">
                            <img src="/assets/images/logo/logo_tezlik1.png" height="55" class="" alt="logo tezlik">
                        </div>
                        <h5 class="mt-4">¿Olvido Contraseña?</h5>
                        <p class="text-muted mb-4">Ingrese su dirección de email y le enviaremos un correo electrónico con instrucciones para restablecer su contraseña.</p>
                        <form id="frmChangePasword" name="frmChangePasword" novalidate>
                            <div class="form-group floating-label">
                                <input type="email" class="form-control" name="email" id="email" />
                                <label for="email">Email</label>
                                <div class="validation-error d-none font-size-13">
                                    <p>Este campo es requerido</p>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary btn-block" data-effect="wave" type="submit" id="btnForgotPass">Enviar
                                </button>
                            </div>
                            <div class="clearfix text-center">
                                <a href="javascript:history.go(0);" class="text-primary">Volver al login</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <!-- main content End -->

            <!-- footer -->
            <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>

        </div>
        <!-- Page End -->
    </div>
    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
    <script src="/global/js/login/forgot-password.js"></script>

</body>

</html>