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
            @include('partials.navbar')
            <div class="content-wrapper">
                <form action="{{ route('admin.compliance_certificates.update', $certificate->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('admin.compliance_certificates.form', ['certificate' => $certificate])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all">Clear All</button>
                        <button type="submit" class="btn btn-add-property">Update Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('partials.scripts')
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById("attachmentsInput").addEventListener("change", function(event) {
        let preview = document.getElementById("attachmentsPreview");

        const files = event.target.files;

        Array.from(files).forEach(file => {
            let ext = file.name.split('.').pop().toLowerCase();
            let isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);

            let box = document.createElement("div");
            box.classList.add("position-relative", "m-2", "text-center");
            box.style.width = "120px";
            box.style.height = "80px";
            box.style.display = "flex";
            box.style.justifyContent = "center";
            box.style.alignItems = "center";
            box.style.flexDirection = "column";
            box.style.overflow = "hidden";

            // Show image preview
            if (isImage) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    box.innerHTML = `
                        <img src="${e.target.result}" height="80" class="img-thumbnail mb-1">
                        <button type="button" class="close-btn" onclick="removeImage(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            }
            // Show filename ONLY for non-images
            else {
                box.innerHTML = `
                    <div class="file-label small text-truncate" style="max-width:100px;">
                        ${file.name}
                    </div>
                    <button type="button" class="close-btn" onclick="removeImage(this)">
                        <i class="fa fa-times"></i>
                    </button>
                `;
            }

            preview.appendChild(box);
        });
    });

    // Remove new files before form submit
    function removeImage(el) {
        el.parentElement.remove();
    }

    // Store removed existing files
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



</html>
