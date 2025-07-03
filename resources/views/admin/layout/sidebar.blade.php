<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Brand -->
    <div class="sidebar-brand px-3 py-4 d-flex align-items-center">
        <a href="{{ url('admin/dashboard') }}" class="brand-link d-flex align-items-center gap-2">
            <img src="{{ asset('admin/images/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" style="width: 32px; height: 32px;" />
            <span class="brand-text fw-light fs-5">
                {{ auth()->user()->company ? auth()->user()->company->name : 'FieldMaster' }}
            </span>
        </a>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- User Management Module -->
                <li
                    class="nav-item {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i>
                        <p>User Management <i class="bi bi-chevron-right ms-auto"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/users') }}"
                                class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <i class="bi bi-person-fill me-2"></i>
                                <p>Manage Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/roles') }}"
                                class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock me-2"></i>
                                <p>Manage Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/permissions') }}"
                                class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                <i class="bi bi-key me-2"></i>
                                <p>Manage Permissions</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Trip Management -->
                <li
                    class="nav-item {{ request()->is('admin/trips*') || request()->is('admin/trip-types*') || request()->is('admin/travel-modes*') || request()->is('admin/purposes*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('admin/trips*') || request()->is('admin/trip-types*') || request()->is('admin/travel-modes*') || request()->is('admin/purposes*') ? 'active' : '' }}">
                        <i class="bi bi-truck-front me-2"></i>
                        <p>Trip Management <i class="bi bi-chevron-right ms-auto"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/trips') }}"
                                class="nav-link {{ request()->is('admin/trips*') ? 'active' : '' }}">
                                <i class="bi bi-truck me-2"></i>
                                <p>All Trips</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/trips/tourtype') }}"
                                class="nav-link {{ request()->is('admin/trips/tourtype*') ? 'active' : '' }}">
                                <i class="bi bi-tag me-2"></i>
                                <p>Trip Types</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/trips/travelmode') }}"
                                class="nav-link {{ request()->is('admin/trips/travelmode*') ? 'active' : '' }}">
                                <i class="bi bi-signpost me-2"></i>
                                <p>Travel Modes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/trips/purpose') }}"
                                class="nav-link {{ request()->is('admin/trips/purpose*') ? 'active' : '' }}">
                                <i class="bi bi-bullseye me-2"></i>
                                <p>Trip Purposes</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- HR Module -->
                <li class="nav-item {{ request()->is('admin/hr/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/hr/*') ? 'active' : '' }}">
                        <i class="bi bi-person-workspace me-2"></i>
                        <p>HR Module <i class="bi bi-chevron-right ms-auto"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/employees') }}"
                                class="nav-link {{ request()->is('admin/hr/employees*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill me-2"></i>
                                <p>Employees</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/designations') }}"
                                class="nav-link {{ request()->is('admin/hr/designations*') ? 'active' : '' }}">
                                <i class="bi bi-person-vcard me-2"></i>
                                <p>Designations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/attendance') }}"
                                class="nav-link {{ request()->is('admin/hr/attendance*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history me-2"></i>
                                <p>Attendance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->is('admin/hr/leave*') ? 'active' : '' }}">
                                <i class="bi bi-calendar-check me-2"></i>
                                <p>Leave Management <i class="bi bi-chevron-right ms-auto"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/pending') }}"
                                        class="nav-link {{ request()->is('admin/hr/leave/pending*') ? 'active' : '' }}">
                                        <i class="bi bi-hourglass-split me-2"></i>
                                        <p>Pending Leaves</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/approved') }}"
                                        class="nav-link {{ request()->is('admin/hr/leave/approved*') ? 'active' : '' }}">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <p>Approved Leaves</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>


                <!-- Party Management -->
                <li class="nav-item">
                    <a href="{{ url('admin/customers') }}"
                        class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        <p>Party (Customers)</p>
                    </a>
                </li>



                <!-- Orders -->
                <li class="nav-item">
                    <a href="{{ url('admin/orders') }}"
                        class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check me-2"></i>
                        <p>Orders</p>
                    </a>
                </li>

                <!-- Product Management -->
                <li class="nav-item">
                    <a href="{{ url('admin/products') }}"
                        class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i>
                        <p>Products</p>
                    </a>
                </li>

                <!-- Company Management -->
                <li class="nav-item">
                    <a href="{{ url('admin/companies') }}"
                        class="nav-link {{ request()->is('admin/companies*') ? 'active' : '' }}">
                        <i class="bi bi-buildings me-2"></i>
                        <p>Companies</p>
                    </a>
                </li>


            </ul>
        </nav>
    </div>
</aside>
