<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>
</div>

<div class="row g-4 mb-4">

    <div class="col-md-6">
        <label class="form-label">Plan Name <span style="color:red;">*</span></label>
        <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror"
            value="{{ old('plan_name', $housePlan->plan_name ?? '') }}">
        @error('plan_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Storey <span style="color:red;">*</span></label>
        <select name="storey" id="storey" class="form-select @error('storey') is-invalid @enderror">
            <option value="">Select Storey</option>
            <option value="ground_floor"
                {{ old('storey', $housePlan->storey ?? '') == 'ground_floor' ? 'selected' : '' }}>
                1
            </option>
            <option value="first_floor"
                {{ old('storey', $housePlan->storey ?? '') == 'first_floor' ? 'selected' : '' }}>
                2
            </option>
            <option value="second_floor"
                {{ old('storey', $housePlan->storey ?? '') == 'second_floor' ? 'selected' : '' }}>
                3
            </option>
            <option value="third_floor"
                {{ old('storey', $housePlan->storey ?? '') == 'third_floor' ? 'selected' : '' }}>
                4
            </option>
            <option value="fourth_floor"
                {{ old('storey', $housePlan->storey ?? '') == 'fourth_floor' ? 'selected' : '' }}>
                5
            </option>
        </select>
        @error('storey')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Pricing <span style="color:red;">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="pricing" class="form-control @error('pricing') is-invalid @enderror"
                value="{{ old('pricing', $housePlan->pricing ?? '') }}" placeholder="0.00" step="0.01">
        </div>
        @error('pricing')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">House Area</label>
        <input type="number" name="house_area" class="form-control @error('house_area') is-invalid @enderror"
            value="{{ old('house_area', $housePlan->house_area ?? '') }}">
        @error('house_area')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Suburbs</label>
        <input type="text" name="suburbs" class="form-control @error('suburbs') is-invalid @enderror"
            value="{{ old('suburbs', $housePlan->suburbs ?? '') }}">
        @error('suburbs')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    <div class="col-md-6">
        <label class="form-label">Display Location</label>
        <input type="text" name="display_location"
            class="form-control @error('display_location') is-invalid @enderror"
            value="{{ old('display_location', $housePlan->display_location ?? '') }}">

        @error('display_location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


</div>

{{-- FLOOR DATA --}}
@php
    $floors = [
        'ground_floor' => '1',
        'first_floor' => '2',
        'second_floor' => '3',
        'third_floor' => '4',
        'fourth_floor' => '5',
    ];

    $floorData = old('floor', $housePlan->floor ?? []);
@endphp

@foreach ($floors as $key => $label)
    <div class="floor-section d-none border rounded p-3 mb-4" data-floor="{{ $key }}">

        <h4 class="mb-3">{{ $label }} Details</h4>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">No. of Bedrooms</label>
                <input type="number" name="floor[{{ $key }}][bedrooms]" class="form-control"
                    placeholder="Bedrooms" value="{{ $floorData[$key]['bedrooms'] ?? '' }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="form-label">No. of Bathrooms</label>
                <input type="number" name="floor[{{ $key }}][bathrooms]" class="form-control"
                    placeholder="Bathrooms" value="{{ $floorData[$key]['bathrooms'] ?? '' }}">
            </div>

            <div class="col-md-6 mb-2">
                <label class="form-label">Parking</label>
                <select name="floor[{{ $key }}][parking]" class="form-control">
                    <option value="">Select Parking</option>

                    @for ($i = 0; $i <= 9; $i++)
                        <option value="{{ $i }}"
                            {{ ($floorData[$key]['parking'] ?? '') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>


            <div class="col-md-6 mb-2">
                <label class="form-label">Swimming Pool</label>
                <select name="floor[{{ $key }}][swimming_pool]" class="form-control">
                    <option value="">Swimming Pool</option>
                    <option value="yes" {{ ($floorData[$key]['swimming_pool'] ?? '') == 'yes' ? 'selected' : '' }}>
                        Yes
                    </option>
                    <option value="no" {{ ($floorData[$key]['swimming_pool'] ?? '') == 'no' ? 'selected' : '' }}>
                        No
                    </option>
                </select>
            </div>



        </div>

        {{-- APPLIANCES --}}
        <div class="mb-3">
            <label class="form-label">Appliances</label>
            <select name="floor[{{ $key }}][appliances][]" class="form-control select2" multiple>
                @foreach ($appliances as $appliance)
                    <option value="{{ $appliance->id }}"
                        {{ in_array($appliance->id, $floorData[$key]['appliances'] ?? []) ? 'selected' : '' }}>
                        {{ $appliance->brand_name }} - {{ $appliance->model }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- FLOOR PLAN --}}
        <div>
            <label class="form-label">Floor Plan Images</label>
            <input type="file" name="floor_plan[{{ $key }}][]" class="form-control floor-plan-input"
                data-floor="{{ $key }}" multiple accept="image/*">

        </div>
        <div id="floor-previews" class="d-flex flex-wrap mt-2 floor-preview" data-preview="{{ $key }}">

            {{-- EXISTING FLOOR PLAN IMAGES --}}
            @if (!empty($floorData[$key]['floor_plan']))
                @foreach ($floorData[$key]['floor_plan'] as $image)
                    <div class="position-relative m-2 existing-image">
                        <img src="{{ asset('storage/' . $image) }}" class="border rounded" height="100">

                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 close-btn"
                            data-floor="{{ $key }}" data-image="{{ $image }}">
                            &times;
                        </button>

                        {{-- keep existing image --}}
                        <input type="hidden" name="existing_floor_plan[{{ $key }}][]"
                            value="{{ $image }}">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endforeach


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("click", function(e) {

        const btn = e.target.closest(".close-btn");
        if (!btn) return;

        const wrapper = btn.closest(".existing-image");
        const floor = btn.dataset.floor;
        const imagePath = btn.dataset.image;

        // Remove the hidden input that keeps this image
        const hiddenInput = wrapper.querySelector(
            `input[name="existing_floor_plan[${floor}][]"][value="${imagePath}"]`
        );

        if (hiddenInput) {
            hiddenInput.remove();
        }

        // Remove image preview
        wrapper.remove();
    });
</script>
<script>
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            width: '100%'
        });
    });

    document.addEventListener("DOMContentLoaded", function() {

        const storeySelect = document.getElementById("storey");
        if (!storeySelect) return;

        const floorSections = document.querySelectorAll(".floor-section");
        const floorOrder = ["ground_floor", "first_floor", "second_floor", "third_floor", "fourth_floor"];

        function toggleFloors() {
            floorSections.forEach(section => {
                section.classList.add("d-none");
            });

            const selected = storeySelect.value;
            if (!selected) return;

            const selectedIndex = floorOrder.indexOf(selected);

            floorOrder.forEach((floor, index) => {
                if (index <= selectedIndex) {
                    const el = document.querySelector(`[data-floor="${floor}"]`);
                    if (el) {
                        el.classList.remove("d-none");

                        $(el).find('.select2').each(function() {
                            if ($(this).hasClass("select2-hidden-accessible")) {
                                $(this).select2('destroy');
                            }
                            $(this).select2({
                                width: '100%'
                            });
                        });
                    }
                }
            });
        }

        storeySelect.addEventListener("change", toggleFloors);
        toggleFloors();
    });
