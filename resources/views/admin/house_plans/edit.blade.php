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
                <form id="housePlans" action="{{ route('admin.house_plans.update', $housePlan->id) }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf
                    @method('PUT')
                    @include('admin.house_plans.form', ['housePlan' => $housePlan])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update House Plan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
{{-- Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />

{{-- Include jQuery first, then Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                        wrapper.className = "position-relative m-2 preview-item";

                        wrapper.innerHTML = `
                        <img src="${e.target.result}" class="border rounded" height="100">
                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 close-btn">
                            &times;
                        </button>
                    `;

                        previewBox.appendChild(wrapper);
                    };

                    reader.readAsDataURL(file);
                });
            });
        });

        // ✅ CLOSE BUTTON WORKING
        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".close-btn");
            if (!btn) return;

            const wrapper = btn.closest(".preview-item");
            if (wrapper) wrapper.remove();
        });

    });
</script>


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

</html>
