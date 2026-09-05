<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!-- User details -->
        <div class="user-profile text-center mt-3">
            <div class="sidebar_logo">
                <img src="../assets/img/logo.jpeg" alt="" class="sidebar_logo_img avatar-md rounded-circle1">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1"><?php echo $_SESSION['employee_name']; ?></h4>
                <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i> Online</span>
            </div>
        </div>

        <!--- Sidemenu -->
        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul class="metismenu list-unstyled" id="side-menu">

                <li class="menu-title">Menu</li>

                <!-- Dashboard -->
                <li>
                    <a href="dashboard.php" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- My Profile -->
                <li>
                    <a href="employee-profile.php" class="waves-effect">
                        <i class="ri-user-line"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <!-- Team -->
                <li>
                    <a href="teams-list.php" class="waves-effect">
                        <i class="ri-team-line"></i>
                        <span>Teams</span>
                    </a>
                </li>



                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Area</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="area.php">Add Area</a></li>
                        <li><a href="area-list.php">Area List</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Doctor</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="doctor-add.php">Add Doctor</a></li>
                        <li><a href="doctor-list.php">Doctor List</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Clinic</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="clinic-add.php">Add Clinic</a>
                        </li>

                        <li>
                            <a href="clinic-list.php">Clinic List</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Chemist</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="chemist-add.php">Add Chemist</a>
                        </li>

                        <li>
                            <a href="chemist-list.php">Chemist List</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Product Master</span>
                    </a>

                    <ul class="sub-menu">

                        <li>
                            <a href="product-add.php">
                                Add Product
                            </a>
                        </li>

                        <li>
                            <a href="product-list.php">
                                Product List
                            </a>
                        </li>

                        <li>
                            <a href="product-sample-download.php">
                                Download Sample Format
                            </a>
                        </li>

                        <li>
                            <a href="product-import.php">
                                Import Products
                            </a>
                        </li>

                        <li>
                            <a href="product-export.php">
                                Export Products
                            </a>
                        </li>



                    </ul>

                </li>


                <!-- Master -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-database-2-line"></i>
                        <span>DCR Approval</span>
                    </a>

                    <ul class="sub-menu">

                        <li>
                            <a href="dcr-list.php">DCR List</a>
                        </li>

                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-award-line"></i>
                        <span>Tour Plan Approval</span>
                    </a>

                    <ul class="sub-menu">

                        <li>
                            <a href="tour-plan-list.php">Tour Plan List</a>
                        </li>



                    </ul>
                </li>

                <li>
                    <a href="attendance.php" class="waves-effect">
                        <i class="ri-calendar-check-line"></i>
                        <span>Attendance</span>
                    </a>
                </li>

                <!-- <li>
                    <a href="expense-list.php" class="waves-effect">
                        <i class="ri-money-dollar-circle-line"></i>
                        <span>Expenses</span>
                    </a>
                </li> -->



                <!-- Reports -->
                <!-- <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-bar-chart-box-line"></i>
                        <span>Reports</span>
                    </a>

                    <ul class="sub-menu">

                        <li>
                            <a href="tour-plan-report.php">
                                Tour Plan Report
                            </a>
                        </li>

                        <li>
                            <a href="day-plan-report.php">
                                Day Plan Report
                            </a>
                        </li>

                        <li>
                            <a href="dcr-report.php">
                                DCR Report
                            </a>
                        </li>

                        <li>
                            <a href="doctor-call-report.php">
                                Doctor Call Report
                            </a>
                        </li>

                        <li>
                            <a href="chemist-call-report.php">
                                Chemist Call Report
                            </a>
                        </li>

                        <li>
                            <a href="attendance-report.php">
                                Attendance Report
                            </a>
                        </li>

                    </ul>
                </li> -->


                <!-- Logout -->
                <li>
                    <a href="../auth/logout.php" class="waves-effect">
                        <i class="ri-logout-circle-r-line"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>

        </div>
        <!-- Sidebar -->


    </div>
</div>