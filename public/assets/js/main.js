// // add hovered class to selected list item
// let list = document.querySelectorAll(".navigation li");

// function activeLink() {
//   list.forEach((item) => {
//     item.classList.remove("hovered");
//   });
//   this.classList.add("hovered");
// }

// list.forEach((item) => item.addEventListener("mouseover", activeLink));

// // Menu Toggle
// let toggle = document.querySelector(".toggle");
// let navigation = document.querySelector(".navigation");
// let main = document.querySelector(".main");

// toggle.onclick = function () {
//   navigation.classList.toggle("active");
//   main.classList.toggle("active");
// };

document.addEventListener("DOMContentLoaded", function () {
  // Menu Toggle
  const toggle = document.querySelector(".toggle");
  const navigation = document.querySelector(".navigation");
  const main = document.querySelector(".main");

  // FIX: Added check to see if toggle exists before adding event listener
  if (toggle) {
    toggle.addEventListener("click", function () {
      navigation.classList.toggle("active");
      main.classList.toggle("active");
    });
  }

  // Form Validation
  function validateForm() {
    let valid = true;

    // Check each required field
    document.querySelectorAll("input[required]").forEach(function (input) {
      const errorMessageId = input.getAttribute("name") + "-error";
      const errorElement = document.getElementById(errorMessageId);

      if (input.value.trim() === "") {
        valid = false;
        if (errorElement) errorElement.style.display = "block";
      } else {
        if (errorElement) errorElement.style.display = "none";
      }
    });

    return valid;
  }

  // Handle form submission
  const updateBtn = document.querySelector(".updateBtn");

  // FIX: Added check to see if updateBtn exists before adding event listener
  if (updateBtn) {
    updateBtn.addEventListener("click", function (event) {
      if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
      }
    });
  }
});