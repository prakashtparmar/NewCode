@extends('admin.layout.layout')

@section('content')

<main class="app-main">

    {{-- Page Header --}}
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Admin Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create New Admin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline mb-4">

                        {{-- Card Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">Create New Admin</div>
                        </div>

                        {{-- Flash Messages --}}
                        @if(Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if($errors->any())
                            @foreach($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                    <strong>Error:</strong> {!! $error !!}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endforeach
                        @endif

                        {{-- Create Form --}}
                        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body row">

                                {{-- Name --}}
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name') }}" placeholder="Enter name" required>
                                </div>

                                {{-- Email --}}
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email') }}" placeholder="Enter email" required>
                                </div>

                                {{-- Password --}}
                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter password" required>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-3 col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" placeholder="Confirm password" required>
                                </div>

                                {{-- Mobile --}}
                                <div class="mb-3 col-md-6">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                           value="{{ old('mobile') }}" placeholder="Enter mobile number">
                                </div>

                                {{-- Image Upload --}}
                                <div class="mb-3 col-md-6">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>

                                {{-- Roles --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Assign Roles</label>
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="form-check col-md-4">
                                                <input type="checkbox" name="roles[{{ $role->name }}]" value="{{ $role->name }}"
                                                       class="form-check-input"
                                                       >
                                                <label class="form-check-label">{{ $role->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div> {{-- End card-body --}}

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Create Admin</button>
                            </div>
                        </form>

                    </div> {{-- End Card --}}
                </div>
            </div>
        </div>
    </div>

</main>

@endsection
