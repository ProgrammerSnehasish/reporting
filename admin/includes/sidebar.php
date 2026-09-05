<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!-- User details -->
        <div class="user-profile text-center mt-2">
            <div class="sidebar_logo">
                <img src="../assets/img/logo.jpeg" alt="" class="sidebar_logo_img avatar-md rounded-circle1">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1"><?php echo $_SESSION['employee_name']; ?></h4>
                <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i> Online</span>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <!-- Dashboard -->
                <li>
                    <a href="dashboard.php" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Employee Management -->
                <li class="menu-title">Employee Management</li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-team-line"></i>
                        <span>Employees</span>
                    </a>

                    <ul class="sub-menu">

                        <li><a href="employees-add.php">Add Employee</a></li>

                        <li><a href="employees-list.php">Employee List</a></li>

                    </ul>
                </li>

                <!-- User Management -->
                <li class="menu-title">User Management</li>

                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-user-settings-line"></i>

                        <span>User Management</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="user-add.php">Create User</a></li>

                        <li><a href="user-list.php">User List</a></li>

                        <li><a href="user-mapping-add.php">User Mapping</a></li>

                        <li><a href="user-mapping-list.php">Mapping List</a></li>

                    </ul>

                </li>

                <!-- Masters -->

                <li class="menu-title">

                    Masters

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

                        <i class="ri-hospital-line"></i>

                        <span>Doctors</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="doctor-add.php">Add Doctor</a></li>

                        <li><a href="doctor-list.php">Doctor List</a></li>

                    </ul>

                </li>

                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-store-2-line"></i>

                        <span>Chemists/Stockists</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="chemist-add.php">Add Chemist</a></li>

                        <li><a href="chemist-list.php">Chemist List</a></li>

                    </ul>

                </li>


                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-building-line"></i>

                        <span>Clinics</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="clinic-add.php">Add Clinic</a></li>

                        <li><a href="clinic-list.php">Clinic List</a></li>

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

                <!-- Pain Management -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Pain Management</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">

                        <li>
                            <a href="pain-add.php">
                                <i class="ri-add-circle-line"></i>
                                Add Pain Product
                            </a>
                        </li>

                        <li>
                            <a href="pain-list.php">
                                <i class="ri-list-check-2"></i>
                                Pain Product List
                            </a>
                        </li>

                        <li>
                            <a href="pain-sample-download.php">
                                <i class="ri-download-2-line"></i>
                                Download Sample Format
                            </a>
                        </li>

                        <li>
                            <a href="pain-import.php">
                                <i class="ri-upload-2-line"></i>
                                Import Pain Products
                            </a>
                        </li>

                        <li>
                            <a href="pain-export.php">
                                <i class="ri-download-cloud-2-line"></i>
                                Export Pain Products
                            </a>
                        </li>

                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-line"></i>
                        <span>Gift Master</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="gift-add.php">Add Gift</a>
                        </li>

                        <li>
                            <a href="gift-list.php">Gift List</a>
                        </li>
                    </ul>
                </li>

                <!-- Planning -->

                <li class="menu-title">

                    Planning

                </li>

                <li>

                    <a href="tour-plan-list.php" class="waves-effect">

                        <i class="ri-road-map-line"></i>

                        <span>Tour Plans and Day Plans</span>

                    </a>

                </li>


                <!-- DCR -->

                <li class="menu-title">

                    DCR

                </li>

                <li>

                    <a href="dcr-list.php" class="waves-effect">

                        <i class="ri-file-list-3-line"></i>

                        <span>DCR List</span>

                    </a>


                </li>

                <li>

                    <a href="attendance.php" class="waves-effect">

                        <i class="ri-money-rupee-circle-line"></i>

                        <span>Attendance</span>

                    </a>

                </li>

                <!-- Expenses -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-money-dollar-circle-line"></i>
                        <span>Expenses</span>
                    </a>

                    <ul class="sub-menu">

                        <!--li>
                            <a href="expense-entry.php">Expense Entry</a>
                        </li-->

                        <li>
                            <a href="expense-list.php">Expense History</a>
                        </li>


                    </ul>
                </li>

                <!-- Reports -->

                <li class="menu-title">

                    Reports

                </li>

                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-bar-chart-grouped-line"></i>

                        <span>Reports</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="doctor-report.php">Doctor Report</a></li>

                        <li><a href="chemist-report.php">Chemist Report</a></li>

                        <li><a href="stockist-report.php">Stockist Report</a></li>

                        <li><a href="tour-report.php">Tour Report</a></li>

                        <li><a href="dcr-report.php">DCR Report</a></li>

                        <!-- <li><a href="expense-report.php">Expense Report</a></li> -->

                    </ul>

                </li>

                <!-- Communication -->

                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-bar-chart-grouped-line"></i>

                        <span>Communication</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="notice-add.php">Add Notice</a></li>

                        <li><a href="notice-list.php">Notice List</a></li>

                        <li><a href="message-add.php">Add Message</a></li>

                        <li><a href="message-list.php">Sent Messages</a></li>


                    </ul>

                </li>

                <li>

                    <a href="javascript:void(0);" class="has-arrow waves-effect">

                        <i class="ri-bar-chart-grouped-line"></i>

                        <span>Target Management</span>

                    </a>

                    <ul class="sub-menu">

                        <li><a href="target-month.php">Target Month</a></li>

                        <li><a href="target-assign.php">Assign Target</a></li>

                        <li><a href="target-list.php">Target List</a></li>


                    </ul>

                </li>


                <!-- Settings -->

                <li class="menu-title">

                    Settings

                </li>

                <li>

                    <a href="employee-profile.php" class="waves-effect">

                        <i class="ri-user-line"></i>

                        <span>My Profile</span>

                    </a>

                </li>

                <li>

                    <a href="change-password.php" class="waves-effect">

                        <i class="ri-lock-password-line"></i>

                        <span>Change Password</span>

                    </a>

                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>