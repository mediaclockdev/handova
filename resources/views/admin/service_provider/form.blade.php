<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>

</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Company Name <span
                style="color:red;">*</span></label>
        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $serviceProvider->company_name ?? '') }}">
        @error('company_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <h4 class="h4 mb-0">Contact Details</h4>
    <div class="col-md-6">
        <label class="form-label">First Name <span
                style="color:red;">*</span></label>
        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $serviceProvider->first_name ?? '') }}">
        @error('first_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Last Name <span
                style="color:red;">*</span></label>
        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $serviceProvider->last_name ?? '') }}">
        @error('last_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Email Address <span
                style="color:red;">*</span></label>
        <input type="text" name="email_address" class="form-control @error('email_address') is-invalid @enderror" value="{{ old('email_address', $serviceProvider->email_address ?? '') }}">
        @error('first_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $serviceProvider->phone_number ?? '') }}">
        @error('phone_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <h4 class="h4 mb-0">Service details</h4>
    <div class="col-md-6">
        <label class="form-label">Service Specialisation <span
                style="color:red;">*</span></label>
        <input type="text" name="service_specialisation" class="form-control @error('service_specialisation') is-invalid @enderror" value="{{ old('service_specialisation', $serviceProvider->service_specialisation ?? '') }}">
        @error('service_specialisation')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Service Type <span
                style="color:red;">*</span></label>
        <input type="text" name="service_type" class="form-control @error('service_type') is-invalid @enderror" value="{{ old('service_type', $serviceProvider->service_type ?? '') }}">
        @error('service_type')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>