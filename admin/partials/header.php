<!-- Begin Header -->
<header id="page-topbar" class="topbar-header">
    <div class="navbar-header">
        <div class="left-bar">
            <div class="d-flex justify-content-center">
                <div class="d-lg-none">
                    <a href="/admin" class="logo logo-dark">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="icon tezlik"></span>
                    </a>
                    <a href="/admin" class="logo logo-light">
                        <span class="logo-sm"><img src="/assets/images/favicon/favicon_tezlik.jpg" alt="Lettstart Admin"></span>
                    </a>
                </div>
                <div class="d-none d-lg-block">
                    <a href="/admin" class="logo logo-dark">
                        <span class="logo-lg"><img src="/assets/images/logo/logo_tezlik.png" alt="Logo tezlik"></span>
                    </a>
                    <a href="/admin" class="logo logo-light">
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
                                <div class="col-md-3" id="navAdmin">
                                    <h5 class="font-size-14 font-weight-600">Administrador</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="users"><a href="/admin/users-admins">Usuarios</a></li>
                                        <li class="plans"><a href="/admin/plans">Planes</a></li>
                                        <!-- <li class="binnacle"><a href="/admin/binnacle">Bitacora</a></li> -->
                                    </ul>
                                </div>
                                <div class="col-md-3" id="navConversion">
                                    <h5 class="font-size-14 font-weight-600">Conversiones</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li class="magnitudes"><a href="/admin/magnitudes">Magnitudes</a></li>
                                        <li class="units"><a href="/admin/units">Unidades</a></li>
                                    </ul>
                                </div>
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
                    <span class="d-none d-xl-inline-block ml-1 userName"><?php echo "{$_SESSION['name']} {$_SESSION['lastname']}"; ?></span>
                    <i class="bx bx-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div aria-labelledby="page-header-profile-dropdown" class="dropdown-menu-right dropdown-menu">
                    <a href="/admin/profile" class="dropdown-item">
                        <i class="bx bx-user mr-1"></i> Perfil
                    </a>
                    <a href="javascript: void(0);" onclick="loadContent('page-content','views/perfil/configuracion.php')" class="dropdown-item">
                        <i class="bx bx-wrench mr-1"></i> Configuración
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript: void(0);" class="text-danger dropdown-item logout">
                        <i class="bx bx-log-in mr-1 text-danger"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header End -->