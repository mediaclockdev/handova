<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>

</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Appliance ID</label>
        <input type="text" name="appliance_id" class="form-control @error('appliance_id') is-invalid @enderror"
            value="{{ old('appliance_id', $nextApplianceId) }}" readonly>
        @error('appliance_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Appliance Name <span style="color:red;">*</span></label>
        <input type="text" name="appliance_name" class="form-control @error('appliance_name') is-invalid @enderror"
            value="{{ old('appliance_name', $appliances->appliance_name ?? '') }}">
        @error('appliance_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Product Details</label>
        <input type="text" name="product_details" class="form-control @error('product_details') is-invalid @enderror"
            value="{{ old('product_details', $appliances->product_details ?? '') }}">
        @error('product_details')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">
            Category <span class="text-danger">*</span>
        </label>

        <select name="category" class="form-select @error('category') is-invalid @enderror">
            <option value="">Select Category</option>

            <option value="Hydrological"
                {{ old('category', $appliances->category ?? '') == 'Hydrological' ? 'selected' : '' }}>
                Hydrological
            </option>

            <option value="Electrical"
                {{ old('category', $appliances->category ?? '') == 'Electrical' ? 'selected' : '' }}>
                Electrical
            </option>

            <option value="Mechanical"
                {{ old('category', $appliances->category ?? '') == 'Mechanical' ? 'selected' : '' }}>
                Mechanical
            </option>
        </select>

        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Place of Location <span class="text-danger">*</span>
        </label>

        <select name="place_of_location" class="form-select @error('place_of_location') is-invalid @enderror">
            <option value="">Select Location</option>

            <option value="Bedroom"
                {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Bedroom' ? 'selected' : '' }}>
                Bedroom</option>
            <option value="Master Bedroom" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Master Bedroom' ? 'selected' : '' }}>Master
                Bedroom
            </option>
            <option value="Living Room" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Living Room' ? 'selected' : '' }}>Living Room
            </option>
            <option value="Kitchen" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
            <option value="Dining Room" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Dining Room' ? 'selected' : '' }}>Dining Room
            </option>
            <option value="Bathroom" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Bathroom' ? 'selected' : '' }}>Bathroom</option>
            <option value="Ensuite" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Ensuite' ? 'selected' : '' }}>Ensuite</option>
            <option value="Laundry" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Laundry' ? 'selected' : '' }}>Laundry</option>
            <option value="Garage" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Garage' ? 'selected' : '' }}>Garage</option>
            <option value="Carport" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Carport' ? 'selected' : '' }}>Carport</option>
            <option value="Study Room" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Study Room' ? 'selected' : '' }}>Study Room
            </option>
            <option value="Home Office" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Home Office' ? 'selected' : '' }}>Home Office
            </option>
            <option value="Balcony" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Balcony' ? 'selected' : '' }}>Balcony</option>
            <option value="Patio" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Patio' ? 'selected' : '' }}>Patio</option>
            <option value="Backyard" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Backyard' ? 'selected' : '' }}>Backyard</option>
            <option value="Front Yard" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Front Yard' ? 'selected' : '' }}>Front Yard
            </option>
            <option value="Storage Room" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Storage Room' ? 'selected' : '' }}>Storage
                Room</option>
            <option value="Hallway" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Hallway' ? 'selected' : '' }}>Hallway</option>
            <option value="Staircase" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Staircase' ? 'selected' : '' }}>Staircase
            </option>
            <option value="Roof Space" {{ old('place_of_location', $appliances->place_of_location ?? '') == 'Roof Space' ? 'selected' : '' }}>Roof Space
            </option>

        </select>

        @error('place_of_location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Brand Name <span style="color:red;">*</span></label>
        <input type="text" name="brand_name" class="form-control @error('brand_name') is-invalid @enderror"
            value="{{ old('brand_name', $appliances->brand_name ?? '') }}">
        @error('brand_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
            value="{{ old('model', $appliances->model ?? '') }}">
        @error('model')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Warranty Information</label>
        <input type="text" name="warranty_information"
            class="form-control @error('warranty_information') is-invalid @enderror"
            value="{{ old('warranty_information', $appliances->warranty_information ?? '') }}">
        @error('warranty_information')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Manuals</label>
        <input type="file" id="fileUpload2" name="manuals[]"
            class="form-control @error('manuals') is-invalid @enderror"
            onchange="previewFiles(event, 'manuals-preview')">
        @error('manuals')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="mt-2 d-flex flex-wrap" id="manuals-preview">
            @if (!empty($appliances->manuals))
                @php
                    $manuals = is_array($appliances->manuals)
                        ? $appliances->manuals
                        : json_decode($appliances->manuals, true);
                @endphp

                @if (!empty($manuals) && is_array($manuals))
                    @foreach ($manuals as $a1)
                        @php
                            $extension = strtolower(pathinfo($a1, PATHINFO_EXTENSION));
                        @endphp
                        <div class="position-relative m-2 text-center">
                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ asset('storage/' . $a1) }}" height="80" class="me-2 border rounded">
                            @elseif ($extension === 'pdf')
                                <a href="{{ asset('storage/' . $a1) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 40px; color: red;"></i>
                                    </div>
                                </a>
                            @elseif (in_array($extension, ['csv', 'xls', 'xlsx']))
                                <a href="{{ asset('storage/' . $a1) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark-spreadsheet"
                                            style="font-size: 40px; color: green;"></i>
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $a1) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark" style="font-size: 40px; color: gray;"></i>
                                    </div>
                                </a>
                            @endif
                            <input type="hidden" name="existing_manuals[]" value="{{ $a1 }}">
                            {{-- <p class="small text-center text-muted mt-1">{{ basename($a1) }}</p> --}}
                            <span class="close-btn" onclick="removeExistingImage(this)">×</span>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>

</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Appliances Images</label>
        <input type="file" name="appliances_images[]"
            class="form-control @error('appliances_images') is-invalid @enderror" multiple
            onchange="previewFiles(event, 'appliances-preview')">
        @error('appliances_images')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="mt-2 d-flex flex-wrap" id="appliances-preview">
            @if (!empty($appliances->appliances_images))
                @php
                    $images = is_array($appliances->appliances_images)
                        ? $appliances->appliances_images
                        : json_decode($appliances->appliances_images, true);
                @endphp

                @if (!empty($images) && is_array($images))
                    @foreach ($images as $img)
                        @php
                            $extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                        @endphp
                        <div class="position-relative m-2 text-center">
                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ asset('storage/' . $img) }}" height="80"
                                    class="me-2 border rounded">
                            @elseif ($extension === 'pdf')
                                <a href="{{ asset($img) }}" target="_blank" class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 40px; color: red;"></i>
                                    </div>
                                </a>
                            @elseif (in_array($extension, ['csv', 'xls', 'xlsx']))
                                <a href="{{ asset('storage/' . $img) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark-spreadsheet"
                                            style="font-size: 40px; color: green;"></i>
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $img) }}" target="_blank"
                                    class="text-decoration-none">
                                    <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                                        <i class="bi bi-file-earmark" style="font-size: 40px; color: gray;"></i>
                                    </div>
                                </a>
                            @endif
                            <input type="hidden" name="existing_appliances_images[]" value="{{ $img }}">
                            {{-- <p class="small text-center text-muted mt-1">{{ basename($img) }}</p> --}}
                            <span class="close-btn" onclick="removeExistingImage(this)">×</span>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>

