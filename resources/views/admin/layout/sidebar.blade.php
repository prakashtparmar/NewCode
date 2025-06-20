<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand px-3 py-4 d-flex align-items-center">
        <a href="{{ url('admin/dashboard') }}" class="brand-link d-flex align-items-center gap-2">
            <img src="{{ asset('admin/images/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" style="width: 32px; height: 32px;" />
            <span class="brand-text fw-light fs-5">
                {{ auth()->user()->company ? auth()->user()->company->name : 'FieldMaster' }}
            </span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <nav class="mt-3">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                {{-- User Management --}}
                <li class="nav-item {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'active' : '' }}">
                        <span>
                            <i class="bi bi-people-fill me-2"></i> User Management
                        </span>
                        <i class="bi {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                    </a>
                    <ul class="nav nav-treeview ps-4" style="{{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ url('admin/users') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <i class="bi bi-person-fill me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/roles') }}" class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock me-2"></i> Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/permissions') }}" class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
                                <i class="bi bi-key me-2"></i> Permissions
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Companies --}}
                <li class="nav-item {{ request()->is('admin/companies*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/companies') }}" class="nav-link {{ request()->is('admin/companies*') ? 'active' : '' }}">
                        <i class="bi bi-buildings me-2"></i> Companies
                    </a>
                </li>

                {{-- Customers --}}
                <li class="nav-item {{ request()->is('admin/customers*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/customers') }}" class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill me-2"></i> Customers
                    </a>
                </li>

                {{-- Trips --}}
                <li class="nav-item {{ request()->is('admin/trips*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/trips') }}" class="nav-link {{ request()->is('admin/trips*') ? 'active' : '' }}">
                        <i class="bi bi-truck-front me-2"></i> Trips
                    </a>
                </li>

                {{-- Orders --}}
                <li class="nav-item {{ request()->is('admin/orders*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/orders') }}" class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check me-2"></i> Orders
                    </a>
                </li>

                {{-- Products --}}
                <li class="nav-item {{ request()->is('admin/products*') ? 'menu-open' : '' }}">
                    <a href="{{ url('admin/products') }}" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Products
                    </a>
                </li>

                {{-- HR Modules --}}
                <li class="nav-item {{ request()->is('admin/hr/*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/*') ? 'active' : '' }}">
                        <span>
                            <i class="bi bi-person-workspace me-2"></i> HR Modules
                        </span>
                        <i class="bi {{ request()->is('admin/hr/*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                    </a>
                    <ul class="nav nav-treeview ps-4" style="{{ request()->is('admin/hr/*') ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/employees') }}" class="nav-link {{ request()->is('admin/hr/employees*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill me-2"></i> Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/hr/attendance') }}" class="nav-link {{ request()->is('admin/hr/attendance*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history me-2"></i> Attendance
                            </a>
                        </li>

                        {{-- Leave Requests --}}
                        <li class="nav-item">
                            <a href="#"
                                class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/hr/leave*') ? 'active' : '' }}">
                                <span>
                                    <i class="bi bi-calendar-check me-2"></i> Live Leave Requests
                                </span>
                                <i class="bi {{ request()->is('admin/hr/leave*') ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4" style="{{ request()->is('admin/hr/leave*') ? 'display: block;' : 'display: none;' }}">
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/pending') }}" class="nav-link {{ request()->is('admin/hr/leave/pending*') ? 'active' : '' }}">
                                        <i class="bi bi-hourglass-split me-2"></i> Pending
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/hr/leave/approved') }}" class="nav-link {{ request()->is('admin/hr/leave/approved*') ? 'active' : '' }}">
                                        <i class="bi bi-check-circle-fill me-2"></i> Approved
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
