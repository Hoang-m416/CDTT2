document.addEventListener('DOMContentLoaded', () => {
    const sortSelect = document.getElementById("sort-options");
    const productsList = document.querySelector(".products-list");

    if (!sortSelect || !productsList) return;

    sortSelect.addEventListener("change", () => {
        const value = sortSelect.value;
        let items = [...productsList.querySelectorAll(".product-item")];
        const originalOrder = [...items];

        switch (value) {
            case "name-asc":
                items.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
                break;
            case "name-desc":
                items.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
                break;
            case "price-asc":
                items.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
                break;
            case "price-desc":
                items.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
                break;
            case "rating-asc":
                items.sort((a, b) => parseFloat(a.dataset.rating) - parseFloat(b.dataset.rating));
                break;
            case "rating-desc":
                items.sort((a, b) => parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating));
                break;
            case "discount-desc":
                items.sort((a, b) => parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount));
                break;
            default:
                items = originalOrder;
        }

        productsList.innerHTML = "";
        items.forEach(item => productsList.appendChild(item));
    });
});