</div>

<script>
    function previewImages(event, previewId) {
        const preview = document.getElementById(previewId);
        const files = event.target.files;

        for (let file of files) {
            const div = document.createElement('div');
            div.classList.add('position-relative', 'm-2');

            const fileType = file.type;
            const fileName = file.name;
            const reader = new FileReader();

            // Handle image preview
            if (fileType.startsWith("image/")) {
                reader.onload = e => {
                    div.innerHTML = `
                    <img src="${e.target.result}" height="80" class="me-2 border rounded">
                    <span class="close-btn" onclick="removeImage(this)">×</span>
                   
                `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }

            // Handle PDF preview
            else if (fileType === "application/pdf") {
                const pdfIcon = `
                <div class="border rounded p-2 text-center bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 40px; color: red;"></i>
                </div>
                
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
                div.innerHTML = pdfIcon;
                preview.appendChild(div);
            }

            // Handle CSV preview
            else if (fileType === "text/csv" || fileName.endsWith(".csv")) {
                const csvIcon = `
                <div class="border rounded p-2 text-center bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 40px; color: green;"></i>
                </div>
              
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
                div.innerHTML = csvIcon;
                preview.appendChild(div);
            }

            // Handle other unsupported types
            else {
                const fileIcon = `
                <div class="border rounded p-2 text-center bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark" style="font-size: 40px; color: gray;"></i>
                </div>
               
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
                div.innerHTML = fileIcon;
                preview.appendChild(div);
            }
        }
    }

    function removeExistingImage(el) {
        const hiddenInput = el.parentElement.querySelector('input[type=hidden]');
        if (hiddenInput) hiddenInput.remove();
        el.parentElement.remove();
    }

    function removeImage(el) {
        el.parentElement.remove();
    }
</script>
