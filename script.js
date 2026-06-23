
function openPopup(title, message) {
  document.getElementById("popupTitle").textContent = title;

  // Paparan senarai untuk kategori tertentu
  if (message === "Rugby Football Court Booking") {
    document.getElementById("popupContent").innerHTML = `
      <ul style="list-style-type:none; padding:0; font-size:16px;">
        <li>🏉 Rugby</li>
        <li>⚽ Football</li>
        <li>🏐 Volleyball</li>
        <li>🏸 Badminton</li>
        <li>🎾 Tennis</li>
      </ul>
    `;
  } else if (message === "Recket Equipment Booking") {
    document.getElementById("popupContent").innerHTML = `
      <ul style="list-style-type:none; padding:0; font-size:16px;">
        <li>🏸 Racket</li>
        <li>🏓 Table Tennis Bat</li>
        <li>🏀 Basketball</li>
        <li>🏐 Volleyball Net</li>
        <li>⚽ Football Net</li>
      </ul>
    `;
  } else {
    // Default mesej biasa
    document.getElementById("popupContent").innerHTML =
      "<p>" + message + "</p>";
  }

  document.getElementById("popupOverlay").classList.add("show");
}


function openCategoriesPopup(img1, img2) {
  document.getElementById("popupTitle").textContent = "Categories";
  document.getElementById("popupContent").innerHTML = `
    <div class="category-options">
      <button type="button" class="category-card" onclick="openPopup('Court Booking With Equipment', 'Recket Equipment Booking')">
        <img src="${img2}" alt="Court booking with equipment">
        <p style="margin-top:8px;">Court booking with equipment</p>
      </button>
      <button type="button" class="category-card" onclick="openPopup('Court Booking Court Only', 'Rugby Football Court Booking')">
        <img src="${img1}" alt="Court booking court only">
        <p style="margin-top:8px;">Court booking court only</p>
      </button>
    </div>
  `;
  document.getElementById("popupOverlay").classList.add("show");
}


function closePopup() {
  document.getElementById("popupOverlay").classList.remove("show");
}
