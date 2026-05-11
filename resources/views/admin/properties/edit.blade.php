<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')

        <div class="main-content">
            <div class="content-wrapper">
                @include('partials.navbar')
                <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 fw-bold">{{ $formTitle }}</h1>
                </div>
                <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                    <h5 class="h5 mb-0">{{ $smallHeading }}</h5>
                </div>

                <form id="propertiesForms" action="{{ route('admin.properties.update', $property) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.properties.create_modal_content', ['property' => $property])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm();">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.scripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcZaByAZLe7qQZAYhKWtc2O9Bn22PAD2E&libraries=places&callback=initAutocomplete"
        async defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dateInput = document.getElementById("build_completion_date");
            dateInput.addEventListener("focus", function() {
                this.showPicker();
            });
            dateInput.addEventListener("click", function() {
                this.showPicker();
            });
        });
    </script>

<!-- Add House Plan Modal -->
<div class="modal fade" id="addHousePlanModal" aria-labelledby="addHousePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHousePlanModalLabel">Add House Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ajaxHousePlanForm" action="{{ route('admin.house_plans.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_ajax" value="1">
                    @include('admin.house_plans.form', ['formTitle' => '', 'housePlan' => new \App\Models\HousePlan()])
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-add-property" id="submitAjaxHousePlan">Save House Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const submitBtn = document.getElementById('submitAjaxHousePlan');
    if(submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxHousePlanForm');
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.innerText = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if(!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Save House Plan';

                if(data.success) {
                    // close modal
                    const modalEl = document.getElementById('addHousePlanModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();

                    // remove modal backdrop manually if it stays stuck
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');

                    // show toastr success
                    if(typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    }

                    // add option to dropdown
                    const housePlanSelect = document.querySelector('select[name="house_plan_id"]');
                    if(housePlanSelect) {
                        const newOption = new Option(data.house_plan.plan_name, data.house_plan.id, false, true);
                        housePlanSelect.appendChild(newOption);
                        if ($(housePlanSelect).hasClass("select2-hidden-accessible")) {
                            $(housePlanSelect).trigger('change');
                        }
                    }

                    // clear form
                    form.reset();
                    const storeySelect = form.querySelector('#storey');
                    if(storeySelect) storeySelect.dispatchEvent(new Event('change'));
                } else {
                    if(typeof toastr !== 'undefined') {
                        toastr.error('Validation Error. Please check fields.');
                    }
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Save House Plan';
                console.error("AJAX Error:", error);
                
                let errorMsg = 'Something went wrong. Please check validation.';
                if(error.errors) {
                    const firstErrorKey = Object.keys(error.errors)[0];
                    errorMsg = error.errors[firstErrorKey][0];
                }
                
                if(typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                }
            });
        });
    }
});
    </script>

<!-- Add Appliance Modal -->
<div class="modal fade" id="addApplianceModal" aria-labelledby="addApplianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addApplianceModalLabel">Add New Appliance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ajaxApplianceForm" action="{{ route('admin.appliances.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_ajax" value="1">
                    @php
                        $lastAppliance = \App\Models\Appliance::orderBy('id', 'desc')->first();
                        $nextNumber = $lastAppliance ? ((int) filter_var($lastAppliance->appliance_id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
                        $nextApplianceId = 'APP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    @endphp
                    @include('admin.appliances.form', ['formTitle' => '', 'nextApplianceId' => $nextApplianceId, 'appliances' => new \App\Models\Appliance()])
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-add-property" id="submitAjaxAppliance">Save Appliance</button>
            </div>
        </div>
    </div>
</div>

<script>
let lastApplianceTrigger = null;
document.addEventListener("DOMContentLoaded", function() {
    const applianceModal = document.getElementById('addApplianceModal');
    if (applianceModal) {
        applianceModal.addEventListener('show.bs.modal', function(event) {
            lastApplianceTrigger = event.relatedTarget;
        });
    }

    const submitBtn = document.getElementById('submitAjaxAppliance');
    if(submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxApplianceForm');
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.innerText = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if(!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Save Appliance';

                if(data.success) {
                    // close modal
                    const modalEl = document.getElementById('addApplianceModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();

                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');

                    // show toastr success
                    if(typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    }

                    // add option to ALL appliance dropdowns on the page
                    const applianceSelects = document.querySelectorAll('.appliance-select');
                    applianceSelects.forEach(select => {
                        // Determine if we should select this option in this specific dropdown
                        let shouldSelect = false;
                        if (lastApplianceTrigger) {
                            const container = lastApplianceTrigger.closest('.mb-3') || lastApplianceTrigger.closest('.col-12');
                            if (container && container.contains(select)) {
                                shouldSelect = true;
                            }
                        }

                        const newOption = new Option(`${data.appliance.brand_name} - ${data.appliance.model}`, data.appliance.id, false, shouldSelect);
                        select.appendChild(newOption);
                        if ($(select).hasClass("select2-hidden-accessible")) {
                            $(select).trigger('change');
                        }
                    });

                    // clear form
                    form.reset();
                    const preview1 = form.querySelector('#manuals-preview');
                    const preview2 = form.querySelector('#appliances-preview');
                    if(preview1) preview1.innerHTML = '';
                    if(preview2) preview2.innerHTML = '';
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Save Appliance';
                console.error("AJAX Error:", error);
                let errorMsg = 'Something went wrong. Please check validation.';
                if(error.errors) {
                    const firstErrorKey = Object.keys(error.errors)[0];
                    errorMsg = error.errors[firstErrorKey][0];
                }
                if(typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                }
            });
        });
    }
});
</script>

</body>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('.select2').each(function() {
            const dropdownParent = jQuery(this).closest('.modal').length ? jQuery(this).closest('.modal') : jQuery(document.body);
            jQuery(this).select2({
                width: '100%',
                dropdownParent: dropdownParent,
                theme: 'bootstrap-5'
            });
        });
    });
</script>
</html>

