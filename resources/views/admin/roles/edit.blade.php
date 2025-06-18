@extends('admin.layout.layout')

@section('content')

<main class="app-main">

    {{-- Page Header --}}
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Role Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Edit Role</li>
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
                            <div class="card-title">Edit Role</div>
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

                        {{-- Edit Role Form --}}
                        <form method="POST" action="{{ route('roles.update', $role->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="card-body row">

                                {{-- Role Name --}}
                                <div class="mb-3 col-md-12">
                                    <label for="name" class="form-label">Role Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ $role->name }}" required>
                                </div>

                                {{-- Permissions --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Assign Permissions</label>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="form-check col-md-4">
                                                <input type="checkbox" class="form-check-input" name="permissions[{{ $permission->name }}]" value="{{ $permission->name }}" 
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $permission->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            {{-- Submit --}}
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Update Role</button>
                            </div>
                        </form>

                    </div> {{-- End Card --}}
                </div>
            </div>
        </div>
    </div>

</main>

@endsection
