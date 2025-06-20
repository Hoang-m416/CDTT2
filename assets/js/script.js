// document.addEventListener('DOMContentLoaded', () => {

//     let searchIcon = document.querySelector('#search-icon');
//     let search = document.querySelector('.search-box');
//     let navbar = document.querySelector('.navbar');
//     let header = document.querySelector('.header');
//     const registerLink = document.querySelector('.register-link');
//     const loginLink = document.querySelector('.login-link');
//     const wrapper = document.querySelector('.wrapper');
//     const menuToggle = document.querySelector('.menu-toggle');
//     const sideMenu = document.getElementById('sideMenu');
//     const overlay = document.getElementById('menuOverlay');
//     const breadcrumb = document.getElementById("breadcrumb");
//     const userAuthButton = document.querySelector('.user-auth');
//     const authLinks = document.querySelector('.auth-links');
//     const urlParams = new URLSearchParams(window.location.search);
//     const tab = urlParams.get('tab');
//     const prevBtn = document.querySelector('.pagination .prev');
//     const nextBtn = document.querySelector('.pagination .next');
//     const pageButtons = document.querySelectorAll('.pagination .page-number');
//     let currentPage = 1;
//     const totalPages = pageButtons.length;

//     const openPopup = document.getElementById("open-shipping-popup");
//     const closePopup = document.getElementById("close-shipping-popup");
//     const popup = document.getElementById("shipping-popup");

//     const productId = parseInt(urlParams.get('id'), 10);

//     fetch('assets/data/products.json')
//         .then(response => response.json())
//         .then(products => {
//             if (productId && products[productId]) {
//                 const product = products[productId];
//                 document.getElementById('product-title').innerText = product.name;
//                 document.getElementById('new-price').innerText = `${product.price} đ`;
//                 document.getElementById('old-price').innerText = `${product.oldPrice} đ`;
//                 document.getElementById('product-rating').innerText = product.rating;
//                 document.getElementById('product-reviews').innerText = `${product.reviews} đánh giá`;
//                 document.getElementById('product-description').innerText = product.description;
//                 document.getElementById('main-image').src = product.image;
//             } else {
//                 document.getElementById('product-title').innerText = 'Sản phẩm không tồn tại';
//                 alert("Sản phẩm không tồn tại.");
//             }
//         })
//         .catch(error => {
//             console.error('Lỗi khi tải sản phẩm:', error);
//             alert("Không thể tải thông tin sản phẩm.");
//         });

//     openPopup.addEventListener("click", function() {
//         popup.classList.remove("popup-hidden");
//     });

//     closePopup.addEventListener("click", function() {
//         popup.classList.add("popup-hidden");
//     });


//     if (searchIcon && search) {
//         searchIcon.onclick = () => {
//             search.classList.toggle('active');
//         };
//     }

//     if (navbar) {
//         document.querySelector('#menu-icon').addEventListener('click', () => {
//             navbar.classList.toggle('active');
//             search.classList.remove('active');
//         });

//         window.onscroll = () => {
//             navbar.classList.remove('active');
//             search.classList.remove('active');
//         };
//     }
//     if (header) {
//         window.addEventListener('scroll', () => {
//             header.classList.toggle('shadow', window.scrollY > 0);
//         });
//     }
//     if (registerLink && loginLink && wrapper) {
//         registerLink.onclick = () => {
//             wrapper.classList.add('active');

//         };
//         loginLink.onclick = () => {
//             wrapper.classList.remove('active');
//         };
//     }
//     if (menuToggle && sideMenu && overlay) {
//         menuToggle.addEventListener('click', (e) => {
//             e.preventDefault();
//             sideMenu.classList.toggle('active');
//             overlay.classList.toggle('active');
//         });

//         overlay.addEventListener('click', () => {
//             sideMenu.classList.remove('active');
//             overlay.classList.remove('active');
//         });
//     }
//     if (breadcrumb) {
//         const urlParams = new URLSearchParams(window.location.search);
//         const sku = urlParams.get("sku");

//         if (sku) {
//             breadcrumb.innerHTML = `
//                 <a href="index.html">Trang chủ</a>
//                 <span>></span>
//                 <a href="productsitem.html">Tất cả sản phẩm</a>
//                 <span>></span>
//                 <span>Chi tiết sản phẩm</span>
//             `;
//         } else {
//             breadcrumb.innerHTML = `
//                 <a href="index.html">Trang chủ</a>
//                 <span>></span>
//                 <a href="productsitem.html">Tất cả sản phẩm</a>
//             `;
//         }
//     } else {
//         console.warn("Sort options element not found!");
//     }
//     userAuthButton.addEventListener('click', (e) => {
//         e.stopPropagation();
//         authLinks.classList.toggle('active');
//     });

//     document.addEventListener('click', (e) => {
//         if (!userAuthButton.contains(e.target) && !authLinks.contains(e.target)) {
//             authLinks.classList.remove('active');
//         }
//     });

//     authLinks.querySelectorAll('a').forEach(link => {
//         link.addEventListener('click', () => {
//             authLinks.classList.remove('active');
//         });
//     });

