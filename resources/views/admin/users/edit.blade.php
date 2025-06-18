@extends('admin.layout.layout')

@section('content')

<main class="app-main">

    {{-- Page Header --}}
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">User Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
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
                        <div class="card-header">
                            <div class="card-title">Edit User</div>
                        </div>

                        {{-- Flash Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <strong>Success:</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show m-3">
                                <strong>Error!</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Edit Form --}}
                        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body row">

                                {{-- Name --}}
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $user->name) }}" required />
                                </div>

                                {{-- Email --}}
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email', $user->email) }}" required />
                                </div>

                                {{-- Mobile --}}
                                <div class="mb-3 col-md-6">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                           value="{{ old('mobile', $user->mobile ?? '') }}" />
                                </div>

                                {{-- Password --}}
                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label">Password <small class="text-muted">(Leave blank if not changing)</small></label>
                                    <input type="password" class="form-control" id="password" name="password" />
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-3 col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" />
                                </div>

                                {{-- Image Upload --}}
                                <div class="mb-3 col-md-6">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" />
                                    @if(!empty($user->image))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $user->image) }}" width="80" alt="User Image" />
                                        </div>
                                    @endif
                                </div>

                                {{-- Roles --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Assign Roles</label>
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="form-check col-md-4">
                                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                       class="form-check-input"
                                                       {{ in_array($role->name, old('roles', $user->getRoleNames()->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $role->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                

                            </div>

                            {{-- Submit --}}
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </form>

                    </div> {{-- End Card --}}
                </div>
            </div>
        </div>
    </div>

</main>

@endsection
