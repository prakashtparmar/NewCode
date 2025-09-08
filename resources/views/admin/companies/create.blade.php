@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <div class="container-fluid py-4">
        <h3>Create New Company</h3>
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Company Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Company Code</label>
                <input type="text" name="code" id="code" class="form-control">
            </div>

            <div class="mb-3">
                <label for="owner_name" class="form-label">Owner Name</label>
                <input type="text" name="owner_name" id="owner_name" class="form-control">
            </div>

            <div class="mb-3">
                <label for="gst_number" class="form-label">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" class="form-control">
            </div>

            <div class="mb-3">
                <label for="contact_no" class="form-label">Contact No</label>
                <input type="text" name="contact_no" id="contact_no" class="form-control">
            </div>

            <div class="mb-3">
                <label for="contact_no2" class="form-label">Contact No 2</label>
                <input type="text" name="contact_no2" id="contact_no2" class="form-control">
            </div>

            <div class="mb-3">
                <label for="telephone_no" class="form-label">Telephone No</label>
                <input type="text" name="telephone_no" id="telephone_no" class="form-control">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Company Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Company Logo (PNG)</label>
                <input type="file" name="logo" id="logo" class="form-control" accept="image/png">
            </div>

            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" name="website" id="website" class="form-control">
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">State Working</label>
                <input type="text" name="state" id="state" class="form-control">
            </div>

            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="form-control">
            </div>

            <div class="mb-3">
                <label for="subscription_type" class="form-label">Subscription Type</label>
                <input type="text" name="subscription_type" id="subscription_type" class="form-control">
            </div>

            <div class="mb-3">
                <label for="tally_configuration" class="form-label">Tally Configuration</label>
                <select name="tally_configuration" id="tally_configuration" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Company Address</label>
                <textarea name="address" id="address" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Company</button>
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</main>
@endsection
