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
      const previewContainer = document.getElementById("preview-container");

      const newFiles = Array.from(event.target.files).filter((f) =>
        f.type.startsWith("image/")
      );

      newFiles.forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const previewBox = document.createElement("div");
          previewBox.classList.add("mb-2", "preview-image");

          const wrapper = document.createElement("div");
          wrapper.classList.add("position-relative", "m-2", "img-thumbnail");

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

            document.getElementById("floorPlanUpload").value = Array.from(
              dt.files
            )
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
    `#${inputId} + .cursor-pointer .input-group-text i`
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

sidebarToggleBtn.addEventListener("click", toggleSidebar);
sidebarBackdrop.addEventListener("click", toggleSidebar);
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
    "#issueReportForm, #houseOwnerForm, #propertiesForm, #housePlan, #applianceForm"
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
