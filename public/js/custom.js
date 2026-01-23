$(document).ready(function () {
    $("#appliance_id").select2({
        placeholder: "Select Appliances",
        allowClear: true,
        width: "100%",
        theme: "bootstrap-5",
    });
    // $("#propertiesTable").DataTable({
    //   pageLength: 5,
    //   lengthMenu: [5, 10, 25, 50],
    //   ordering: true,
    //   searching: false,
    //   responsive: true,
    //   language: {
    //     search: "🔍 Search:",
    //     lengthMenu: "Show _MENU_ entries",
    //     info: "Showing _START_ to _END_ of _TOTAL_ properties",
    //   },
    // });
    document
        .getElementById("fileUpload")
        .addEventListener("change", function (event) {
            const previewContainer =
                document.getElementById("preview-container");

            const newFiles = Array.from(event.target.files).filter((f) =>
                f.type.startsWith("image/"),
            );

            newFiles.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewBox = document.createElement("div");
                    previewBox.classList.add("mb-2", "preview-image");

                    const wrapper = document.createElement("div");
                    wrapper.classList.add(
                        "position-relative",
                        "m-2",
                        "img-thumbnail",
                    );

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.className = "img-fluid img-thumbnail";
                    img.style.height = "80px";
                    //img.style.objectFit = "cover";

                    const closeBtn = document.createElement("button");
                    closeBtn.type = "button";
                    closeBtn.className = "close-btn";
                    closeBtn.innerHTML = "&times;";
                    closeBtn.title = "Remove";

                    closeBtn.addEventListener("click", () => {
                        previewBox.remove();

                        const dt = new DataTransfer();
                        const currentFiles = Array.from(event.target.files);

                        currentFiles.forEach((f) => {
                            if (f.name !== file.name) dt.items.add(f);
                        });

                        event.target.files = dt.files;

                        document.getElementById("floorPlanUpload").value =
                            Array.from(dt.files)
                                .map((f) => f.name)
                                .join(", ");
                    });

                    wrapper.appendChild(img);
                    wrapper.appendChild(closeBtn);
                    previewBox.appendChild(wrapper);
                    previewContainer.appendChild(previewBox);
                };

                reader.readAsDataURL(file);
            });

            document.getElementById("floorPlanUpload").value = newFiles
                .map((f) => f.name)
                .join(", ");
        });
});

document.querySelectorAll(".remove-image-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
        const index = this.dataset.index;
        this.closest(".preview-image").remove();
        const removedInput = document.createElement("input");
        removedInput.type = "hidden";
        removedInput.name = "removed_existing_images[]";
        removedInput.value = index; // or use image path if needed

        document.querySelector("form").appendChild(removedInput);
    });
});

// JavaScript for page navigation
function showPage(pageId) {
    document.querySelectorAll(".auth-page").forEach((page) => {
        page.style.display = "none";
    });
    document.getElementById(pageId).style.display = "block";
}

// JavaScript for password visibility toggle
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.querySelector(`#${inputId} + .input-group-text i`);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}

function toggleSuperadmminPasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.querySelector(
        `#${inputId} + .cursor-pointer .input-group-text i`,
    );

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}

// JavaScript for auto-focus and auto-tabbing for verification codes
document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".verification-input");
    inputs.forEach((input, index) => {
        input.addEventListener("keyup", (e) => {
            const value = e.target.value;
            if (value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            } else if (e.key === "Backspace" && value.length === 0) {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });
    });
});

const sidebarToggleBtn = document.getElementById("sidebar-toggle-btn");
const sidebar = document.getElementById("sidebar");
const sidebarBackdrop = document.getElementById("sidebar-backdrop");
const navLinks = document.querySelectorAll(".sidebar .nav-link");

const toggleSidebar = () => {
    sidebar.classList.toggle("show");
    if (sidebar.classList.contains("show")) {
        sidebarBackdrop.style.display = "block";
    } else {
        sidebarBackdrop.style.display = "none";
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggleBtn = document.getElementById("sidebarToggleBtn");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    function toggleSidebar() {
        document.body.classList.toggle("sidebar-open");
    }

    sidebarToggleBtn?.addEventListener("click", toggleSidebar);
    sidebarBackdrop?.addEventListener("click", toggleSidebar);
});

window.addEventListener("resize", () => {
    if (window.innerWidth >= 768) {
        sidebar.classList.remove("show");
        sidebarBackdrop.style.display = "none";
    }
});

// Function to handle active state for sidebar links
navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
        navLinks.forEach((item) => {
            item.classList.remove("active");
        });
        e.currentTarget.classList.add("active");
    });
});

