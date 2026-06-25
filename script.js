
function openPopup(title, message) {
  document.getElementById("popupTitle").textContent = title;

  document.getElementById("popupOverlay").classList.add("show");
}


function closePopup() {
  document.getElementById("popupOverlay").classList.remove("show");
}
