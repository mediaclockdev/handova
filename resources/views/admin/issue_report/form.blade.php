<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Issue Number</label>
        <input type="text" name="issue_number" class="form-control"
            value="{{ old('issue_number', $issueReport->issue_number ?? ($newIssueNumber ?? '')) }}" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label">Select Property <span style="color:red;">*</span></label>
        <select id="property_select" name="properties_id"
            class="form-select  @error('properties_id') is-invalid @enderror">
            <option value="">Select Property</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}"
                    {{ $issueReport->properties_id == $property->id ? 'selected' : '' }}>
                    {{ $property->property_title }}
                </option>
            @endforeach
        </select>
        @error('properties_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Issue Title <span style="color:red;">*</span></label>
        <input type="text" name="issue_title" class="form-control @error('issue_title') is-invalid @enderror"
            value="{{ old('issue_title', $issueReport->issue_title ?? '') }}">
        @error('issue_title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Issue Category</label>
        <input type="text" name="issue_category" class="form-control"
            value="{{ old('issue_category', $issueReport->issue_category ?? '') }}">
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Issue Location <span style="color:red;">*</span></label>
        <input type="text" name="issue_location" class="form-control @error('issue_location') is-invalid @enderror"
            value="{{ old('issue_location', $issueReport->issue_location ?? '') }}">
        @error('issue_location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="col-md-6">
        <label class="form-label">Customer Contact <span style="color:red;">*</span></label>
        <input type="text" name="customer_contact"
            class="form-control @error('customer_contact') is-invalid @enderror"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="customer_contact"
            value="{{ old('customer_contact', $issueReport->customer_contact ?? '') }}">
        @error('customer_contact')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> --}}

    <div class="col-md-6 phone_number_div">
        <div>
            <label class="form-label">Customer Contact <span style="color:red;">*</span></label>
        </div>

        <input type="tel" id="customer_contact" name="customer_contact" class="form-control @error('customer_contact') is-invalid @enderror"
            value="{{ old('customer_contact', $nationalNumber) }}">

        <input type="hidden" name="report_country_code" id="report_country_code"
            value="{{ old('report_country_code', $countryCode) }}">
        <input type="hidden" id="report_country_iso" value="{{ $countryIso }}">

        @error('customer_contact')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12 mt-3">
        <label class="form-label">Upload Images</label>

        <input type="file" name="image[]" id="imageInput" multiple class="form-control"
            onchange="previewIssueImages(event)">


        <!-- 👇 PREVIEW BOX FOR EXISTING + NEW UPLOADED IMAGES -->
        <div id="imagePreviewContainer" class="d-flex flex-wrap gap-3 mt-3" style="gap: 15px;">

            {{-- Existing images (your Blade loop will place them here) --}}
            @if (isset($issueReport) && $issueReport->image)
                @php
                    $images = is_array($issueReport->image)
                        ? $issueReport->image
                        : json_decode($issueReport->image, true);
                @endphp

                @if (!empty($images))
                    @foreach ($images as $img)
                        <div class="position-relative image-wrapper" style="display: inline-block;">
                            <img src="{{ asset('storage/' . $img) }}" width="80" class="img-thumbnail">

                            <button type="button" class="close-btn remove-image-btn" data-image="{{ $img }}"
                                data-type="existing"
                                style="border-radius: 50%;padding: 3px 7px;font-size: 14px;position: absolute;top: 0;right: 0;">
                                ×
                            </button>

                            <input type="hidden" name="existing_images[]" value="{{ $img }}">
                        </div>
                    @endforeach
                @endif

            @endif

        </div>
    </div>

</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <label class="form-label">Issue Details <span style="color:red;">*</span></label>
        <textarea name="issue_details" class="form-control @error('issue_details') is-invalid @enderror" rows="4">{{ old('issue_details', $issueReport->issue_details ?? '') }}</textarea>
        @error('issue_details')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Reported By (House Owner) <span style="color:red;">*</span></label>
        <select name="reported_by" id="reported_by" class="form-control @error('reported_by') is-invalid @enderror">
            <option value="">Select House Owner</option>
            @foreach ($houseOwners as $owner)
                <option value="{{ $owner->id }}" {{ $issueReport->reported_by == $owner->id ? 'selected' : '' }}>
                    {{ $owner->email }}
                </option>
            @endforeach
        </select>
        @error('reported_by')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Reported Date </label>
        <input type="text" name="reported_date" class="form-control"
            value="{{ old('reported_date', $issueReport->reported_date ?? now()->format('Y-m-d')) }}" readonly>
    </div>

    <div class="col-md-12">
        <label class="form-label">Urgency Level</label>
        <select name="issue_urgency_level" id="issue_urgency_level" class="form-select">
            @foreach (['Low', 'Medium', 'High', 'Critical'] as $level)
                <option value="{{ $level }}"
                    {{ ($issueReport->issue_urgency_level ?? '') == $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label">Assigned to service provider</label>
        <select name="assigned_to_service_provider" id="assigned_to_service_provider_status" class="form-select">
            <option value="">Select Options</option>
            <option value="yes"
                {{ ($issueReport->assigned_to_service_provider ?? '') == 'yes' ? 'selected' : '' }}>
                Yes</option>
            <option value="no" {{ ($issueReport->assigned_to_service_provider ?? '') == 'no' ? 'selected' : '' }}>
                No</option>
        </select>
    </div>

    <div class="col-md-12" id="service_provider_wrapper" style="display:none;">
        <label class="form-label">Service Provider</label>
        <select name="service_provider" id="service_provider_select" class="form-select">
            <option value="">Select Service Provider</option>
        </select>
        <input type="hidden" id="selected_service_provider" value="{{ $issueReport->service_provider }}">
    </div>

    <div class="col-md-12">
        <label class="form-label">Issue Status</label>
        <select name="issue_status" id="issue_status" class="form-select">
            @foreach (['open', 'completed', 'pending', 'inprogress', 'close'] as $st)
                <option value="{{ $st }}"
                    {{ ($issueReport->issue_status ?? '') == $st ? 'selected' : '' }}>
                    {{ ucfirst($st) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-12" id="service_provider_status_wrapper" style="display:none;">
        <label class="form-label">Service Provider Status</label>
        <select name="status" id="status" class="form-select">
            @foreach (['pending', 'accepted', 'declined'] as $st)
                <option value="{{ $st }}" {{ ($issueReport->status ?? '') == $st ? 'selected' : '' }}>
                    {{ ucfirst($st) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
    // NEW IMAGE PREVIEW (same as house owner)
    function previewIssueImages(event) {
        let preview = document.getElementById("imagePreviewContainer");
        const files = event.target.files;

        for (let file of files) {
            let fileName = file.name;
            let fileExt = fileName.split('.').pop().toLowerCase();

            let isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
            let isPDF = fileExt === 'pdf';
            let isDoc = ['doc', 'docx'].includes(fileExt);

            let div = document.createElement("div");
            div.classList.add("position-relative", "m-2", "text-center");
            div.style.width = "100px";

            if (isImage) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    div.innerHTML = `
                        <img src="${e.target.result}" height="80" class="img-thumbnail">
                        <button type="button" class="close-btn" onclick="removeIssueNewImage(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            } else if (isPDF) {
                div.innerHTML = `
                    <i class="fa fa-file-pdf-o fa-3x text-danger"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeIssueNewImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            } else if (isDoc) {
                div.innerHTML = `
                    <i class="fa fa-file-word-o fa-3x text-primary"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeIssueNewImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            } else {
                div.innerHTML = `
                    <i class="fa fa-file fa-3x text-secondary"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeIssueNewImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }

            preview.appendChild(div);
        }
    }

    // REMOVE NEW UPLOADED IMAGE (before submit)
    function removeIssueNewImage(el) {
        el.parentElement.remove();
    }

    // REMOVE EXISTING IMAGE FROM DB
    document.addEventListener("click", function(e) {
        if (e.target.closest(".remove-image-btn")) {
            let btn = e.target.closest(".remove-image-btn");

            // Remove hidden input so Laravel won't keep it
            let hiddenInput = btn.parentElement.querySelector("input[type=hidden]");
            if (hiddenInput) hiddenInput.remove();

            btn.parentElement.remove();
        }
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assignedSelect = document.getElementById('assigned_to_service_provider_status');
        const providerWrapper = document.getElementById('service_provider_wrapper');
        const providerStatusWrapper = document.getElementById('service_provider_status_wrapper');


        function toggleProviderWrapper() {
            if (assignedSelect.value === 'yes') {
                providerWrapper.style.display = 'block';
                providerStatusWrapper.style.display = 'block';
            } else {
                providerWrapper.style.display = 'none';
                providerStatusWrapper.style.display = 'none';
            }
        }

        toggleProviderWrapper();
        assignedSelect.addEventListener('change', toggleProviderWrapper);
    });


    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".remove-image-btn").forEach(btn => {
            btn.addEventListener("click", function() {

                let wrapper = this.closest(".image-wrapper");
                let imgPath = this.getAttribute("data-image");

                wrapper.remove();

                // Remove hidden existing_images input
                document.querySelectorAll('input[name="existing_images[]"]').forEach(input => {
                    if (input.value === imgPath) {
                        input.remove();
                    }
                });

                // Add to remove_images[]
                let rm = document.createElement("input");
                rm.type = "hidden";
                rm.name = "remove_images[]";
                rm.value = imgPath;
                document.getElementById("issueReportForm").appendChild(rm);
            });
        });
    });
</script>
