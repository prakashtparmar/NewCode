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
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">

                        <div class="card-header">
                            <div class="card-title">Edit User</div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <strong>Success:</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show m-3">
                                <strong>Error!</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body row">

                                {{-- Core Fields --}}
                                <div class="mb-3 col-md-4">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required />
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required />
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $user->mobile) }}" />
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="password" class="form-label">Password <small>(leave blank to keep current)</small></label>
                                    <input type="password" class="form-control" name="password" />
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" />
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" name="image" />
                                    @if($user->image)
                                        <img src="{{ asset('storage/'.$user->image) }}" alt="User Image" width="80" class="mt-2" />
                                    @endif
                                </div>

                                {{-- Additional Fields --}}
                                @php
                                    $fields = [
                                        ['user_code', 'User Code'], ['designation', 'Designation'], ['reporting_to', 'Reporting To'],
                                        ['headquarter', 'Headquarter'], ['user_type', 'User Type'],
                                        ['date_of_birth', 'Date of Birth', 'date'], ['joining_date', 'Joining Date', 'date'],
                                        ['emergency_contact_no', 'Emergency Contact No'],
                                        ['gender', 'Gender', 'select', ['Male', 'Female', 'Other']],
                                        ['marital_status', 'Marital Status', 'select', ['Single', 'Married']],
                                        ['address', 'Address', 'textarea'],
                                        ['district', 'District'], ['state', 'State'], ['tehsil', 'Tehsil'],
                                        ['city', 'City'], ['pincode', 'Pincode'], ['postal_address', 'Postal Address'],
                                        ['latitude', 'Latitude'], ['longitude', 'Longitude'], ['depo', 'Depo'],
                                    ];
                                @endphp

                                @foreach($fields as $field)
                                    @php
                                        $name = $field[0];
                                        $label = $field[1];
                                        $type = $field[2] ?? 'text';
                                        $options = $field[3] ?? [];
                                    @endphp
                                    <div class="mb-3 col-md-4">
                                        <label for="{{ $name }}" class="form-label">{{ $label }}</label>

                                        @if($type === 'select')
                                            <select name="{{ $name }}" class="form-select">
                                                <option value="">Select</option>
                                                @foreach($options as $option)
                                                    <option value="{{ $option }}" {{ old($name, $user->$name) === $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @elseif($type === 'textarea')
                                            <textarea name="{{ $name }}" class="form-control" rows="2">{{ old($name, $user->$name) }}</textarea>
                                        @else
                                            <input type="{{ $type }}" class="form-control" name="{{ $name }}" value="{{ old($name, $user->$name) }}" />
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Booleans --}}
                                @php
                                    $booleans = [
                                        'is_self_sale' => 'Is Self Sale',
                                        'is_multi_day_start_end_allowed' => 'Multi-Day Start/End Allowed',
                                        'is_allow_tracking' => 'Allow Tracking'
                                    ];
                                @endphp

                                @foreach($booleans as $field => $label)
                                    <div class="mb-3 col-md-4">
                                        <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                        <select name="{{ $field }}" class="form-select">
                                            <option value="0" {{ old($field, $user->$field) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old($field, $user->$field) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                @endforeach

                                {{-- Roles --}}
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Assign Roles</label>
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="form-check col-md-3">
                                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                    class="form-check-input"
                                                    {{ in_array($role->name, old('roles', $user->getRoleNames()->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $role->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