//     if (tab === 'register') {
//         wrapper.classList.add('active');
//     } else {
//         wrapper.classList.remove('active');
//     }

//     function updateActivePage(newPage) {
//         if (newPage < 1 || newPage > totalPages) return;
//         currentPage = newPage;

//         pageButtons.forEach(btn => {
//             btn.classList.toggle('active', Number(btn.textContent) === currentPage);
//         });

//         prevBtn.disabled = currentPage === 1;
//         nextBtn.disabled = currentPage === totalPages;
//     }

//     pageButtons.forEach(btn => {
//         btn.addEventListener('click', () => {
//             const page = Number(btn.textContent);
//             updateActivePage(page);
//         });
//     });

//     prevBtn.addEventListener('click', () => {
//         updateActivePage(currentPage - 1);
//     });

//     nextBtn.addEventListener('click', () => {
//         updateActivePage(currentPage + 1);
//     });

//     updateActivePage(currentPage);
// });
// const productsList = document.querySelector(".products-list");
// document.getElementById("sort-options").addEventListener("change", function() {
//     const sortSelect = document.getElementById("sort-options");
//     if (sortSelect) {
//         sortSelect.addEventListener("change", function() {
//             const sortValue = this.value;
//             const productsList = document.querySelector(".products-list");
//             if (!productsList) {
//                 console.warn("Products list element not found!");
//                 return;
//             }
//             let productItems = [...productsList.querySelectorAll(".product-item")];
//             const originalOrder = [...productItems];

//             if (sortValue === "default") {
//                 productItems = originalOrder;
//             } else if (sortValue === "name-asc") {
//                 productItems.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
//             } else if (sortValue === "name-desc") {
//                 productItems.sort((a, b) => b.dataset.name.localeCompare(b.dataset.name));
//             } else if (sortValue === "price-asc") {
//                 productItems.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
//             } else if (sortValue === "price-desc") {
//                 productItems.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
//             } else if (sortValue === "rating-desc") {
//                 productItems.sort((a, b) => parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating));
//             } else if (sortValue === "rating-asc") {
//                 productItems.sort((a, b) => parseFloat(a.dataset.rating) - parseFloat(b.dataset.rating));
//             } else if (sortValue === "discount-desc") {
//                 productItems.sort((a, b) => parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount));
//             }

//             productsList.innerHTML = "";
//             productItems.forEach(item => productsList.appendChild(item));
//         });
//     } else {
//         console.warn("Sort options element not found!");
//     }
// });
// const allProducts = document.querySelectorAll('.product-item');
// const productsPerPage = 16;
// let currentPage = 1;

// function displayProducts(page) {
//     const start = (page - 1) * productsPerPage;
//     const end = start + productsPerPage;

//     allProducts.forEach((product, index) => {
//         if (index >= start && index < end) {
//             product.style.display = 'block';
//         } else {
//             product.style.display = 'none';
//         }
//     });
// }

// function setupPagination() {
//     const pageCount = Math.ceil(allProducts.length / productsPerPage);
//     const paginationContainer = document.getElementById('pagination');
//     paginationContainer.innerHTML = '';


//     const prevBtn = document.createElement('button');
//     prevBtn.textContent = '« Trước';
//     prevBtn.classList.add('prev');
//     prevBtn.disabled = currentPage === 1;
//     prevBtn.addEventListener('click', () => {
//         if (currentPage > 1) {
//             currentPage--;
//             displayProducts(currentPage);
//             setupPagination();
//         }
//     });
//     paginationContainer.appendChild(prevBtn);


//     for (let i = 1; i <= pageCount; i++) {
//         const pageBtn = document.createElement('button');
//         pageBtn.textContent = i;
//         pageBtn.classList.add('page-number');
//         if (i === currentPage) pageBtn.classList.add('active');

//         pageBtn.addEventListener('click', () => {
//             currentPage = i;
//             displayProducts(currentPage);
//             setupPagination();
//         });

//         paginationContainer.appendChild(pageBtn);
//     }

//     const nextBtn = document.createElement('button');
//     nextBtn.textContent = 'Sau »';
//     nextBtn.classList.add('next');
//     nextBtn.disabled = currentPage === pageCount;
//     nextBtn.addEventListener('click', () => {
//         if (currentPage < pageCount) {
//             currentPage++;
//             displayProducts(currentPage);
//             setupPagination();
//         }
//     });
//     paginationContainer.appendChild(nextBtn);
// }



// displayProducts(currentPage);
// setupPagination();

// function updateQuantity(change) {
//     const input = document.getElementById("quantity");
//     let current = parseInt(input.value);
//     const min = parseInt(input.min);
//     const max = parseInt(input.max);

//     current += change;
//     if (current < min) current = min;
//     if (current > max) current = max;

//     input.value = current;
// }

// function scrollSlider(direction) {
//     const slider = document.getElementById("other");
//     const scrollAmount = 220;
//     slider.scrollBy({
//         left: direction * scrollAmount,
//         behavior: "smooth"
//     });
// }