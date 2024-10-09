<div class="horizontal-topnav shadow-sm">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/planning" id="dashboard-link">
                            <i class="bi bi-bar-chart-line-fill mr-1" id="dashboard-icon"></i> Dashboards
                            <i class="bx bx-chevron-down"></i>
                        </a>
                    </li>

                    <?php if ($_SESSION['inventory'] == 1 && $_SESSION['plan_planning_inventory'] == 1) { ?>
                        <li class="nav-item planInventories">
                        <?php } else { ?>
                        <li class="nav-item planInventories" style="display: none;">
                        <?php } ?>
                        <a class="nav-link" href="/planning/inventory" id="inventory-link">
                            <i class="bx bxs-box mr-1" id="inventory-icon"></i> Inventarios
                            <i class="bx bx-chevron-down"></i>
                        </a>
                        </li>
                        <?php if ($_SESSION['requisition'] == 1) { ?>
                            <li class="planRequisitions">
                            <?php } else { ?>
                            <li class="nav-link planRequisitions" style="display: none;">
                            <?php } ?>
                            <a class="nav-link" href="/planning/requisitions" id="requisitions-link">
                                <i class="fas fa-shopping-cart mr-1" id="requisitions-icon"></i>Compras
                                <i class="bx bx-chevron-down"></i>
                            </a>
                            </li>

                            <?php if ($_SESSION['plan_order'] == 1 && $_SESSION['plan_planning_order'] == 1) { ?>
                                <li class="nav-item planOrders">
                                <?php } else { ?>
                                <li class="nav-item planOrders" style="display: none;">
                                <?php } ?>
                                <a class="nav-link" href="/planning/orders" id="orders-link">
                                    <i class="fas fa-clipboard-check mr-1" id="orders-icon"></i>Pedidos
                                    <i class="bx bx-chevron-down"></i>
                                </a>
                                </li>

                                <?php if ($_SESSION['program'] == 1 && $_SESSION['plan_planning_program'] == 1) { ?>
                                    <li class="nav-item dropdown planPrograms">
                                    <?php } else { ?>
                                    <li class="nav-item dropdown planPrograms" style="display: none;">
                                    <?php } ?>
                                    <a class="nav-link" href="/planning/programming" id="programming-link">
                                        <i class="bx bxs-customize mr-1" id="programming-icon"></i>Programar Producción
                                        <i class="bx bx-chevron-down"></i>
                                    </a>
                                    <!-- <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bxs-customize mr-1"></i>Programa Producción
                                        <i class="bx bx-chevron-down"></i>
                                    </a> -->
                                    <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown"> -->
                                    <!-- <a class="dropdown-item" href="/planning/consolidated">
                                        <i class="bx bxs-customize mr-1"></i>
                                        <span> Consolidado</span>
                                        <i class="bx bx-chevron-right"></i>
                                    </a> -->
                                    <!--  <a class="dropdown-item" href="/planning/programming"><i class="bx bxs-customize mr-1"></i>
                                            <span> Programación</span>
                                            <i class="bx bx-chevron-right"></i></a>
                                    </div> -->
                                    </li>

                                    <?php if ($_SESSION['explosion_of_material'] == 1 && $_SESSION['plan_planning_explosion_of_material'] == 1) { ?>
                                        <li class="nav-item planExplosionMaterials">
                                        <?php } else { ?>
                                        <li class="nav-item planExplosionMaterials" style="display: none;">
                                        <?php } ?>
                                        <a class="nav-link" href="/planning/explosion-materials" id="explosion-link">
                                            <i class="fas fa-bahai mr-1" id="explosion-icon"></i>Explosión MP<i class="bx bx-chevron-down"></i>
                                        </a>
                                        </li>


                                        <?php if ($_SESSION['production_order'] == 1 && $_SESSION['plan_production_order'] == 1) { ?>
                                            <li class="nav-item planProductionOrder">
                                            <?php } else { ?>
                                            <li class="nav-item planProductionOrder" style="display: none;">
                                            <?php } ?>
                                            <a class="nav-link" href="/planning/production-order" id="production-order-link">
                                                <i class="fas fa-tasks mr-1" id="production-order-icon"></i>OP<i class="bx bx-chevron-down"></i>
                                            </a>
                                            </li>

                                            <?php if ($_SESSION['office'] == 1 && $_SESSION['plan_planning_office'] == 1) { ?>
                                                <li class="nav-item planOffices">
                                                <?php } else { ?>
                                                <li class="nav-item planOffices" style="display: none;">
                                                <?php } ?>
                                                <a class="nav-link" href="/planning/store" id="store-link">
                                                    <i class="fas fa-warehouse mr-1" id="store-icon"></i>Almacen<i class="bx bx-chevron-down"></i>
                                                </a>
                                                </li>

                                                <?php if ($_SESSION['store'] == 1 && $_SESSION['plan_store'] == 1) { ?>
                                                    <li class="nav-item planStore">
                                                    <?php } else { ?>
                                                    <li class="nav-item planStore" style="display: none;">
                                                    <?php } ?>
                                                    <a class="nav-link" href="/planning/offices" id="offices-link">
                                                        <i class="fas fa-truck mr-1" id="offices-icon"></i>Despachos<i class="bx bx-chevron-down"></i>
                                                    </a>

                                                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>