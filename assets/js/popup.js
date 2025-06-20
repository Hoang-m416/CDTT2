document.addEventListener('DOMContentLoaded', () => {
    const openPopup = document.getElementById("open-shipping-popup");
    const closePopup = document.getElementById("close-shipping-popup");
    const popup = document.getElementById("shipping-popup");

    if (openPopup && closePopup && popup) {
        openPopup.addEventListener("click", () => popup.classList.remove("popup-hidden"));
        closePopup.addEventListener("click", () => popup.classList.add("popup-hidden"));
    }
});