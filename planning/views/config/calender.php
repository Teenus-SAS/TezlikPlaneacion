<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="description" content="LetStart Admin is a full featured, multipurpose, premium bootstrap admin template built with Bootstrap 4 Framework, HTML5, CSS and JQuery.">
   <meta name="keywords" content="admin, panels, dashboard, admin panel, multipurpose, bootstrap, bootstrap4, all type of dashboards">
   <meta name="author" content="MatrrDigital">
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Tezlik - Planning | Calendar</title>
   <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon" />

   <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsCSS.php'; ?>

   <!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
   <link rel="stylesheet" href="/assets/css/icons.css" />
   <link rel="stylesheet" href="/assets/libs/wave-effect/css/waves.min.css" />
   <link rel="stylesheet" href="/assets/libs/owl-carousel/css/owl.carousel.min.css" />
   <!-- ================== BEGIN PAGE LEVEL END ================== -->
   <!-- ================== Plugins CSS  ================== -->
   <link href="/assets/libs/fullcalendar/fullcalendar.min.css" rel="stylesheet" />
   <link href="/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
   <!-- ================== Plugins CSS ================== -->
   <!-- ================== BEGIN APP CSS  ================== -->
   <link rel="stylesheet" href="/assets/css/bootstrap.css" />
   <link rel="stylesheet" href="/assets/css/styles.css" />
   <!-- ================== END APP CSS ================== -->

   <!-- ================== BEGIN PAGE LEVEL CSS START ================== -->
   <!-- <link rel="stylesheet" href="/assets/css/icons.css" />
  <link rel="stylesheet" href="/assets/libs/wave-effect/css/waves.min.css" />
  <link rel="stylesheet" href="/assets/libs/owl-carousel/css/owl.carousel.min.css" />
   -->
   <!-- ================== Plugins CSS  ================== -->
   <!-- <link href="/assets/libs/fullcalendar/fullcalendar.min.css" rel="stylesheet" />
  <link href="/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
   -->
   <!-- ================== BEGIN APP CSS  ================== -->
   <!-- <link rel="stylesheet" href="/assets/css/bootstrap.css" />
  <link rel="stylesheet" href="/assets/css/styles.css" />
   -->
   <!-- ================== BEGIN POLYFILLS  ================== -->
   <!--[if lt IE 9]>
     <script src="assets/libs/html5shiv/js/html5shiv.js"></script>
     <script src="assets/libs/respondjs/js/respond.min.js"></script>
  <![endif]-->
   <!-- ================== END POLYFILLS  ================== -->
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
                           <h3 class="mb-1 font-weight-bold text-dark">Calendario de Producción</h3>
                           <!-- <ol class="breadcrumb mb-3 mb-md-0">
                              <li class="breadcrumb-item active">Días Laborales y No laborales</li>
                           </ol> -->
                        </div>
                     </div>
                     <div class="col-sm-7 col-xl-6 form-inline justify-content-sm-end">
                        <!-- <div class="col-xs-2 mr-2">
                           <button class="btn btn-warning" id="btnNewPlanCiclesMachine" name="btnNewPlanCiclesMachine"><i class="bi bi-plus-circle"></i> Nuevo Ciclo Máquina</button>
                        </div>
                        <div class="col-xs-2 py-2 mr-2">
                           <button class="btn btn-info" id="btnImportNewPlanCiclesMachine" name="btnImportNewPlanCiclesMachine"> <i class="bi bi-cloud-arrow-up-fill"></i> Importar</button>
                        </div> -->
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
                           <!-- <div class="card-header">
                                        <h5 class="card-title">Ciclos</h5>
                                    </div> -->
                           <div class="card-body">
                              <div class="table-responsive">
                                 <div class="main-content mt-0">
                                    <!-- content -->
                                    <div class="page-content">
                                       <!-- page header -->
                                       <div class="page-title-box">
                                          <div class="container-fluid">
                                             <div class="row">
                                                <div class="col-12">
                                                   <div class="card">
                                                      <div class="card-body">
                                                         <div class="row">
                                                            <div class="col-xl-2 col-lg-3 col-6">
                                                               <img src="/assets/images/calender.svg" class="mr-4 align-self-center img-fluid " alt="cal" />
                                                            </div>
                                                            <div class="col-xl-10 col-lg-9">
                                                               <div class="mt-4 mt-lg-0">
                                                                  <h5 class="mt-0 mb-1 font-weight-bold">Bienvenido</h5>
                                                                  <p class="text-muted mb-2">Este calendario muestra los dias laborales y no laborales del área de producción.
                                                                     <!--  Click on event to see or edit the details. You can create new event by
                                                                     clicking on "Create New event" button or any cell available
                                                                     in calendar below. -->
                                                                  </p>
                                                                  <!-- <button class="btn btn-primary mt-2" data-effect="wave" id="btn-new-event" data-toggle="modal" data-target="#addeventmodal"><i class="uil-plus-circle"></i>
                                                                     Create New Event</button> -->
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div> <!-- end card body-->
                                                   </div> <!-- end card -->
                                                </div>
                                                <!-- end col-12 -->
                                             </div>
                                             <div class="row">
                                                <div class="col-12">
                                                   <div class="card">
                                                      <div class="card-body">
                                                         <div class="row">
                                                            <div class="col-xl-2 col-lg-3 col-md-4">
                                                               <!-- <h4 class="font-size-14">Nuevo Evento</h4>
                                                               <form method="post" id="add_event_form" class="mb-4">
                                                                  <input type="text" class="form-control form-control-sm" placeholder="Add new event..." />
                                                               </form> -->
                                                               <div id='external-events'>
                                                                  <h4 class="font-size-14">Eventos</h4>
                                                                  <div class='fc-event'>Día Laboral</div>
                                                                  <div class='fc-event bg-secondary'>Día Laboral 1/2 Tiempo</div>
                                                                  <div class='fc-event bg-warning'>No Laboral</div>
                                                                  <div class='fc-event bg-danger'>Festivo</div>
                                                                  <!-- <div class='fc-event bg-success'>My Event 5</div> -->
                                                               </div>
                                                               <!-- checkbox -->
                                                               <!--  <div class="custom-control custom-checkbox mt-3">
                                                                  <input type="checkbox" class="custom-control-input" id="drop-remove" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                                                  <label class="custom-control-label" for="drop-remove">Remove after drop</label>
                                                               </div> -->

                                                            </div>

                                                            <div id='fc_calendar' class="col-xl-10 col-lg-9 col-md-8 mt-4 mt-md-0"></div>

                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <!-- page content -->
                                       <div class="page-content-wrapper mt--45">
                                          <div class="container-fluid">


                                          </div>
                                       </div>
                                    </div>
                                    <!-- main content End -->
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
      <!-- main content End -->
      <!-- footer -->
      <?php include_once  dirname(dirname(dirname(__DIR__))) . '/global/partials/footer.php'; ?>
   </div>

   <?php include_once dirname(dirname(dirname(__DIR__))) . '/global/partials/scriptsJS.php'; ?>
   <!-- Page End -->
   <!-- ================== BEGIN BASE JS ================== -->
   <script src="/assets/js/vendor.min.js"></script>
   <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
   <!-- ================== END BASE JS ================== -->

   <!-- ================== BEGIN PAGE LEVEL JS ================== -->
   <script src="/assets/js/utils/colors.js"></script>
   <script src="/assets/libs/jquery-ui-dist/js/jquery-ui.min.js"></script>
   <script src="/assets/libs/moment/min/moment.min.js"></script>
   <script src="/assets/libs/fullcalendar/fullcalendar.min.js"></script>
   <script src="/assets/libs/select2/js/select2.min.js"></script>
   <script src="/assets/js/pages/calender.init.js"></script>
   <!-- ================== END PAGE LEVEL JS ================== -->
   <!-- ================== BEGIN PAGE JS ================== -->
   <script src="/assets/js/app.js"></script>
   <!-- ================== END PAGE JS ================== -->
</body>

</html>