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
    {{-- <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $serviceProvider->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> --}}

    <div class="col-md-6 phone_number_div">
        <div>
            <label>Phone Number <span style="color:red;">*</span></label>
        </div>

        <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $nationalNumber) }}">

        <input type="hidden" name="country_codes" id="country_codes" value="{{ old('country_codes', $countryCode) }}">
        <input type="hidden" id="country_isos" value="{{ $countryIso }}">

        @error('phone')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

    </div>


    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <label for="address" class="form-label">
                Address <span style="color:red;">*</span>
            </label>

            <input value="{{ old('address', $serviceProvider->address ?? '') }}" type="text"
                class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                placeholder="Start typing address..." autocomplete="off" />
            <input type="hidden" name="latitude" id="latitude" value="">
            <input type="hidden" name="longitude" id="longitude" value="">
            @error('address')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <h4 class="h4 mb-0">Service details</h4>
    <div class="col-md-6">
        <label class="form-label">Service Specialisation</label>
        <select name="service_specialisation" class="form-select">
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

    <div class="col-md-6">
        <label class="form-label">Coverage (KM)</label>
        <input type="number" name="coverage" class="form-control @error('coverage') is-invalid @enderror"
            value="{{ old('coverage', $serviceProvider->coverage ?? '') }}">
        @error('coverage')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
