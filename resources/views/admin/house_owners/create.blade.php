<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')

        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <div class="main-content">
            <div class="content-wrapper">
                @include('partials.navbar')
                <form id="houseOwnerForm" action="{{ route('admin.house_owners.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    @include('admin.house_owners.form', ['owner' => null])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Add House Owner
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @include('partials/scripts')
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
</body>

<script>
    @if (session('success'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

<!-- Add Property Modal -->
<div class="modal fade" id="addPropertyModal" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPropertyModalLabel">Add Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ajaxPropertyForm" action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_ajax" value="1">
                    @include('admin.properties.create_modal_content', ['formTitle' => '', 'property' => new \App\Models\Property()])
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-add-property" id="submitAjaxProperty">Save Property</button>
            </div>
        </div>
    </div>
</div>

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
    // Modal trigger tracking
    const applianceModal = document.getElementById('addApplianceModal');
    if (applianceModal) {
        applianceModal.addEventListener('show.bs.modal', function(event) {
            lastApplianceTrigger = event.relatedTarget;
        });
    }

    // PROPERTY AJAX
    const submitPropertyBtn = document.getElementById('submitAjaxProperty');
    if(submitPropertyBtn) {
        submitPropertyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxPropertyForm');
            const formData = new FormData(form);
            submitPropertyBtn.disabled = true;
            submitPropertyBtn.innerText = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => { throw err; }))
            .then(data => {
                submitPropertyBtn.disabled = false;
                submitPropertyBtn.innerText = 'Save Property';
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addPropertyModal'));
                    modal.hide();
                    if(typeof toastr !== 'undefined') toastr.success(data.message);
                    
                    const propertySelect = document.querySelector('select[name="properties_id"]');
                    if(propertySelect) {
                        const newOption = new Option(data.property.property_title, data.property.id, false, true);
                        propertySelect.appendChild(newOption);
                        if ($(propertySelect).hasClass("select2-hidden-accessible")) $(propertySelect).trigger('change');
                    }
                    form.reset();
                }
            })
            .catch(error => {
                submitPropertyBtn.disabled = false;
                submitPropertyBtn.innerText = 'Save Property';
                let errorMsg = error.errors ? error.errors[Object.keys(error.errors)[0]][0] : 'Something went wrong.';
                if(typeof toastr !== 'undefined') toastr.error(errorMsg);
            });
        });
    }

    // HOUSE PLAN AJAX
    const submitHousePlanBtn = document.getElementById('submitAjaxHousePlan');
    if(submitHousePlanBtn) {
        submitHousePlanBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxHousePlanForm');
            const formData = new FormData(form);
            submitHousePlanBtn.disabled = true;
            submitHousePlanBtn.innerText = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => { throw err; }))
            .then(data => {
                submitHousePlanBtn.disabled = false;
                submitHousePlanBtn.innerText = 'Save House Plan';
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addHousePlanModal'));
                    modal.hide();
                    if(typeof toastr !== 'undefined') toastr.success(data.message);

                    const housePlanSelect = document.querySelector('select[name="house_plan_id"]');
                    if(housePlanSelect) {
                        const newOption = new Option(data.house_plan.plan_name, data.house_plan.id, false, true);
                        housePlanSelect.appendChild(newOption);
                        if ($(housePlanSelect).hasClass("select2-hidden-accessible")) $(housePlanSelect).trigger('change');
                    }
                    form.reset();
                }
            })
            .catch(error => {
                submitHousePlanBtn.disabled = false;
                submitHousePlanBtn.innerText = 'Save House Plan';
                let errorMsg = error.errors ? error.errors[Object.keys(error.errors)[0]][0] : 'Something went wrong.';
                if(typeof toastr !== 'undefined') toastr.error(errorMsg);
            });
        });
    }

    // APPLIANCE AJAX
    const submitApplianceBtn = document.getElementById('submitAjaxAppliance');
    if(submitApplianceBtn) {
        submitApplianceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxApplianceForm');
            const formData = new FormData(form);
            submitApplianceBtn.disabled = true;
            submitApplianceBtn.innerText = 'Saving...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => { throw err; }))
            .then(data => {
                submitApplianceBtn.disabled = false;
                submitApplianceBtn.innerText = 'Save Appliance';
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addApplianceModal'));
                    modal.hide();
                    if(typeof toastr !== 'undefined') toastr.success(data.message);

                    const applianceSelects = document.querySelectorAll('.appliance-select');
                    applianceSelects.forEach(select => {
                        let shouldSelect = false;
                        if (lastApplianceTrigger) {
                            const container = lastApplianceTrigger.closest('.mb-3') || lastApplianceTrigger.closest('.col-12');
                            if (container && container.contains(select)) shouldSelect = true;
                        }
                        const newOption = new Option(`${data.appliance.brand_name} - ${data.appliance.model}`, data.appliance.id, false, shouldSelect);
                        select.appendChild(newOption);
                        if ($(select).hasClass("select2-hidden-accessible")) $(select).trigger('change');
                    });
                    form.reset();
                }
            })
            .catch(error => {
                submitApplianceBtn.disabled = false;
                submitApplianceBtn.innerText = 'Save Appliance';
                let errorMsg = error.errors ? error.errors[Object.keys(error.errors)[0]][0] : 'Something went wrong.';
                if(typeof toastr !== 'undefined') toastr.error(errorMsg);
            });
        });
    }
});
</script>

</html>
