document.addEventListener('DOMContentLoaded', () => {
    const allProducts = document.querySelectorAll('.product-item');
    const productsPerPage = 16;
    let currentPage = 1;

    const displayProducts = (page) => {
        const start = (page - 1) * productsPerPage;
        const end = start + productsPerPage;

        allProducts.forEach((product, index) => {
            product.style.display = index >= start && index < end ? 'block' : 'none';
        });
    };

    const setupPagination = () => {
        const pageCount = Math.ceil(allProducts.length / productsPerPage);
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        const createButton = (text, handler, isDisabled = false, className = '') => {
            const btn = document.createElement('button');
            btn.textContent = text;
            btn.disabled = isDisabled;
            btn.className = className;
            btn.addEventListener('click', handler);
            return btn;
        };

        paginationContainer.appendChild(
            createButton('« Trước', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayProducts(currentPage);
                    setupPagination();
                }
            }, currentPage === 1, 'prev')
        );

        for (let i = 1; i <= pageCount; i++) {
            const btn = createButton(i, () => {
                currentPage = i;
                displayProducts(currentPage);
                setupPagination();
            }, false, 'page-number' + (i === currentPage ? ' active' : ''));
            paginationContainer.appendChild(btn);
        }

        paginationContainer.appendChild(
            createButton('Sau »', () => {
                if (currentPage < pageCount) {
                    currentPage++;
                    displayProducts(currentPage);
                    setupPagination();
                }
            }, currentPage === pageCount, 'next')
        );
    };

    displayProducts(currentPage);
    setupPagination();
});