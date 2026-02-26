@extends('layouts.master')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-credit-card me-2"></i>Payment Methods
        </h1>
        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-primary rounded-pill">
            <i class="fas fa-eye me-2"></i>View Payment Methods
        </a>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-plus-circle me-2"></i>Add Payment Method
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="name">Name</label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           placeholder="Payment Method Name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           required>

                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Code Input -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="code">Code</label>
                                    <input type="text"
                                           name="code"
                                           id="code"
                                           placeholder="Payment Method Code"
                                           class="form-control @error('code') is-invalid @enderror"
                                           value="{{ old('code') }}"
                                           required>

                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Price Input -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="additional_price">
                                        <i class="fas fa-dollar-sign text-success me-2"></i>Additional Price
                                    </label>
                                    <input type="number"
                                           name="additional_price"
                                           id="additional_price"
                                           placeholder="0.00"
                                           class="form-control @error('additional_price') is-invalid @enderror"
                                           value="{{ old('additional_price', '0') }}"
                                           min="0"
                                           step="0.01"
                                           max="999999.99">

                                    @error('additional_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted d-flex align-items-center mt-2">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    Extra fee charged for using this payment method (optional)
                                </small>
                            </div>

                            <!-- Logo Upload -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label d-flex align-items-center">
                                        <i class="fas fa-image text-primary me-2"></i>Logo
                                    </label>
                                    <div class="input-group">
                                        <input type="file"
                                               class="form-control @error('logo') is-invalid @enderror"
                                               id="logo"
                                               name="logo"
                                               accept="image/*">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted d-flex align-items-center mt-2">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                                    </small>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="col-md-12 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        <i class="fas fa-power-off text-primary me-2"></i>Active Status
                                    </label>
                                </div>
                            </div>

                            <!-- Preview Image Container -->
                            <div class="col-12">
                                <div id="imagePreview" class="text-center" style="display: none;">
                                    <img src="" alt="Logo Preview" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-plus me-2"></i>Add Payment Method
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update file input label with selected filename
    document.querySelector('.form-control[type="file"]').addEventListener('change', function() {
        // Preview image
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').style.display = 'block';
                document.querySelector('#imagePreview img').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endsection
