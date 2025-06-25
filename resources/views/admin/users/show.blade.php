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
                        <li class="breadcrumb-item active">Admin Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card card-primary card-outline mb-4">
                        {{-- Card Header --}}
                        <div class="card-header">
                            <h5 class="card-title mb-0">Admin Details</h5>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body">
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#adminDetailsModal">
                                View Admin Details
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

</main>

{{-- Modal --}}
<div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminDetailsModalLabel">Admin Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $user->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td><!-- Admin Type here --></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Image</th>
                            <td>
                                @if($user->image)
                                    <img src="{{ asset('storage/'.$user->image) }}" alt="Admin Image" class="img-thumbnail" style="width: 100px; height: auto;">
                                @else
                                    <span class="text-muted">No image uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $user->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Admin
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
