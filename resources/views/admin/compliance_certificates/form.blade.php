<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle ?? 'Compliance Certificate' }}</h1>
</div>

<div class="row g-4 mb-4">
    <div class="mb-3 col-12">
        <label for="property_id" class="form-label">Select Property</label>
        {{-- <div class="d-flex gap-2"> --}}
        <select name="property_id" id="property_id" class="form-control" required>
            <option value="">Select Property</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}"
                    {{ old('property_id', $certificate->property_id ?? '') == $property->id ? 'selected' : '' }}>
                    {{ $property->property_title }}
                </option>
            @endforeach
        </select>
        <div class="mb-2 mt-2">
            <a href="{{ route('admin.properties.create') }}" class="btn btn-add-property">
                Add
            </a>
        </div>
        {{-- </div> --}}
    </div>

    <div class="col-md-6">
        <label class="form-label">Certification Title</label>
        <input type="text" name="certification_title"
            class="form-control @error('certification_title') is-invalid @enderror"
            value="{{ old('certification_title', $certificate->certification_title ?? '') }}">
        @error('certification_title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Type of Compliance</label>
        <input type="text" name="compliance_type" class="form-control @error('compliance_type') is-invalid @enderror"
            value="{{ old('compliance_type', $certificate->compliance_type ?? '') }}">
        @error('compliance_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Certificate Number/ID</label>
        <input type="text" name="certificate_number"
            class="form-control @error('certificate_number') is-invalid @enderror"
            value="{{ old('certificate_number', $certificate->certificate_number ?? '') }}">
        @error('certificate_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Issuing Authority</label>
        <input type="text" name="issuing_authority"
            class="form-control @error('issuing_authority') is-invalid @enderror"
            value="{{ old('issuing_authority', $certificate->issuing_authority ?? '') }}">
        @error('issuing_authority')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Date of Issue</label>
        <input type="date" name="date_of_issue" class="form-control @error('date_of_issue') is-invalid @enderror"
            value="{{ old('date_of_issue', isset($certificate->date_of_issue) ? $certificate->date_of_issue->format('Y-m-d') : '') }}">
        @error('date_of_issue')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror"
            value="{{ old('expiry_date', isset($certificate->expiry_date) ? $certificate->expiry_date->format('Y-m-d') : '') }}">
        @error('expiry_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Property Area / Scope</label>
        <input type="text" name="property_area" class="form-control @error('property_area') is-invalid @enderror"
            value="{{ old('property_area', $certificate->property_area ?? '') }}">
        @error('property_area')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        {{-- <label class="form-label">Attachments (PDF, CSV, Excel, Image) - multiple allowed</label> --}}

        <div class="mb-3">
            <label class="form-label">Attachments</label>
            <input type="file" id="attachmentsInput" name="attachments[]" class="form-control">
        </div>

        <div id="attachmentsPreview" class="mt-3 d-flex flex-wrap gap-3"></div>

        {{-- Existing Files --}}
        <div class="mt-3 d-flex flex-wrap" id="existingAttachmentsWrapper">
            @php
                $existingAttachments = [];

                if (!empty($certificate) && !empty($certificate->attachments)) {
                    $existingAttachments = is_array($certificate->attachments)
                        ? $certificate->attachments
                        : json_decode($certificate->attachments, true) ?? [];
                }
            @endphp


            @foreach ($existingAttachments as $file)
                @php
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                @endphp

                <div class="m-2 position-relative existing-attachment-box" data-file="{{ $file }}">

                    {{-- REMOVE BUTTON --}}
                    <button type="button" class="btn btn-danger btn-sm remove-existing-btn close-btn">
                        ✕
                    </button>


                    {{-- Image --}}
                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ asset('storage/' . $file) }}" height="100" class="border rounded">
                    @else
                        <div class="attachment-file border rounded p-2"
                            style="width:160px; height:100px; display:flex; align-items:center; justify-content:center;">
                            <div class="text-center">
                                <div class="fw-bold">{{ strtoupper($ext) }}</div>
                                <div class="small text-truncate" style="max-width:130px;">
                                    {{ basename($file) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <input type="hidden" name="existing_attachments[]" value="{{ $file }}">
                </div>
            @endforeach
        </div>

        {{-- Hidden input to track what user removed --}}
        <input type="hidden" name="remove_attachments" id="removeAttachmentsInput">


    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <label class="form-label">Notes / Additional Information</label>
        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $certificate->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


<script>
    // --- PREVIEW NEW FILES (Images, PDFs, Docs, CSV, Excel) ---
    document.getElementById("attachmentsInput").addEventListener("change", function(event) {
        let preview = document.getElementById("attachmentsPreview");
        preview.innerHTML = ""; // clear previous previews

        const files = event.target.files;

        Array.from(files).forEach(file => {
            let fileName = file.name;
            let ext = fileName.split('.').pop().toLowerCase();

            let isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            let isPDF = ext === 'pdf';
            let isExcel = ['xls', 'xlsx'].includes(ext);
            let isCSV = ext === 'csv';
            let isDoc = ['doc', 'docx'].includes(ext);

            let div = document.createElement("div");
            div.classList.add("position-relative", "m-2", "text-center");
            div.style.width = "120px";

            // IMAGE PREVIEW
            if (isImage) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    div.innerHTML = `
                        <img src="${e.target.result}" height="80" class="img-thumbnail">
                        <button type="button" class="close-btn" onclick="removeImage(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            }
            // PDF PREVIEW
            else if (isPDF) {
                div.innerHTML = `
                    <i class="fa fa-file-pdf-o fa-3x text-danger"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }
            // DOC/DOCX PREVIEW
            else if (isDoc) {
                div.innerHTML = `
                    <i class="fa fa-file-word-o fa-3x text-primary"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }
            // CSV / EXCEL PREVIEW
            else if (isExcel || isCSV) {
                div.innerHTML = `
                    <i class="fa fa-file-excel-o fa-3x text-success"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }
            // OTHER FILES
            else {
                div.innerHTML = `
                    <i class="fa fa-file fa-3x text-secondary"></i>
                    <small class="d-block text-truncate">${fileName}</small>
                    <button type="button" class="close-btn" onclick="removeImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }

            preview.appendChild(div);
        });
    });

    // REMOVE NEWLY ADDED FILE PREVIEW
    function removeImage(el) {
        el.parentElement.remove();
    }

    // REMOVE EXISTING FILES
    let removedFiles = [];
    document.querySelectorAll(".remove-existing-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            let box = this.closest(".existing-attachment-box");
            let filePath = box.getAttribute("data-file");

            removedFiles.push(filePath);

            document.getElementById("removeAttachmentsInput").value = JSON.stringify(removedFiles);

            box.remove();
        });
    });
</script>
