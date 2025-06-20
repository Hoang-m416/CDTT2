document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id'), 10);

    fetch('assets/data/products.json')
        .then(res => res.json())
        .then(products => {
            if (productId && products[productId]) {
                const product = products[productId];
                document.getElementById('product-title').innerText = product.name;
                document.getElementById('new-price').innerText = `${product.price} đ`;
                document.getElementById('old-price').innerText = `${product.oldPrice} đ`;
                document.getElementById('product-rating').innerText = product.rating;
                document.getElementById('product-reviews').innerText = `${product.reviews} đánh giá`;
                document.getElementById('product-description').innerText = product.description;
                document.getElementById('main-image').src = product.image;
            } else {
                document.getElementById('product-title').innerText = 'Sản phẩm không tồn tại';
                alert("Sản phẩm không tồn tại.");
            }
        })
        .catch(err => {
            console.error("Lỗi khi tải sản phẩm:", err);
            alert("Không thể tải thông tin sản phẩm.");
        });
});