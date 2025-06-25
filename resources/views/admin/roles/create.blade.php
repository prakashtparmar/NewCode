@extends('admin.layout.layout')

@section('content')
    <main class="app-main">
        <!-- Header -->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Create Role</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Form -->
        <div class="app-content">
            <div class="container-fluid">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Role Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mt-3">Assign Permissions</h5>
                            <hr>
                        </div>

                        <!-- User Permissions -->
                        <div class="col-md-3">
                            <h6>User Related</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'user')) as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Role Permissions -->
                        <div class="col-md-3">
                            <h6>Role Related</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'role')) as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Customer Permissions -->
                        <div class="col-md-3">
                            <h6>Customer Related</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'customer')) as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Company Permissions -->
                        <div class="col-md-3">
                            <h6>Company Related</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'company')) as $permission)
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="form-check-input" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Other Permissions -->
                        <div class="col-md-12 mt-4">
                            <h6>Other Permissions</h6>
                            <div class="row">
                                @foreach ($permissions->filter(fn($p) =>
                                    !str_contains($p->name, 'user') &&
                                    !str_contains($p->name, 'role') &&
                                    !str_contains($p->name, 'customer') &&
                                    !str_contains($p->name, 'company')) as $permission)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="form-check-input" id="perm_{{ $permission->id }}">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Role</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
