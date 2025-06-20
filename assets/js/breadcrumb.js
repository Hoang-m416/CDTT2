document.addEventListener('DOMContentLoaded', () => {
    const breadcrumb = document.getElementById("breadcrumb");
    const urlParams = new URLSearchParams(window.location.search);
    const sku = urlParams.get("sku");

    if (breadcrumb) {
        if (sku) {
            breadcrumb.innerHTML = `
                <a href="index.html">Trang chủ</a>
                <span>></span>
                <a href="productsitem.html">Tất cả sản phẩm</a>
                <span>></span>
                <span>Chi tiết sản phẩm</span>
            `;
        } else {
            breadcrumb.innerHTML = `
                <a href="index.html">Trang chủ</a>
                <span>></span>
                <a href="productsitem.html">Tất cả sản phẩm</a>
            `;
        }
    }
});