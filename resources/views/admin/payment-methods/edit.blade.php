@extends('layouts.master')

@section('content')
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
                <!-- Card Header -->
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i>Edit Payment Method
                    </h5>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <label for="name">Name</label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $paymentMethod->name) }}"
                                           placeholder="Payment Method Name"
                                           required>

                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Code Input -->
                            <div class="col-md-6">
                                <label for="code">Code</label>
                                <div class="form-floating">
                                    <input type="text"
                                           name="code"
                                           id="code"
                                           class="form-control @error('code') is-invalid @enderror"
                                           value="{{ old('code', $paymentMethod->code) }}"
                                           placeholder="Payment Method Code"
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
                                           value="{{ old('additional_price', $paymentMethod->additional_price ?? '0') }}"
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
                                <label class="form-label">Logo</label>
                                @if($paymentMethod->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $paymentMethod->logo) }}"
                                             alt="Current Logo"
                                             class="img-thumbnail"
                                             style="max-height: 100px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input @error('logo') is-invalid @enderror"
                                           id="logo"
                                           name="logo"
                                           accept="image/*">
                                    <label class="custom-file-label" for="logo">
                                        {{ $paymentMethod->logo ? 'Change logo' : 'Choose logo' }}
                                    </label>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                                </small>
                            </div>

                            <!-- Active Status -->
                            <div class="col-md-12 d-flex align-items-center">
                                <div class="custom-control custom-switch mt-4">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active Status</label>
                                </div>
                            </div>

                            <!-- Preview Image Container -->
                            <div class="col-12">
                                <div id="imagePreview" class="text-center" style="display: none;">
                                    <img src=""
                                         alt="Logo Preview"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-save me-2"></i>Update Payment Method
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
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);

        // Preview image
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').show();
                $('#imagePreview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endsection
