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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create New Role</li>
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
                            <div class="card-title mb-0">Create New Role</div>
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

                        {{-- Create Role Form --}}
                        
                        <form method="POST" action="{{ route('roles.store') }}">
                            @csrf
                            <div class="card-body row">

                                {{-- Role Name --}}
                                <div class="mb-3 col-md-12">
                                    <label for="name" class="form-label">Role Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter role name" required>
                                    @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                                </div>

                                {{-- Permissions --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Assign Permissions</label>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="form-check col-md-4">
                                                <input type="checkbox" name="permissions[{{ $permission->name }}]" value="{{ $permission->name }}"
                                                       class="form-check-input"
                                                       >
                                                <label class="form-check-label">{{ $permission->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div> {{-- End card-body --}}

                            <div class="card-footer text-end">
                                <a href="{{ route('roles.index') }}" class="btn btn-primary">Back role List</a>
                                <button type="submit" class="btn btn-primary">Create Role</button>
                            </div>
                        </form>

                    </div> {{-- End Card --}}
                </div>
            </div>
        </div>
    </div>

</main>

@endsection
