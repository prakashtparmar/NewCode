<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand px-3 py-4 d-flex align-items-center">
        <a href="{{ url('admin/dashboard') }}" class="brand-link d-flex align-items-center gap-2">
            <img src="{{ asset('admin/images/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" style="width: 32px; height: 32px;" />
            <span class="brand-text fw-light fs-5">AdminLTE 4</span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                {{-- Users, Roles & Permissions --}}
                <li
                    class="nav-item {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'active' : '' }}"
                        aria-expanded="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'true' : 'false' }}">
                        <span>
                            <i class="nav-icon bi bi-people-fill me-2"></i>
                            User Management
                        </span>
                        <i
                            class="nav-arrow bi {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                    </a>
                    <ul class="nav nav-treeview ps-4"
                        style="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ url('admin/users') }}"
                                class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-person-fill me-2 fs-6"></i>
                                <p class="mb-0">Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/roles') }}"
                                class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-shield-lock-fill me-2 fs-6"></i>
                                <p class="mb-0">Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/permissions') }}"
                                class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-key-fill me-2 fs-6"></i>
                                <p class="mb-0">Permissions</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Customers --}}
                <li class="nav-item {{ request()->is('admin/customers*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/customers') }}"
                        class="nav-link d-flex align-items-center {{ request()->is('admin/customers*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-lines-fill me-2"></i>
                        <p class="mb-0">Customers</p>
                    </a>
                </li>

                {{-- Trips --}}
                <li class="nav-item {{ request()->is('admin/trips*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/trips') }}"
                        class="nav-link d-flex align-items-center {{ request()->is('admin/trips*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-car-front-fill me-2"></i>
                        <p class="mb-0">Trips</p>
                    </a>
                </li>

                {{-- Orders --}}
                <li class="nav-item {{ request()->is('admin/orders*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/orders') }}"
                        class="nav-link d-flex align-items-center {{ request()->is('admin/orders*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-basket3-fill me-2"></i>
                        <p class="mb-0">Orders</p>
                    </a>
                </li>

                {{-- Products --}}
                <li class="nav-item {{ request()->is('admin/products*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/products') }}"
                        class="nav-link d-flex align-items-center {{ request()->is('admin/products*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-box-seam me-2"></i>
                        <p class="mb-0">Products</p>
                    </a>
                </li>

                {{-- HR Modules --}}
                <li class="nav-item {{ request()->is('admin/hr/*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/*') ? 'active' : '' }}"
                        aria-expanded="{{ request()->is('admin/hr/*') ? 'true' : 'false' }}">
                        <span>
                            <i class="nav-icon bi bi-person-workspace me-2"></i>
                            HR Modules
                        </span>
                        <i
                            class="nav-arrow bi {{ request()->is('admin/hr/*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                    </a>
                    <ul class="nav nav-treeview ps-4"
                        style="{{ request()->is('admin/hr/*') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/employees') }}"
                                class="nav-link {{ request()->is('admin/hr/employees*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people-fill me-2 fs-6"></i>
                                <p class="mb-0">Employees</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/attendance') }}"
                                class="nav-link {{ request()->is('admin/hr/attendance*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-clock-history me-2 fs-6"></i>
                                <p class="mb-0">Attendance</p>
                            </a>
                        </li>

                        {{-- Live Leave Requests --}}
                        <li class="nav-item">
                            <a href="#"
                                class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/leave*') ? 'active' : '' }}"
                                aria-expanded="{{ request()->is('admin/hr/leave*') ? 'true' : 'false' }}">
                                <span>
                                    <i class="nav-icon bi bi-calendar2-check me-2 fs-6"></i>
                                    Live Leave Requests
                                </span>
                                <i
                                    class="nav-arrow bi {{ request()->is('admin/hr/leave*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4"
                                style="{{ request()->is('admin/hr/leave*') ? 'display: block;' : 'display: none;' }}">
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/pending') }}"
                                        class="nav-link {{ request()->is('admin/hr/leave/pending*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-hourglass-split me-2 fs-6"></i>
                                        <p class="mb-0">Pending</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/approved') }}"
                                        class="nav-link {{ request()->is('admin/hr/leave/approved*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-check-circle me-2 fs-6"></i>
                                        <p class="mb-0">Approved</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/rejected') }}"
                                        class="nav-link {{ request()->is('admin/hr/leave/rejected*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-x-circle me-2 fs-6"></i>
                                        <p class="mb-0">Rejected</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Allowances --}}
                        <li class="nav-item">
                            <a href="#"
                                class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/allowances*') ? 'active' : '' }}"
                                aria-expanded="{{ request()->is('admin/hr/allowances*') ? 'true' : 'false' }}">
                                <span>
                                    <i class="nav-icon bi bi-cash-stack me-2 fs-6"></i>
                                    Allowances
                                </span>
                                <i
                                    class="nav-arrow bi {{ request()->is('admin/hr/allowances*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4"
                                style="{{ request()->is('admin/hr/allowances*') ? 'display: block;' : 'display: none;' }}">
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/allowances/create') }}"
                                        class="nav-link {{ request()->is('admin/hr/allowances/create*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-plus-circle me-2 fs-6"></i>
                                        <p class="mb-0">Create</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/allowances') }}"
                                        class="nav-link {{ request()->is('admin/hr/allowances') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul me-2 fs-6"></i>
                                        <p class="mb-0">List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Claims --}}
                        <li class="nav-item">
                            <a href="#"
                                class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/claims*') ? 'active' : '' }}"
                                aria-expanded="{{ request()->is('admin/hr/claims*') ? 'true' : 'false' }}">
                                <span>
                                    <i class="nav-icon bi bi-file-earmark-medical me-2 fs-6"></i>
                                    Claims
                                </span>
                                <i
                                    class="nav-arrow bi {{ request()->is('admin/hr/claims*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4"
                                style="{{ request()->is('admin/hr/claims*') ? 'display: block;' : 'display: none;' }}">
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/claims/create') }}"
                                        class="nav-link {{ request()->is('admin/hr/claims/create*') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-plus-circle me-2 fs-6"></i>
                                        <p class="mb-0">Create</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/claims') }}"
                                        class="nav-link {{ request()->is('admin/hr/claims') ? 'active' : '' }}">
                                        <i class="nav-icon bi bi-list-ul me-2 fs-6"></i>
                                        <p class="mb-0">List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Other HR submodules placeholder --}}
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/other') }}"
                                class="nav-link {{ request()->is('admin/hr/other*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-sliders me-2 fs-6"></i>
                                <p class="mb-0">Other HR Modules</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Report Modules --}}
                <li class="nav-item {{ request()->is('admin/reports/*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/reports/*') ? 'active' : '' }}"
                        aria-expanded="{{ request()->is('admin/reports/*') ? 'true' : 'false' }}">
                        <span>
                            <i class="nav-icon bi bi-file-earmark-bar-graph-fill me-2"></i>
                            Reports
                        </span>
                        <i
                            class="nav-arrow bi {{ request()->is('admin/reports/*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                    </a>
                    <ul class="nav nav-treeview ps-4"
                        style="{{ request()->is('admin/reports/*') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ url('admin/reports/sales') }}"
                                class="nav-link {{ request()->is('admin/reports/sales*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle me-2 fs-6"></i>
                                <p class="mb-0">Sales Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/reports/finance') }}"
                                class="nav-link {{ request()->is('admin/reports/finance*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle me-2 fs-6"></i>
                                <p class="mb-0">Finance Report</p>
                            </a>
                        </li>
                        <!-- Add more report submodules as needed -->
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
