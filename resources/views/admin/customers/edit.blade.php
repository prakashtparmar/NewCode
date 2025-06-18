@extends('admin.layout.layout')

@section('content')

<main class="app-main">

    {{-- Page Header --}}
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Customer Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</li>
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
                            <div class="card-title">{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</div>
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

                        {{-- Add/Edit Form --}}
                        <form method="POST" action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}">
                            @csrf
                            @if(isset($customer))
                                @method('PUT')
                            @endif

                            <div class="card-body row">

                                {{-- Name --}}
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $customer->name ?? '') }}" required />
                                </div>

                                {{-- Email --}}
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email', $customer->email ?? '') }}" required />
                                </div>

                                {{-- Phone --}}
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ old('phone', $customer->phone ?? '') }}" required />
                                </div>

                                {{-- Address --}}
                                <div class="mb-3 col-md-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $customer->address ?? '') }}</textarea>
                                </div>

                            </div>

                            {{-- Submit --}}
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($customer) ? 'Update Customer' : 'Add Customer' }}
                                </button>
                            </div>
                        </form>

                    </div> {{-- End Card --}}
                </div>
            </div>
        </div>
    </div>

</main>

@endsection
