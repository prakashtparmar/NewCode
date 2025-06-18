@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <!-- Page Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Customers</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Customer Management</a></li>
                        <li class="breadcrumb-item active">Customer List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Customer Details</h3>
                            <a href="{{ url('admin/customers/create') }}" class="btn btn-primary float-end" style="max-width: 180px;">
                                Add New Customer
                            </a>
                        </div>

                        <div class="card-body">
                            {{-- Alerts --}}
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Customer Table --}}
                            <div class="table-responsive">
                                <table id="customers" class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Executive</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->address }}</td>
                                                <td>{{ $customer->executive->name ?? 'N/A' }}</td>
                                                <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="javascript:void(0);" class="dropdown-item text-info"
                                                                    onclick="showCustomerDetails({{ $customer->id }})">
                                                                    Show Details
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('admin/customers/' . $customer->id . '/edit') }}">
                                                                    Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form action="{{ url('admin/customers/' . $customer->id) }}" method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No customers found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-hover table-bordered table-striped">
                    <tbody id="customerDetailsTable">
                        <!-- Dynamic content via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
