<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/planning">
                            <i class="bx bxs-dashboard mr-1"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <?php if ($_SESSION['inventory'] == 1 && $_SESSION['plan_planning_inventory'] == 1) { ?>
                        <li class="nav-item inventories">
                            <a class="nav-link" href="/planning/inventory">
                                <i class="bx bx-tone mr-1"></i> Inventarios
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['plan_order'] == 1 && $_SESSION['plan_planning_order'] == 1) { ?>
                        <li class="nav-item orders">
                            <a class="nav-link" href="/planning/orders">
                                <i class="bx bx-tone mr-1"></i> Pedidos
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['program'] == 1 && $_SESSION['plan_planning_program'] == 1) { ?>
                        <li class="nav-item dropdown programs">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-layer mr-1"></i> Programa
                                <i class="bx bx-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/planning/consolidated">
                                    <i class="bx bxs-customize mr-1"></i>
                                    <span> Consolidado</span>
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                                <a class="dropdown-item" href="/planning/programming"><i class="bx bxs-customize mr-1"></i>
                                    <span> Programación</span>
                                    <i class="bx bx-chevron-right"></i></a>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['plan_load'] == 1 && $_SESSION['plan_planning_load'] == 1) { ?>
                        <li class="nav-item loads">
                            <a class="nav-link" href="/planning/">
                                <i class="bx bxs-customize mr-1"></i> Cargues
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['explosion_of_material'] == 1 && $_SESSION['plan_planning_explosion_of_material'] == 1) { ?>
                        <li class="nav-item explosionMaterials">
                            <a class="nav-link" href="/planning/explosion-materials">
                                <i class="bx bxs-customize mr-1"></i> Explosión Materiales
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="nav-item explosionMaterials">
                        <a class="nav-link" href="/planning/production-order">
                            <i class="bx bxs-customize mr-1"></i> Order Producción
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <?php if ($_SESSION['office'] == 1 && $_SESSION['plan_planning_office'] == 1) { ?>
                        <li class="nav-item offices">
                            <a class="nav-link" href="/planning/offices">
                                <i class="bx bxs-customize mr-1"></i> Despachos
                                <i class="bx bx-chevron-down"></i>
                            </a>
                        </li>
                    <?php } ?>

                    <?php //if ($_SESSION['office'] == 1 && $_SESSION['plan_planning_office'] == 1) { 
                    ?>
                    <li class="nav-item store">
                        <a class="nav-link" href="/planning/store">
                            <i class="bx bi bi-truck mr-1"></i> Almacen
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>
                    <?php //} 
                    ?>

                </ul>
            </div>
        </nav>
    </div>
</div>