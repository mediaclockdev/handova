{{-- Basic Information --}}
<div class="mb-4">
    <h5 class="fw-semibold mb-3">Basic Information</h5>

    <div class="row g-3">
        {{-- Name --}}
        <div class="col-md-6">
            <label class="form-label">
                Name <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control"
                placeholder="Full name" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
            <label class="form-label">
                Email <span class="text-danger">*</span>
            </label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control"
                placeholder="builder@example.com" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Phone --}}
        {{-- <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input
                type="text"
                name="phone"
                value="{{ old('phone', $user->phone ?? '') }}"
                class="form-control"
                placeholder="+61 485 1515 151">
            @error('phone')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div> --}}

        <div class="col-md-6 phone_number_div">
            <div>
                <label class="form-label">Phone Number</label>
            </div>

            <input type="tel" id="phone" name="phone" class="form-control"
                value="{{ old('phone', $nationalNumber) }}">

            <input type="hidden" name="country_codes" id="country_codes"
                value="{{ old('country_codes', $countryCode) }}">
            <input type="hidden" id="country_isos" value="{{ $countryIso }}">

            @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

        </div>

        {{-- Status --}}
        <div class="col-md-6">
            <label class="form-label">
                Status <span class="text-danger">*</span>
            </label>

            <select name="status" class="form-select" required>
                <option value="">Select status</option>
                <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="pending" {{ old('status', $user->status ?? '') === 'pending' ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="blocked" {{ old('status', $user->status ?? '') === 'blocked' ? 'selected' : '' }}>
                    Blocked
                </option>
            </select>

            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

    </div>
</div>

{{-- Security --}}
<div class="mb-4">
    <h5 class="fw-semibold mb-3">Security</h5>

    <div class="row">
        <div class="col-md-6">
            <label class="form-label">
                Password
                @if (!empty($edit))
                    <small class="text-muted">(optional)</small>
                @else
                    <span class="text-danger">*</span>
                @endif
            </label>

            <input type="password" name="password" class="form-control" placeholder="••••••••"
                {{ empty($edit) ? 'required' : '' }}>

            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="d-flex justify-content-end gap-2 border-top pt-3">
    <a href="{{ route('superadmin.builders.index') }}" class="btn btn-light">
        Cancel
    </a>

    <button type="submit" class="btn btn-dark px-4">
        {{ $buttonText }}
    </button>
</div>