</script>


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.querySelectorAll(".floor-plan-input").forEach(input => {

                input.addEventListener("change", function() {

                    const floorKey = this.dataset.floor;
                    const previewBox = document.querySelector(
                        `.floor-preview[data-preview="${floorKey}"]`
                    );

                    if (!previewBox) return;

                    Array.from(this.files).forEach(file => {

                        if (!file.type.startsWith("image/")) return;

                        const reader = new FileReader();

                        reader.onload = function(e) {

                            const wrapper = document.createElement("div");
                            wrapper.className = "position-relative m-2 new-image";

                            wrapper.innerHTML = `
                <img src="${e.target.result}" class="border rounded" height="100">
                <button type="button"
                    class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-new">
                    &times;
                </button>
            `;

                            // ✅ APPEND — DO NOT CLEAR EXISTING
                            previewBox.appendChild(wrapper);
                        };

                        reader.readAsDataURL(file);
                    });

                });

            });

            // Remove new preview only (does not affect input files)
            document.addEventListener("click", function(e) {
                const btn = e.target.closest(".remove-new");
                if (!btn) return;
                btn.closest(".new-image").remove();
            });

        });
    </script>






    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const fileInput = document.getElementById("attachmentsInput");
            const previewContainer = document.getElementById("attachmentsPreview");

            // ✅ ADD GUARD — DO NOT REMOVE ANYTHING
            if (!fileInput || !previewContainer) {
                return;
            }

            const formEl = previewContainer.closest("form");

            let selectedFiles = []; // Will store new uploads

            // ---------------------------
            // RENDER NEW UPLOADED FILES
            // ---------------------------
            fileInput.addEventListener("change", function(e) {
                const newFiles = Array.from(e.target.files);

                selectedFiles = selectedFiles.concat(newFiles);

                renderNewFiles();
                rebuildFileList();
            });

            // REST CODE REMAINS SAME
        });
    </script>
@endpush
