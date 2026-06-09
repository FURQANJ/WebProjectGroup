function openPopup(title, message) {
  document.getElementById("popupTitle").textContent = title;
  document.getElementById("popupContent").innerHTML = "<p>" + message + "</p>";
  document.getElementById("popupOverlay").classList.add("show");
}

function openCategoriesPopup() {
  document.getElementById("popupTitle").textContent = "Categories";
  document.getElementById("popupContent").innerHTML = `
    <div class="category-options">
      <button type="button" class="category-card" onclick="openPopup('Court Booking With Equipment', 'Add your court booking with equipment content here.')">
        <img src="Group 3.png" alt="Court booking with equipment">
      </button>
      <button type="button" class="category-card" onclick="openPopup('Court Booking Court Only', 'Add your court only booking content here.')">
        <img src="Group 2.png" alt="Court booking court only">
      </button>
    </div>
  `;
  document.getElementById("popupOverlay").classList.add("show");
}

function closePopup() {
  document.getElementById("popupOverlay").classList.remove("show");
}
