<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <!-- Start Navbar -->
        <ul class="navbar-nav">
            <!-- Sidebar Toggle -->
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            <!-- Main Links -->
            <li class="nav-item d-none d-md-block">
                <a href="{{ url('admin/dashboard') }}" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>

        <!-- End Navbar -->
        <ul class="navbar-nav ms-auto align-items-center">
            <!-- Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <!-- Messages Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-chat-text"></i>
                    <span class="badge text-bg-danger navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    @foreach ([['name'=>'Brad Diesel','img'=>'user1-128x128.jpg','text'=>'Call me whenever you can...','time'=>'4 Hours Ago','star'=>'text-danger'],
                              ['name'=>'John Pierce','img'=>'user8-128x128.jpg','text'=>'I got your message bro','time'=>'4 Hours Ago','star'=>'text-secondary'],
                              ['name'=>'Nora Silvester','img'=>'user3-128x128.jpg','text'=>'The subject goes here','time'=>'4 Hours Ago','star'=>'text-warning']] as $msg)
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-start">
                            <img src="{{ asset('admin/images/' . $msg['img']) }}" alt="User Avatar" class="rounded-circle me-3" width="50" height="50">
                            <div class="flex-grow-1">
                                <h6 class="dropdown-item-title mb-0">
                                    {{ $msg['name'] }}
                                    <span class="float-end fs-7 {{ $msg['star'] }}">
                                        <i class="bi bi-star-fill"></i>
                                    </span>
                                </h6>
                                <p class="fs-7 mb-0">{{ $msg['text'] }}</p>
                                <small class="text-muted"><i class="bi bi-clock-fill me-1"></i> {{ $msg['time'] }}</small>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>

            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge text-bg-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-envelope me-2"></i> 4 new messages
                        <span class="float-end text-secondary fs-7">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-people-fill me-2"></i> 8 friend requests
                        <span class="float-end text-secondary fs-7">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                        <span class="float-end text-secondary fs-7">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>

            <!-- Fullscreen Toggle -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
                </a>
            </li>

            <!-- User Dropdown -->
            @php
                $user = Auth::user();
                $defaultImage = asset(
                    $user->gender === 'Female'
                        ? 'admin/images/avatar-female.png'
                        : 'admin/images/avatar-male.png'
                );
                $userImage = $user->image ? asset('storage/' . $user->image) : $defaultImage;
            @endphp

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ $userImage }}"
                         class="user-image rounded-circle shadow" alt="User Image" width="32" height="32" />
                    <span class="d-none d-md-inline">{{ $user->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!-- User Info -->
                    <li class="user-header text-bg-primary text-center">
                        <img src="{{ $userImage }}"
                             class="rounded-circle shadow mb-2" alt="User Image" width="80" height="80" />
                        <p class="mb-0">{{ $user->name }}</p>
                        <small>Member since {{ $user->created_at->format('M Y') }}</small>
                    </li>

                    <!-- User Body -->
                    <li class="user-body px-3 py-2">
                        <div class="row text-center">
                            <div class="col-4"><a href="#">Followers</a></div>
                            <div class="col-4"><a href="#">Sales</a></div>
                            <div class="col-4"><a href="#">Friends</a></div>
                        </div>
                    </li>

                    <!-- Footer -->
                    <li class="user-footer d-flex justify-content-between px-3 py-2">
                        <a href="#" class="btn btn-outline-primary btn-sm">Profile</a>
                        <a href="{{ url('admin/logout') }}" class="btn btn-outline-danger btn-sm">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
