<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>

</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" class="form-control"
            value="{{ old('company_name', $serviceProvider->company_name ?? '') }}">
    </div>
</div>

<div class="row g-4 mb-4">
    <h4 class="h4 mb-0">Contact Details</h4>
    <div class="col-md-6">
        <label class="form-label">First Name <span style="color:red;">*</span></label>
        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
            value="{{ old('first_name', $serviceProvider->first_name ?? '') }}">
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Last Name <span style="color:red;">*</span></label>
        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
            value="{{ old('last_name', $serviceProvider->last_name ?? '') }}">
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Email Address <span style="color:red;">*</span></label>
        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $serviceProvider->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $serviceProvider->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <h4 class="h4 mb-0">Service details</h4>
    <div class="col-md-6">
        <label class="form-label">Service Specialisation</label>
        <select name="service_specialisation" class="form-control">
            <option value="">Select Specialisation</option>

            @foreach ($specializations as $specialization)
                <option value="{{ $specialization->id }}"
                    {{ old('service_specialisation', $serviceProvider->service_specialisation ?? '') == $specialization->id ? 'selected' : '' }}>
                    {{ $specialization->specialization }}
                </option>
            @endforeach
        </select>

        @error('service_specialisation')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>
