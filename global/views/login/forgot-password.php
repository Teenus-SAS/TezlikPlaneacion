<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tezlik - Forgot-Pasword </title>
    <link rel="shortcut icon" href="/assets/images/favicon/favicon_tezlik.jpg" type="image/x-icon" />

    <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet">
</head>

<body>
    <div class="backhome">
        <a href="/" class="avatar avatar-sm bg-primary text-white"><i class="bx bx-home-alt fs-sm"></i></a>
    </div>
    <!-- Begin Page -->
    <div class="auth-pages">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body p-4 p-md-5">
                            <div class="clearfix" style="text-align: center;">
                                <img src="/assets/images/logo/logo_tezlik1.png" height="55" class="" alt="logo tezlik">
                            </div>
                            <h5 class="mt-4">Resetear Contraseña</h5>
                            <p class="text-muted mb-4">Ingrese su dirección de email y le enviaremos un correo electrónico con instrucciones para restablecer su contraseña.</p>
                            <form id="recoveryForm" name="recoveryForm" novalidate>
                                <div class="form-group floating-label">
                                    <input type="email" class="form-control" name="validation-email" id="email" />
                                    <label for="email">Email</label>
                                    <div class="validation-error d-none font-size-13">
                                        <p>Ingrese una dirección de correo electrónico válida</p>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button class="btn btn-primary btn-block" data-effect="wave" type="submit">Resetear</button>
                                </div>
                            </form>
                            <!-- <div class="clearfix text-center">
                                <p>Volver a <a href="/index.php" class="font-weight-bold text-primary">Login</a></p>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- Page End -->

    <!--BEGIN BASE JS-->
    <script src="assets/js/vendor.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <!--BEGIN PAGE LEVEL JS-->
    <script src="assets/js/utils/colors.js"></script>
    <script src="assets/libs/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="assets/libs/jquery-validation/js/additional-methods.min.js"></script>

    <!--BEGIN PAGE JS-->
    <script src="assets/js/app.js"></script>
    <script src="/global/js/login/forgot-password.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

    <script>
        //Initialize form
        $('#recoveryForm').validate({
            focusInvalid: false,
            rules: {
                'validation-email': {
                    required: true,
                    email: true
                }
            },
            errorPlacement: function errorPlacement(error, element) {
                $(element).siblings(".validation-error").removeClass("d-none");
                if (error[0].textContent === "Please enter the same value again.") {
                    $(element).siblings(".validation-error").text("Password Mismatch")
                }
                return true
            },
            highlight: function(element) {
                var $el = $(element);
                var $parent = $el.parents('.form-group');
                $parent.addClass("invalid-field")
            },
            unhighlight: function(element) {
                var $el = $(element);
                var $parent = $el.parents('.form-group');
                $parent.removeClass("invalid-field");
                $(element).siblings(".validation-error").addClass("d-none")
            },
            submitHandler: function(form) {
                var formdata = $(form).serializeArray();
                var data = {};
                $(formdata).each(function(index, obj) {
                    data[obj.name] = obj.value;
                });
                forgotPass();
                $(form).trigger('reset')
                $(".floating-label").removeClass("enable-floating-label");
            }
        });
    </script>
</body>

</html>