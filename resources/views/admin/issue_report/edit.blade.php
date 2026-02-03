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
                <form action="{{ route('admin.issue_report.update', $issueReport->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.issue_report.form', ['owner' => $issueReport])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update Tenants
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    function previewIssueImages(event) {
        let previewContainer = document.getElementById("imagePreviewContainer");
        const files = event.target.files;

        for (let file of files) {
            let fileName = file.name;
            let fileExt = fileName.split('.').pop().toLowerCase();
            let isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);

            let div = document.createElement("div");
            div.classList.add("position-relative", "image-wrapper");
            div.style.display = "inline-block";
           
            if (isImage) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    div.innerHTML = `
                    <img src="${e.target.result}" width="80" class="img-thumbnail">
                    <button type="button"
                            class="close-btn"
                            onclick="removeNewIssueImage(this)"
                            style="
                                border-radius: 50%;
                                padding: 3px 7px;
                                font-size: 14px;
                                position: absolute;
                                top: 0;
                                right: 0;
                            ">
                        ×
                    </button>
                `;
                };
                reader.readAsDataURL(file);
            } else {
                div.innerHTML = `
                <i class="fa fa-file fa-3x text-secondary"></i>
                <small class="d-block text-truncate">${fileName}</small>
                <button type="button"
                        class="close-btn"
                        onclick="removeNewIssueImage(this)"
                        style="
                            border-radius: 50%;
                            padding: 3px 7px;
                            font-size: 14px;
                            position: absolute;
                            top: 0;
                            right: 0;
                        ">
                    ×
                </button>
            `;
            }

            previewContainer.appendChild(div);
        }
    }
    @if(session('success'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };
    toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif
</script>

</html>