function clearForm() {
    // Reset all forms safely
    $(
        "#issueReportForm, #houseOwnerForm, #propertiesForm, #housePlan, #applianceForm",
    ).each(function () {
        this.reset();
    });

    // Clear previews
    const floorPreview = document.getElementById("floorplan-preview");
    if (floorPreview) floorPreview.innerHTML = "";

    const floorInput = document.getElementById("floorplan");
    if (floorInput) floorInput.value = "";

    const manualPreview = document.getElementById("manuals-preview");
    if (manualPreview) manualPreview.innerHTML = "";

    const appliancesPreview = document.getElementById("appliances-preview");
    if (appliancesPreview) appliancesPreview.innerHTML = "";
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".suspend-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const form = this.closest(".suspend-form");
            Swal.fire({
                title: "Suspend Account?",
                text: "This user will be suspended and lose access.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, suspend",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll(".properties-suspend-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const form = this.closest(".suspend-form");
            Swal.fire({
                title: "Update Property Status ?",
                text: "Property Status changed as a pending.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

// Select all
document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("select-all")
        ?.addEventListener("change", function () {
            document.querySelectorAll(".row-checkbox").forEach((cb) => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
        });

    document.querySelectorAll(".row-checkbox").forEach((cb) => {
        cb.addEventListener("change", updateSelectedCount);
    });
});

function updateSelectedCount() {
    const count = document.querySelectorAll(".row-checkbox:checked").length;
    const counter = document.querySelector(
        "#bulk-actions span.text-neutral-500",
    );

    if (counter) {
        counter.innerText = count + " selected";
    }
}

function submitBulkAction(action) {
    const selected = Array.from(
        document.querySelectorAll(".row-checkbox:checked"),
    ).map((cb) => cb.value);

    if (selected.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select at least one record",
        });
        return;
    }

    document.getElementById("bulk-action-type").value = action;
    document.getElementById("bulk-user-ids").value = selected.join(",");
    document.getElementById("bulk-action-form").submit();
}

function openSendMessageModal() {
    const checked = document.querySelectorAll(".row-checkbox:checked");
    if (checked.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select at least one builder",
        });
        return;
    }
    const emails = Array.from(checked).map((cb) => cb.dataset.email);
    document.getElementById("selected-emails").value = emails.join(",");
    document.getElementById("email-preview").value = emails.join(", ");
    const modal = new bootstrap.Modal(
        document.getElementById("sendMessageModal"),
    );
    modal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const form = this.closest(".delete-form");

            Swal.fire({
                title: "Delete Record?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".view-builder-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const picture = this.dataset.picture;
            const img = document.getElementById("picture-name");
            img.src = picture;
            img.alt = this.dataset.name;

            document.getElementById("view-name").innerText = this.dataset.name;
            document.getElementById("view-email").innerText =
                this.dataset.email;
            document.getElementById("view-phone").innerText =
                this.dataset.phone;
            document.getElementById("view-status").innerText =
                this.dataset.status;
            document.getElementById("view-created").innerText =
                this.dataset.created;
        });
    });

    document.querySelectorAll(".view-properties-btn").forEach((button) => {
        button.addEventListener("click", function () {
            // ---------- Profile Picture ----------
            // const img = document.getElementById("picture-name");
            // img.src = this.dataset.picture ?? "";
            // img.alt = "Builder Profile";

            // ---------- Text Fields ----------
            document.getElementById("property_title").innerText =
                this.dataset.propertytitle;
            document.getElementById("description").innerText =
                this.dataset.description;
            document.getElementById("property_type").innerText =
                this.dataset.propertytype;
            document.getElementById("property_status").innerText =
                this.dataset.propertystatus;
            document.getElementById("address").innerText = this.dataset.address;
            document.getElementById("house_plan_name").innerText =
                this.dataset.houseplanname;
            document.getElementById("build_completion_date").innerText =
                this.dataset.buildcompletiondate;
            document.getElementById("number_of_bedrooms").innerText =
                this.dataset.numberofbedrooms;
            document.getElementById("number_of_bathrooms").innerText =
                this.dataset.numberofbathrooms;
            document.getElementById("parking").innerText = this.dataset.parking;
            document.getElementById("swimming_pool").innerText =
                this.dataset.swimmingpool == 1 ? "Available" : "Not Available";
            document.getElementById("tags").innerText = this.dataset.tags;
            document.getElementById("internal_notes").innerText =
                this.dataset.internalnotes;
            document.getElementById("compliance_certificate").innerText =
                this.dataset.compliancecertificate;
            document.getElementById("created_at").innerText =
                this.dataset.createdat;

            const baseUrl = this.dataset.baseurl;

            const floorPlans = JSON.parse(this.dataset.floorplanupload || "[]");
            const container = document.getElementById("floor_plans_container");
            container.innerHTML = "";

            if (floorPlans.length > 0) {
                floorPlans.forEach((path) => {
                    const img = document.createElement("img");
                    img.src = baseUrl + "public/storage/" + path;
                    img.className = "rounded border h-auto w-25 object-cover";
                    container.appendChild(img);
                });
            } else {
                container.innerHTML =
                    "<p class='text-gray-500'>No floor plans available</p>";
            }
        });
    });

    document.querySelectorAll(".view-houseowners-btn").forEach((button) => {
        button.addEventListener("click", function () {
            // ---------- Text Fields ----------
            document.getElementById("houseownerid").innerText =
                this.dataset.houseownerid;
            document.getElementById("firstname").innerText =
                this.dataset.firstname;
            document.getElementById("lastname").innerText =
                this.dataset.lastname;
            document.getElementById("email").innerText = this.dataset.email;
            document.getElementById("phonenumber").innerText =
                this.dataset.phonenumber;
            document.getElementById("properydetails").innerText =
                this.dataset.properydetails;
            document.getElementById("addressofproperty").innerText =
                this.dataset.addressofproperty;
            document.getElementById("tags").innerText = this.dataset.tags;
            document.getElementById("internalnotes").innerText =
                this.dataset.internalnotes;
            document.getElementById("created_at").innerText =
                this.dataset.createdat;

            const baseUrl = this.dataset.baseurl;

            const handoverdoc = JSON.parse(
                this.dataset.handoverdocuments || "[]",
            );
            const handovercontainer =
                document.getElementById("handoverdocuments");
            handovercontainer.innerHTML = "";

            if (handoverdoc.length > 0) {
                handoverdoc.forEach((path) => {
                    const img = document.createElement("img");
                    img.src = baseUrl + "public/storage/" + path;
                    img.className = "rounded border h-auto w-25 object-cover";
                    handovercontainer.appendChild(img);
                });
            } else {
                container.innerHTML =
                    "<p class='text-gray-500'>No floor plans available</p>";
            }

            const floorPlans = JSON.parse(this.dataset.floorplanupload || "[]");
            const container = document.getElementById("floorplanupload");
            container.innerHTML = "";

            if (floorPlans.length > 0) {
                floorPlans.forEach((path) => {
                    const img = document.createElement("img");
                    img.src = baseUrl + "public/storage/" + path;
                    img.className = "rounded border h-auto w-25 object-cover";
                    container.appendChild(img);
                });
            } else {
                container.innerHTML =
                    "<p class='text-gray-500'>No floor plans available</p>";
            }
        });
    });

    document.querySelectorAll(".view-providers-btn").forEach((button) => {
        button.addEventListener("click", function () {
            const picture = this.dataset.picture;
            const img = document.getElementById("picture-name");
            img.src = picture;
            img.alt = this.dataset.name;

            document.getElementById("view-name").innerText =
                this.dataset.firstname;
            document.getElementById("last-name").innerText =
                this.dataset.lastname;
            document.getElementById("view-email").innerText =
                this.dataset.email;
            document.getElementById("view-phone").innerText =
                this.dataset.phone;
            document.getElementById("servicespecialisation").innerText =
                this.dataset.servicespecialisation;
            document.getElementById("servicetype").innerText =
                this.dataset.servicetype;
            document.getElementById("view-status").innerText =
                this.dataset.status;
            document.getElementById("view-created").innerText =
                this.dataset.created;
        });
    });
});

function submitPropertyBulkAction(action) {
    const selected = Array.from(
        document.querySelectorAll(".property-checkbox:checked"),
    ).map((cb) => cb.value);

    if (selected.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select at least one property",
        });
        return;
    }

    document.getElementById("bulk_action").value = action;
    document.getElementById("bulk_property_ids").value = selected.join(",");
    document.getElementById("bulkActionForm").submit();
}

document.addEventListener("DOMContentLoaded", function () {
    const selectAll = document.getElementById("select-all");

    // Select all checkboxes
    selectAll?.addEventListener("change", function () {
        document.querySelectorAll(".property-checkbox").forEach((cb) => {
            cb.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    document.querySelectorAll(".property-checkbox").forEach((cb) => {
        cb.addEventListener("change", updateSelectedCount);
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll(
            ".property-checkbox:checked",
        ).length;
        const counter = document.querySelector(
            "#bulk-actionss span.text-neutral-500",
        );

        if (counter) {
            counter.innerText = `${count} selected`;
        }
    }
});

document.getElementById("logout-btn").addEventListener("click", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, logout",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("logout-form").submit();
        }
    });
});

document.getElementById("logout-btns").addEventListener("click", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, logout",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("logout-form").submit();
        }
    });
});
