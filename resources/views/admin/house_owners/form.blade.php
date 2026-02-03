              <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                  <h1 class="h3 mb-0">{{ $formTitle }}</h1>
              </div>

              <div class="row g-4 mb-4">
                  <h1></h1>
                  <!-- <div class="col-md-12">
                      <label class="form-label">House Owner ID</label>
                      <input type="text" name="house_owner_id" class="form-control @error('house_owner_id') is-invalid @enderror" value="{{ old('house_owner_id', $owner->house_owner_id ?? '') }}">
                      @error('house_owner_id')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
                  </div> -->

                  <div class="col-md-12">
                      <label class="form-label">House Owner ID</label>
                      <input type="text" name="house_owner_id"
                          class="form-control @error('house_owner_id') is-invalid @enderror"
                          value="{{ old('house_owner_id', $owner->house_owner_id ?? ($houseOwnerId ?? '')) }}" readonly>
                      @error('house_owner_id')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>



                  <div class="mb-3">
                      <label for="properties_id" class="form-label">Select Property <span
                              style="color:red;">*</span></label>
                      {{-- <div class="d-flex gap-2"> --}}
                      <select name="properties_id" id="properties_id"
                          class="form-select @error('properties_id') is-invalid @enderror">
                          <option value="">Select Property</option>
                          @foreach ($properties as $property)
                              <option value="{{ $property->id }}"
                                  {{ isset($owner) && $owner->properties_id == $property->id ? 'selected' : '' }}>
                                  {{ $property->property_title }}
                              </option>
                          @endforeach
                      </select>
                      @error('properties_id')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <div class="mb-2 mt-2">
                          <a href="{{ route('admin.properties.create') }}" class="btn btn-add-property">
                              Add
                          </a>
                      </div>
                      {{-- </div> --}}

                  </div>

              </div>
              <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                  <h6 class="h5 mb-0">House Owner Details</h6>
              </div>
              <div class="row g-4 mb-4">
                  <div class="col-md-6">
                      <label class="form-label">First Name <span style="color:red;">*</span></label>
                      <input type="text" name="first_name"
                          class="form-control @error('first_name') is-invalid @enderror"
                          value="{{ old('first_name', $owner->first_name ?? '') }}">
                      @error('first_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>

                  <div class="col-md-6">
                      <label class="form-label">Last Name <span style="color:red;">*</span></label>
                      <input type="text" name="last_name"
                          class="form-control @error('last_name') is-invalid @enderror"
                          value="{{ old('last_name', $owner->last_name ?? '') }}">
                      @error('last_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
              </div>

              <div class="row g-4 mb-4">
                  <div class="col-md-6">
                      <label class="form-label">Email Address <span style="color:red;">*</span></label>
                      <input type="email" name="email_address"
                          class="form-control @error('email_address') is-invalid @enderror"
                          value="{{ old('email_address', $owner->email_address ?? '') }}">
                      @error('email_address')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>

                  <div class="col-md-6 phone_number_div">
                      <div>
                          <label class="form-label">Phone Number <span style="color:red;">*</span></label>
                      </div>

                      <input type="tel" id="phone_number" name="phone_number"
                          class="form-control @error('phone_number') is-invalid @enderror"
                          value="{{ old('phone_number', $nationalNumber) }}">

                      <input type="hidden" name="country_code" id="country_code"
                          value="{{ old('country_code', $countryCode) }}">
                      <input type="hidden" id="country_iso" value="{{ $countryIso }}">

                      @error('phone_number')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror

                  </div>

              </div>

              <div class="col-md-12 mb-3">
                  <label class="form-label">Address<span style="color:red;">*</span></label>
                  <textarea name="address_of_property" class="form-control @error('address_of_property') is-invalid @enderror">{{ old('address_of_property', $owner->address_of_property ?? '') }}</textarea>
                  @error('address_of_property')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>

              {{-- <div class="row g-4 mb-4">
                  <div class="col-md-6">
                      <label>House Plan Name </label>
                      <input type="text" name="house_plan_name"
                          class="form-control @error('house_plan_name') is-invalid @enderror"
                          value="{{ old('house_plan_name', $owner->house_plan_name ?? '') }}">
                      @error('house_plan_name')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
                  <div class="col-md-6">
                      <label>Build Completion Date <span style="color:red;">*</span></label>
                      <input type="date" name="build_completion_date"
                          class="form-control @error('build_completion_date') is-invalid @enderror"
                          value="{{ old('build_completion_date', $owner->build_completion_date ?? '') }}">
                      @error('build_completion_date')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
              </div> --}}

              {{-- <div class="col-md-12 mb-3">
                  <label>Assigned Builder/Site Manager <span style="color:red;">*</span></label>
                  <input type="text" name="assigned_builder_site_manager"
                      class="form-control @error('assigned_builder_site_manager') is-invalid @enderror"
                      value="{{ old('assigned_builder_site_manager', $owner->assigned_builder_site_manager ?? '') }}">
                  @error('assigned_builder_site_manager')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div> --}}

              {{-- <div class="row g-4 mb-4">
                  <div class="col-md-6 mb-3">
                      <label>Number of Bedrooms <span style="color:red;">*</span></label>
                      <input type="number" name="number_of_bedrooms"
                          class="form-control @error('number_of_bedrooms') is-invalid @enderror"
                          value="{{ old('number_of_bedrooms', $owner->number_of_bedrooms ?? '') }}">
                      @error('number_of_bedrooms')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                      <label>Number of Bathrooms <span style="color:red;">*</span></label>
                      <input type="number" name="number_of_bathrooms"
                          class="form-control @error('number_of_bathrooms') is-invalid @enderror"
                          value="{{ old('number_of_bathrooms', $owner->number_of_bathrooms ?? '') }}">
                      @error('number_of_bathrooms')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
              </div> --}}

              <div class="row g-4 mb-4">
                  <div class="col-md-6 mb-3">
                      <label class="form-label">Handover Documents</label>
                      <input type="file" name="handover_documents[]" class="form-control" multiple
                          onchange="previewImages(event, 'handover-preview')">

                      <div class="mt-2 d-flex flex-wrap" id="handover-preview">
                          @php
                              $handoverDocs = [];

                              if (isset($owner) && !empty($owner->handover_documents)) {
                                  $handoverDocs = is_string($owner->handover_documents)
                                      ? json_decode($owner->handover_documents, true)
                                      : $owner->handover_documents;
                              }
                          @endphp

                          @foreach ($handoverDocs as $doc)
                              @php
                                  $ext = pathinfo($doc, PATHINFO_EXTENSION);
                                  $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                              @endphp

                              <div class="position-relative m-2 text-center img-thumbnail">
                                  @if ($isImage)
                                      <img src="{{ asset('storage/' . $doc) }}" height="80">
                                  @else
                                      <a href="{{ asset('storage/' . $doc) }}" target="_blank">
                                          <i class="fa fa-file fa-3x"></i>
                                          <small class="d-block text-truncate">{{ basename($doc) }}</small>
                                      </a>
                                  @endif

                                  <input type="hidden" name="existing_handover_documents[]"
                                      value="{{ $doc }}">

                                  <button type="button" class="close-btn" onclick="removeExistingImage(this)">
                                      <i class="fa fa-times"></i>
                                  </button>
                              </div>
                          @endforeach
                      </div>
                  </div>
              </div>

              <div class="row g-4 mb-4">
                  <div class="col-md-6">
                      <label class="form-label">Floor Plan Upload</label>
                      <input type="file" name="floor_plan_upload[]" class="form-control" multiple
                          onchange="previewImages(event, 'floorplan-preview')">

                      <div class="mt-2 d-flex flex-wrap" id="floorplan-preview">
                          @php
                              $floorPlans = [];

                              if (isset($owner) && !empty($owner->floor_plan_upload)) {
                                  $floorPlans = is_string($owner->floor_plan_upload)
                                      ? json_decode($owner->floor_plan_upload, true)
                                      : $owner->floor_plan_upload;
                              }
                          @endphp

                          @foreach ($floorPlans as $plan)
                              <div class="position-relative m-2 img-thumbnail">
                                  <img src="{{ asset('storage/' . $plan) }}" height="80">
                                  <input type="hidden" name="existing_floor_plan_upload[]"
                                      value="{{ $plan }}">
                                  <button type="button" class="close-btn" onclick="removeExistingImage(this)">
                                      <i class="fa fa-times"></i>
                                  </button>
                              </div>
                          @endforeach
                      </div>
                  </div>

                  <div class="col-md-6">
                      <label class="form-label">Property Status <span style="color:red;">*</span></label>
                      <input type="text" name="property_status"
                          class="form-control @error('property_status') is-invalid @enderror"
                          value="{{ old('property_status', $owner->property_status ?? '') }}">
                      @error('property_status')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
              </div>

              <div class="row g-4 mb-4">
                  <div class="col-md-6">
                      <label class="form-label">Tags/Label</label>
                      <input type="text" name="tags" class="form-control  @error('tags') is-invalid @enderror"
                          value="{{ old('tags', $owner->tags ?? '') }}">
                      @error('tags')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>

                  <div class="col-md-6">
                      <label class="form-label">Internal Notes</label>
                      <input type="text" name="internal_notes"
                          class="form-control @error('internal_notes') is-invalid @enderror"
                          value="{{ old('internal_notes', $owner->internal_notes ?? '') }}">
                      @error('internal_notes')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                  </div>
              </div>

              <script>
                  document.addEventListener("DOMContentLoaded", function() {
                      // Select all inputs, textarea, and select fields
                      const fields = document.querySelectorAll("input, textarea, select");

                      fields.forEach(field => {
                          field.addEventListener("input", removeError);
                          field.addEventListener("change", removeError);
                      });

                      function removeError(e) {
                          const el = e.target;

                          // Remove red border
                          el.classList.remove("is-invalid");

                          // Remove error message div
                          let next = el.nextElementSibling;
                          if (next && next.classList.contains("invalid-feedback")) {
                              next.remove();
                          }
                      }
                  });
              </script>


              <script>
                  function previewImages(event, previewId) {
                      let preview = document.getElementById(previewId);
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

                          // --- IMAGE PREVIEW ---
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
                          // --- PDF PREVIEW ---
                          else if (isPDF) {
                              div.innerHTML = `
                <i class="fa fa-file-pdf-o fa-3x text-danger"></i>
                <small class="d-block text-truncate">${fileName}</small>
                <button type="button" class="close-btn" onclick="removeImage(this)">
                    <i class="fa fa-times"></i>
                </button>
            `;
                          }
                          // --- DOC / DOCX PREVIEW ---
                          else if (isDoc) {
                              div.innerHTML = `
                <i class="fa fa-file-word-o fa-3x text-primary"></i>
                <small class="d-block text-truncate">${fileName}</small>
                <button type="button" class="close-btn" onclick="removeImage(this)">
                    <i class="fa fa-times"></i>
                </button>
            `;
                          }
                          // --- OTHER FILE TYPES ---
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
                      }
                  }

                  // For removing existing DB images
                  function removeExistingImage(el) {
                      let hiddenInput = el.parentElement.querySelector('input[type=hidden]');
                      if (hiddenInput) hiddenInput.remove(); // This ensures it won't be sent to Laravel
                      el.parentElement.remove(); // Remove from preview
                  }

                  // For removing new uploads before submit
                  function removeImage(el) {
                      el.parentElement.remove();
                  }
              </script>
