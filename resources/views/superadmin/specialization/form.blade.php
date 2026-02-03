<div class="mb-4">
    <div class="row g-3">
        {{-- Service Specialization --}}
        <div class="col-md-6">
            <label class="form-label">
                Service Specialization <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="specialization"
                   value="{{ old('specialization', $specialization->specialization ?? '') }}"
                   class="form-control"
                   placeholder="Service Specialization"
                   required>

            @error('specialization')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Status --}}
        <div class="col-md-6">
            <label class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="active"
                    {{ old('status', $specialization->status ?? 'active') == 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive"
                    {{ old('status', $specialization->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>

            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 border-top pt-3">
    <a href="{{ route('superadmin.specialization.index') }}" class="btn btn-light">
        Cancel
    </a>

    <button type="submit" class="btn btn-dark px-4">
        {{ $buttonText }}
    </button>
</div>
