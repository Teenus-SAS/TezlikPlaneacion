<!-- Begin Header -->
<header id="page-topbar" class="topbar-header">
    <div class="navbar-header">
        <div class="left-bar">
            <div class="d-flex justify-content-center">
                <div class="d-lg-none">
                    <a href="/planning" class="logo logo-dark">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="icon tezlik"></span>
                    </a>
                    <a href="/planning" class="logo logo-light">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="Lettstart Admin"></span>
                    </a>
                </div>

                <div class="d-none d-lg-block">
                    <a href="/planning" class="logo logo-dark">
                        <span class="logo-lg"><img src="/assets/images/logo/logo_tezlik.png" alt="Logo tezlik"></span>
                    </a>
                    <a href="/planning" class="logo logo-light">
                        <span class="logo-lg"><img src="/assets/images/logo/logo_tezlik.png" alt="Lettstart Admin"></span>
                    </a>
                </div>
            </div>
            <a class="navbar-toggle collapsed" href="javascript:void(0)" data-toggle="collapse" data-target="#topnav-menu-content" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </a>
        </div>
        <div class="right-bar">
            <div class="dropdown-mega dropdown d-inline-flex ml-0 ml-sm-2">
                <a href="javascript:void(0)" data-toggle="dropdown" id="mega-dropdown" aria-haspopup="true" aria-expanded="false" class="btn header-item">
                    <i class="bx bx-cog bx-spin" style="font-size: 2em;"></i>
                </a>
                <div class="dropdown-megamenu dropdown-menu" aria-labelledby="mega-dropdown">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="row" id="nav">
                                <?php if (
                                    $_SESSION['create_mold'] != 0 || $_SESSION['planning_product'] != 0 || $_SESSION['planning_material'] != 0 ||
                                    $_SESSION['planning_machine'] != 0 || $_SESSION['planning_process'] != 0
                                ) { ?>
                                    <div class="col-md-3" id="navPlanBasics">
                                        <h5 class="font-size-14 font-weight-600">Básico</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php // if ($_SESSION['create_mold'] == 1) { 
                                            ?>
                                            <!-- <li class="invMolds"><a href="/planning/molds">Moldes</a></li> -->
                                            <?php //} 
                                            ?>
                                            <?php if ($_SESSION['planning_product'] == 1) { ?>
                                                <li class="planProducts"><a href="/planning/products">Inventario Productos</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['planning_material'] == 1) { ?>
                                                <li class="planMaterials"><a href="/planning/materials">Inventario Materias Prima</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['planning_machine'] == 1) { ?>
                                                <li class="planMachines"><a href="/planning/machines">Máquinas</a></li>
                                            <?php } ?>
                                            <li class="requisitions"><a href="/planning/requisitions">Control Requisiciones</a></li>
                                            <?php //if ($_SESSION['planning_process'] == 1) { 
                                            ?>
                                            <!-- <li class="planProcess"><a href="/planning/process">Procesos</a></li> -->
                                            <?php //} 
                                            ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <?php if (
                                    $_SESSION['planning_products_material'] != 0 || $_SESSION['planning_products_process'] != 0 ||
                                    $_SESSION['programs_machine'] != 0 || $_SESSION['cicles_machine'] != 0
                                ) { ?>
                                    <div class="col-md-3" id="navPlanSetting">
                                        <h5 class="font-size-14 font-weight-600">Configuración</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['planning_products_material'] == 1) { ?>
                                                <li class="planProductsMaterials"><a href="/planning/product-materials">Ficha Técnica Productos</a></li>
                                            <?php } ?>
                                            <?php //if ($_SESSION['planning_products_process'] == 1) { 
                                            ?>
                                            <!-- <li class="planProductsProcess"><a href="/planning/product-process">Ficha Técnica Procesos</a></li> -->
                                            <?php //} 
                                            ?>
                                            <?php if ($_SESSION['programs_machine'] == 1) { ?>
                                                <li class="planningMachines"><a href="/planning/planning-machines">Datos Programación Máquinas</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['cicles_machine'] == 1) { ?>
                                                <li class="planCiclesMachine"><a href="/planning/cicles-machines">Producto/Ciclos Maquina</a></li>
                                            <?php } ?>
                                            <li class="stock"><a href="/planning/stock">Stock Minimo</a></li>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <?php //if ($_SESSION['client'] != 0 ||$_SESSION['sale'] != 0) { 
                                ?>
                                <div class="col-md-3" id="navPlanGeneral">
                                    <h5 class="font-size-14 font-weight-600">General</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <?php //if ($_SESSION['client'] == 1) { 
                                        ?>
                                        <li class="clients"><a href="/planning/clients">Clientes</a></li>
                                        <?php //} 
                                        ?>
                                        <?php if ($_SESSION['sale'] == 1) { ?>
                                            <li class="sales"><a href="/planning/sales">Ventas</a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php //} 
                                ?>
                                <?php if ($_SESSION['planning_user'] != 0 || $_SESSION['client'] != 0 || $_SESSION['orders_type'] != 0) { ?>
                                    <div class="col-md-3" id="navPlanAdmin">
                                        <h5 class="font-size-14 font-weight-600">Administrador</h5>
                                        <ul class="list-unstyled megamenu-list">
                                            <?php if ($_SESSION['planning_user'] == 1) { ?>
                                                <li class="planUsers"><a href="/planning/users">Usuarios y Accesos</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['orders_type'] == 1) { ?>
                                                <li class="typeOrder"><a href="/planning/order-types">Tipo Pedidos</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none d-lg-block ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-search-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                    <i class="bx bx-search"></i>
                </button>
                <div aria-labelledby="page-header-search-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu">
                    <form class="p-3">
                        <div class="search-box">
                            <div class="position-relative">
                                <input type="text" placeholder="Search..." class="form-control form-control-sm">
                                <i class="bx bx-search icon"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- <div class="d-none d-lg-inline-flex ml-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-app-dropdown" aria-expanded="false" class="btn header-item notify-icon">
                    <i class="bx bx-customize"></i>
                </button>
                <div aria-labelledby="page-header-app-dropdown" class="dropdown-menu-lg dropdown-menu-right dropdown-menu">
                    <div class="px-lg-2">
                        <div class="row no-gutters">
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="/selector/assets/img/cost.png" alt="cost">
                                    <span>Costos</span>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript: void(0);" class="dropdown-icon-item">
                                    <img src="/selector/assets/img/time-planning.png" alt="planning">
                                    <span>Planeación</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="d-none d-lg-inline-flex ml-2">
                <button type="button" data-toggle="fullscreen" class="btn header-item notify-icon" id="full-screen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-notification-dropdown" aria-expanded="false" class="btn header-item notify-icon position-relative">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge badge-danger badge-pill notify-icon-badge" id="count"></span>
                </button>
                <div aria-labelledby="page-header-notification-dropdown" class="dropdown-menu-lg dropdown-menu-right p-0 dropdown-menu notify-scrollbar" style="max-height:280px; overflow-y: auto;">

                </div>
            </div>
            <div class="d-inline-flex ml-0 ml-sm-2 dropdown">
                <button data-toggle="dropdown" aria-haspopup="true" type="button" id="page-header-profile-dropdown" aria-expanded="false" class="btn header-item">
                    <?php
                    if (empty($_SESSION['avatar']))
                        $avatar = "/assets/images/users/empty_user.png";
                    else
                        $avatar = $_SESSION['avatar'];
                    ?>
                    <img id="hAvatar" src="<?php echo $avatar; ?>" alt="Header Avatar" class="avatar avatar-xs mr-0">
                    <span class="d-none d-xl-inline-block ml-1 userName"><?php if (!empty($_SESSION))
                                                                                echo "{$_SESSION['name']} {$_SESSION['lastname']}" ?>
                    </span>
                    <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                    <a href="/planning/profile" class="dropdown-item">
                        <i class="bx bx-user mr-1"></i> Perfil
                    </a>
                    <!-- <a href="javascript: void(0);" onclick="loadContent('page-content','views/perfil/configuracion.php')" class="dropdown-item">
                        <i class="bx bx-wrench mr-1"></i> Configuración
                    </a> -->
                    <div class="dropdown-divider"></div>
                    <a href="javascript: void(0);" class="text-danger dropdown-item logout">
                        <i class="bx bx-log-in mr-1 text-danger"></i> Salir
                    </a>
                </div>
            </div>
            <!-- <div class="d-inline-flex">
                <button type="button" id="layout" class="btn header-item notify-icon">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div> -->
        </div>
    </div>
</header>
<!-- Header End -->