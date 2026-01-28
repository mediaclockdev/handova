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
                <form action="{{ route('admin.appliances.store') }}" method="POST" enctype="multipart/form-data"
                    id="applianceForm">
                    @csrf

                    @include('admin.appliances.form')

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Add Appliances
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
    function previewFiles(event, previewId) {

        const preview = document.getElementById(previewId);
        const files = event.target.files;

        for (let file of files) {
            const div = document.createElement('div');
            div.classList.add('position-relative', 'm-2', 'text-center');

            const fileType = file.type;
            const fileName = file.name;
            const reader = new FileReader();

            // Image Preview
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

            // PDF
            else if (fileType === "application/pdf") {
                div.innerHTML = `
                <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 40px; color:red;"></i>
                </div>
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
                preview.appendChild(div);
            }

            // CSV / Excel
            else if (
                fileType === "text/csv" ||
                fileName.endsWith(".csv") ||
                fileName.endsWith(".xls") ||
                fileName.endsWith(".xlsx")
            ) {
                div.innerHTML = `
                <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-spreadsheet" style="font-size: 40px; color:green;"></i>
                </div>
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
                preview.appendChild(div);
            }

            // Other file types
            else {
                div.innerHTML = `
                <div class="border rounded p-2 bg-light" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark" style="font-size: 40px; color:gray;"></i>
                </div>
                <span class="close-btn" onclick="removeImage(this)">×</span>
            `;
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
