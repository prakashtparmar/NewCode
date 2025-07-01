@extends('admin.layout.layout')

@section('content')
<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3>Travel Modes</h3></div>
                <div class="col-sm-6 text-end">
                    <a href="{{ route('travelmode.create') }}" class="btn btn-primary">Add Travel Mode</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Travel Mode List</h5>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                @if(auth()->user()->user_level === 'master_admin')
                                    <th>Company</th>
                                @endif
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($travelModes as $key => $mode)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $mode->name }}</td>
                                    @if(auth()->user()->user_level === 'master_admin')
                                        <td>{{ $mode->company->name ?? '-' }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('travelmode.edit', $mode->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('travelmode.destroy', $mode->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No travel modes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</main>
@endsection
