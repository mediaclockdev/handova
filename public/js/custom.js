$(document).ready(function () {
    if (document.getElementById("appliance_id")) {
        $("#appliance_id").select2({
            placeholder: "Select Appliances",
            allowClear: true,
            width: "100%",
            theme: "bootstrap-5",
        });
    }
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
    if (document.getElementById("fileUpload")) {
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
    }
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
        if (sidebar) {
            sidebar.classList.remove("show");
        }
        if (sidebarBackdrop) {
            sidebarBackdrop.style.display = "none";
        }
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
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to clear all entered data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, clear it",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $(
            "#issueReportForm, #houseOwnerForm, #propertiesForm, #housePlan, #housePlans, #applianceForm, #complianceCertificateForm",
            "#propertiesForms",
        ).each(function () {
            this.reset();
        });

        $("#propertiesForms,#propertiesForm")
            .find("input, textarea, select")
            .val("");

        // Clear previews
        const clearHTML = (id) => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = "";
        };

        const clearValue = (id) => {
            const el = document.getElementById(id);
            if (el) el.value = "";
        };

        clearHTML("preview-container");
        clearHTML("floorplan-preview");
        clearHTML("manuals-preview");
        clearHTML("appliances-preview");
        clearHTML("floor-previews");
        clearHTML("attachmentsPreview");

        clearValue("floorplan");

        Swal.fire({
            icon: "success",
            title: "Cleared!",
            text: "All form data has been cleared.",
            timer: 1500,
            showConfirmButton: false,
        });
    });
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

const logoutBtn = document.getElementById("logout-btn");

if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
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
}

document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logout-btns");
    const logoutForm = document.getElementById("logout-form");

    if (!logoutBtn || !logoutForm) return;

    logoutBtn.addEventListener("click", function (e) {
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
                logoutForm.submit();
            }
        });
    });
});

document.addEventListener("input", function (e) {
    if (!e.target.classList.contains("form-control")) return;

    if (e.target.classList.contains("is-invalid")) {
        e.target.classList.remove("is-invalid");

        const errorFeedback = e.target
            .closest(".col-md-6, .col-md-12")
            ?.querySelector(".invalid-feedback");

        if (errorFeedback) {
            errorFeedback.remove();
        }
    }
});

document.addEventListener("change", function (e) {
    if (!e.target.classList.contains("form-control")) return;

    if (e.target.classList.contains("is-invalid")) {
        e.target.classList.remove("is-invalid");

        const errorFeedback = e.target
            .closest(".col-md-6, .col-md-12")
            ?.querySelector(".invalid-feedback");

        if (errorFeedback) {
            errorFeedback.remove();
        }
    }
});

document.addEventListener("input", handleValidation);
document.addEventListener("change", handleValidation);

function handleValidation(e) {
    if (
        !e.target.classList.contains("form-control") &&
        !e.target.classList.contains("form-select")
    ) {
        return;
    }

    if (e.target.value.trim() !== "") {
        e.target.classList.remove("is-invalid");

        const errorMessage = e.target
            .closest(".col-md-6, .col-md-12")
            ?.querySelector(".text-danger");

        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

function initAutocomplete() {
    const input = document.getElementById("address");
    if (!input) return;

    const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ["geocode"],
    });

    autocomplete.addListener("place_changed", function () {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            console.warn("No details available for input");
            return;
        }

        const latitude = place.geometry.location.lat();
        const longitude = place.geometry.location.lng();

        document.getElementById("latitude").value = latitude;
        document.getElementById("longitude").value = longitude;
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const phoneInput =
        document.querySelector("#phone") ||
        document.querySelector("#phone_number") ||
        document.querySelector("#customer_contact") ||
        document.querySelector("#phone_number1");
    const countryInput =
        document.querySelector("#country_code") ||
        document.querySelector("#country_codes") ||
        document.querySelector("#report_country_code") ||
        document.querySelector("#country_code1");
    const countryIsoInput =
        document.querySelector("#country_iso") ||
        document.querySelector("#country_isos") ||
        document.querySelector("#report_country_iso") ||
        document.querySelector("#country_iso1");

    if (!phoneInput || !countryInput) return;

    const iti = window.intlTelInput(phoneInput, {
        separateDialCode: true,
        initialCountry: "au",
    });

    function syncCountryCode() {
        const data = iti.getSelectedCountryData();
        if (data?.dialCode) {
            countryInput.value = "+" + data.dialCode;
        }
    }

    if (countryIsoInput?.value) {
        iti.setCountry(countryIsoInput.value);
    }

    syncCountryCode();

    phoneInput.addEventListener("countrychange", syncCountryCode);
    phoneInput.addEventListener("input", syncCountryCode);
});

document.addEventListener("DOMContentLoaded", function () {
    const textInput = document.getElementById("floorPlanUpload");
    const fileInput = document.getElementById("fileUpload");

    if (!textInput || !fileInput) return;

    textInput.addEventListener("click", function () {
        fileInput.click();
    });

    fileInput.addEventListener("change", function () {
        if (this.files.length > 0) {
            textInput.value = Array.from(this.files)
                .map((file) => file.name)
                .join(", ");
        }
    });
});

$("#assigned_to_service_provider_status").on("change", function () {
    if ($(this).val() === "yes") {
        $("#service_provider_wrapper").slideDown();
    } else {
        $("#service_provider_wrapper").slideUp();
        $('select[name="service_provider"]').val("");
    }
});

function loadServiceProviders(propertyId, serviceProviderSelect = null) {
    let selectedProvider =
        serviceProviderSelect || $("#selected_service_provider").val();
    let baseUrl = $('meta[name="base-url"]').attr("content");

    $("#service_provider_select").html('<option value="">Loading...</option>');

    if (!propertyId) {
        $("#service_provider_wrapper").hide();
        return;
    }

    $.ajax({
        url: baseUrl + "/admin/service-providers/by-property",
        type: "GET",
        data: {
            property_id: propertyId,
        },
        success: function (response) {
            let options = '<option value="">Select Service Provider</option>';

            if (response.length > 0) {
                response.forEach(function (provider) {
                    let selected =
                        provider.id == selectedProvider ? "selected" : "";

                    options += `<option value="${provider.id}" ${selected}>
                        ${provider.company_name} (${provider.distance.toFixed(2)} KM)
                    </option>`;
                });

                $("#service_provider_wrapper").slideDown();
            } else {
                options +=
                    '<option value="">No providers in coverage area</option>';
                $("#service_provider_wrapper").slideDown();
            }

            $("#service_provider_select").html(options);
        },
    });
}

$(document).ready(function () {
    $("#property_select").on("change", function () {
        loadServiceProviders($(this).val());
    });

    let propertyId = $("#property_select").val();
    let serviceProviderSelect = $("#service_provider_select").val();

    if (propertyId) {
        loadServiceProviders(propertyId, serviceProviderSelect);
    }
});
