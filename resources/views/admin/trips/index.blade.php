@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <!-- Header Section -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Trips</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Trip Management</a></li>
                        <li class="breadcrumb-item active">All Trips</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header">
                    <h5 class="card-title">Trip List</h5>
                    <a href="{{ route('trips.create') }}" class="btn btn-primary float-end">Add Trip</a>
                </div>

                <!-- Card Body -->
                <div class="card-body table-responsive">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <strong>Success:</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <table id="trips-table" class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Distance (km)</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trips as $trip)
                                <tr>
                                    <td>{{ $trip->id }}</td>
                                    <td>{{ $trip->trip_date }}</td>
                                    <td>{{ $trip->travel_mode }}</td>
                                    <td>{{ $trip->total_distance_km }}</td>
                                    <td><span class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($trip->status) }}</span></td>
                                    <td>
                                        <span class="badge 
                                            @if($trip->approval_status == 'approved') bg-success
                                            @elseif($trip->approval_status == 'denied') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($trip->approval_status) ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('trips.show', $trip) }}" class="text-info me-2" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('trips.edit', $trip) }}" class="text-warning me-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this trip?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link p-0 text-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No trips found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
</main>
@endsection
