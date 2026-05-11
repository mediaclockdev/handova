<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label for="propertyTitle" class="form-label">Property Title <span style="color:red;">*</span></label>
        <input type="text" class="form-control @error('property_title') is-invalid @enderror" id="property_title" name="property_title" placeholder="Write here" value="{{ old('property_title', $property->property_title ?? '') }}" />
        @error('property_title')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="propertyType" class="form-label">Property Type <span style="color:red;">*</span></label>
        <select class="form-select @error('property_type') is-invalid @enderror" id="property_type" name="property_type">
            <option value="">Select Property Type</option>
            <option value="Industrial" {{ old('property_type', $property->property_type ?? '') == 'Industrial' ? 'selected' : '' }}>Industrial</option>
            <option value="Retail" {{ old('property_type', $property->property_type ?? '') == 'Retail' ? 'selected' : '' }}>Retail</option>
        </select>
        @error('property_type')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label for="address" class="form-label">Address <span style="color:red;">*</span></label>
        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Start typing address..." autocomplete="off" value="{{ old('address', $property->address ?? '') }}" />
        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude ?? '') }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude ?? '') }}">
        @error('address')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">House Plan <span class="text-danger">*</span></label>
        <select name="house_plan_id" class="form-select @error('house_plan_id') is-invalid @enderror">
            <option value="">Select House Plan</option>
            @foreach ($housePlans as $plan)
                <option value="{{ $plan->id }}" {{ old('house_plan_id', $property->house_plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                    {{ $plan->plan_name }}
                </option>
            @endforeach
        </select>
        @error('house_plan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="mb-2 mt-2">
            <button type="button" class="btn btn-add-property" data-bs-toggle="modal" data-bs-target="#addHousePlanModal">
                Add
            </button>
        </div>
    </div>
    <div class="col-md-6">
        <label for="buildCompletionDate" class="form-label">Build Completion Date</label>
        <input type="date" class="form-control @error('build_completion_date') is-invalid @enderror" id="build_completion_date" name="build_completion_date" value="{{ old('build_completion_date', $property->build_completion_date ?? '') }}" />
        @error('build_completion_date')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <label for="assignedBuilder" class="form-label">Assigned Builder / Site Manager</label>
        <select class="form-select @error('assigned_builder_site_manager') is-invalid @enderror" id="assigned_builder_site_manager" name="assigned_builder_site_manager">
            <option value="">Select Assigned builder</option>
            <option value="1" {{ old('assigned_builder_site_manager', $property->assigned_builder_site_manager ?? '') == '1' ? 'selected' : '' }}>Builder A</option>
            <option value="2" {{ old('assigned_builder_site_manager', $property->assigned_builder_site_manager ?? '') == '2' ? 'selected' : '' }}>Builder B</option>
            <option value="3" {{ old('assigned_builder_site_manager', $property->assigned_builder_site_manager ?? '') == '3' ? 'selected' : '' }}>Builder C</option>
        </select>
        @error('assigned_builder_site_manager')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label for="numBedrooms" class="form-label">Number of Bedrooms</label>
        <input type="number" class="form-control @error('number_of_bedrooms') is-invalid @enderror" id="number_of_bedrooms" name="number_of_bedrooms" placeholder="Add No.of Bedrooms" step="1" min="0" value="{{ old('number_of_bedrooms', $property->number_of_bedrooms ?? '') }}" />
        @error('number_of_bedrooms')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="numBathrooms" class="form-label">Number of Bathrooms</label>
        <input type="number" class="form-control @error('number_of_bathrooms') is-invalid @enderror" name="number_of_bathrooms" id="numBathrooms" placeholder="Add No.of Bathroom" step="1" min="0" value="{{ old('number_of_bathrooms', $property->number_of_bathrooms ?? '') }}" />
        @error('number_of_bathrooms')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label for="parking" class="form-label">Parking</label>
        <select id="parking" name="parking" class="form-control @error('parking') is-invalid @enderror">
            <option value="">Select parking</option>
            @for ($i = 0; $i <= 9; $i++)
                <option value="{{ $i }}" {{ old('parking', $property->parking ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        @error('parking')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="swimmingPool" class="form-label">Swimming Pool </label>
        <select class="form-select" id="swimming_pool" name="swimming_pool">
            <option value="">Select here</option>
            <option value="1" {{ old('swimming_pool', $property->swimming_pool ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('swimming_pool', $property->swimming_pool ?? '') == 0 ? 'selected' : '' }}>No</option>
        </select>
        @error('swimming_pool')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label for="floorPlanUpload" class="form-label">Floor Plan Upload</label>
        <div class="input-group">
            <input type="text" class="form-control" id="floorPlanUpload" placeholder="Upload here" readonly />
            <label class="input-group-text" for="fileUpload" style="cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload upload-icon" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V10.5a.5.5 0 0 1-1 0V2.707L4.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                </svg>
                <input type="file" class="@error('floor_plan_upload') is-invalid @enderror" name="floor_plan_upload[]" id="fileUpload" multiple accept="image/*" hidden />
                @error('floor_plan_upload')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </label>
        </div>
        <div id="preview-container" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
            @if(isset($property) && !empty($property->floor_plan_upload))
                @php
                    $floorPlans = is_string($property->floor_plan_upload) ? json_decode($property->floor_plan_upload, true) : $property->floor_plan_upload;
                @endphp
                @if (!empty($floorPlans))
                    @foreach ($floorPlans as $index => $image)
                        <div class="mb-2 preview-image existing-image" data-index="{{ $index }}">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image) }}" alt="Floor Plan" class="img-fluid img-thumbnail m-2" style="height: 80px;">
                                <button type="button" class="remove-image-btn close-btn" title="Remove" data-index="{{ $index }}">&times;</button>
                                <input type="hidden" name="existing_floor_plans[]" value="{{ $image }}">
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <label for="propertyStatus" class="form-label">Property Status <span style="color:red;">*</span></label>
        <select class="form-select @error('property_status') is-invalid @enderror" id="property_status" name="property_status">
            <option value="">Select</option>
            <option value="available" {{ old('property_status', $property->property_status ?? '') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="pending" {{ old('property_status', $property->property_status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="sold" {{ old('property_status', $property->property_status ?? '') == 'sold' ? 'selected' : '' }}>Sold</option>
        </select>
        @error('property_status')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <label for="appliance_id" class="form-label">Appliances</label>
        <select class="form-select select2 appliance-select @error('appliance_id') is-invalid @enderror" id="appliance_id" name="appliance_id[]" multiple>
            @foreach ($appliances as $appliance)
                <option value="{{ $appliance->id }}" {{ collect(old('appliance_id', isset($property) ? $property->appliance_id : []))->contains($appliance->id) ? 'selected' : '' }}>
                    {{ $appliance->brand_name }} - {{ $appliance->model }}
                </option>
            @endforeach
        </select>
        @error('appliance_id')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="mb-2 mt-2">
            <button type="button" class="btn btn-add-property" data-bs-toggle="modal" data-bs-target="#addApplianceModal">
                Add
            </button>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label for="tagsLabels" class="form-label">Tags / Labels</label>
        <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags">
            <option value="">Select Tags</option>
            <option value="new" {{ old('tags', $property->tags ?? '') == 'new' ? 'selected' : '' }}>New</option>
            <option value="featured" {{ old('tags', $property->tags ?? '') == 'featured' ? 'selected' : '' }}>Featured</option>
            <option value="luxury" {{ old('tags', $property->tags ?? '') == 'luxury' ? 'selected' : '' }}>Luxury</option>
        </select>
        @error('tags')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="internalNotes" class="form-label">Internal Notes</label>
        <input type="text" class="form-control @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" placeholder="Write here" value="{{ old('internal_notes', $property->internal_notes ?? '') }}" />
        @error('internal_notes')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-12">
        <label for="complianceCertificate" class="form-label">Compliance certificate <span style="color:red;">*</span></label>
        <select class="form-select @error('compliance_certificate') is-invalid @enderror" id="compliance_certificate" name="compliance_certificate">
            <option value="">Select here</option>
            <option value="issued" {{ old('compliance_certificate', $property->compliance_certificate ?? '') == 'issued' ? 'selected' : '' }}>Issued</option>
            <option value="pending" {{ old('compliance_certificate', $property->compliance_certificate ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>
        @error('compliance_certificate')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
