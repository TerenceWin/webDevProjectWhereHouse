document.addEventListener("DOMContentLoaded", function () {
    const createSectionBtn = document.getElementById("createSectionBtn");
    const sectionInput = document.getElementById("sectionName");
    const sectionList = document.querySelector(".section-list");
    const warehouseId = document
        .querySelector('meta[name="warehouse-id"]')
        .getAttribute("content");

    const createProductBtn = document.getElementById("createProductBtn");
    const productNameInput = document.getElementById("productName");
    const productSkuInput = document.getElementById("productSku");
    const productQuantityInput = document.getElementById("productQuantity");
    const productSectionIdInput = document.getElementById("productSectionId");

    // Get CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // ============ SECTION FUNCTIONS ============

    // Function to create and append a section container with products
    function addSectionBox(section) {
        const sectionContainer = document.createElement("div");
        sectionContainer.classList.add("section-container");
        sectionContainer.dataset.sectionId = section.id;

        // Section header
        const sectionHeader = document.createElement("div");
        sectionHeader.classList.add("section-header");

        const sectionName = document.createElement("h3");
        sectionName.classList.add("section-name");
        sectionName.textContent = section.section_name;

        const sectionActions = document.createElement("div");
        sectionActions.classList.add("section-actions");

        // Add product button
        const addProductBtn = document.createElement("button");
        addProductBtn.classList.add("btn-add-product");
        addProductBtn.textContent = "+ Add Product";
        addProductBtn.dataset.sectionId = section.id;
        addProductBtn.setAttribute("data-bs-toggle", "modal");
        addProductBtn.setAttribute("data-bs-target", "#createProductModal");

        // Delete section button
        const deleteBtn = document.createElement("button");
        deleteBtn.classList.add("delete-section-btn");
        deleteBtn.textContent = "×";
        deleteBtn.dataset.sectionId = section.id;

        sectionActions.appendChild(addProductBtn);
        sectionActions.appendChild(deleteBtn);
        sectionHeader.appendChild(sectionName);
        sectionHeader.appendChild(sectionActions);

        // Product list container
        const productList = document.createElement("div");
        productList.classList.add("product-list");
        productList.dataset.sectionId = section.id;

        sectionContainer.appendChild(sectionHeader);
        sectionContainer.appendChild(productList);
        sectionList.appendChild(sectionContainer);

        // Load products for this section
        loadProducts(section.id);
    }

    // Load existing sections
    function loadSections() {
        fetch(`/warehouses/${warehouseId}/sections`)
            .then((response) => response.json())
            .then((data) => {
                if (data.sections) {
                    sectionList.innerHTML = "";
                    data.sections.forEach((section) => {
                        addSectionBox(section);
                    });
                }
            })
            .catch((error) => {
                console.error("Error loading sections:", error);
            });
    }

    // Create new section
    createSectionBtn.addEventListener("click", function () {
        const sectionName = sectionInput.value.trim();
        if (!sectionName) {
            alert("Please enter a section name.");
            return;
        }

        fetch(`/warehouses/${warehouseId}/sections`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ section_name: sectionName }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("createSectionModal")
                    );
                    modal.hide();
                    sectionInput.value = "";
                    addSectionBox(data.section);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error creating section");
            });
    });

    // Delete section (event delegation)
    sectionList.addEventListener("click", function (event) {
        if (
            event.target &&
            event.target.classList.contains("delete-section-btn")
        ) {
            const deleteBtn = event.target;
            const sectionId = deleteBtn.dataset.sectionId;

            if (
                !confirm(
                    "Are you sure you want to delete this section and all its products?"
                )
            )
                return;

            deleteBtn.closest(".section-container").remove();

            fetch(`/warehouses/${warehouseId}/sections/${sectionId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        alert("Error deleting section");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error deleting section");
                });
        }
    });

    // ============ PRODUCT FUNCTIONS ============

    // Function to create and append a product item
    function addProductItem(product, sectionId) {
        const productList = document.querySelector(
            `.product-list[data-section-id="${sectionId}"]`
        );
        if (!productList) return;

        const productItem = document.createElement("div");
        productItem.classList.add("product-item");
        productItem.dataset.productId = product.id;

        const productInfo = document.createElement("div");
        productInfo.classList.add("product-info");

        const productName = document.createElement("span");
        productName.classList.add("product-name");
        productName.textContent = product.product_name;
        productInfo.appendChild(productName);

        if (product.sku) {
            const productSku = document.createElement("span");
            productSku.classList.add("product-sku");
            productSku.textContent = `SKU: ${product.sku}`;
            productInfo.appendChild(productSku);
        }

        const productQuantity = document.createElement("span");
        productQuantity.classList.add("product-quantity");
        productQuantity.textContent = `Qty: ${product.quantity}`;
        productInfo.appendChild(productQuantity);

        const deleteProductBtn = document.createElement("button");
        deleteProductBtn.classList.add("delete-product-btn");
        deleteProductBtn.textContent = "×";
        deleteProductBtn.dataset.sectionId = sectionId;
        deleteProductBtn.dataset.productId = product.id;

        productItem.appendChild(productInfo);
        productItem.appendChild(deleteProductBtn);
        productList.appendChild(productItem);
    }

    // Load products for a section
    function loadProducts(sectionId) {
        fetch(`/warehouses/${warehouseId}/sections/${sectionId}/products`)
            .then((response) => response.json())
            .then((data) => {
                if (data.products) {
                    const productList = document.querySelector(
                        `.product-list[data-section-id="${sectionId}"]`
                    );
                    if (productList) {
                        productList.innerHTML = "";
                        data.products.forEach((product) => {
                            addProductItem(product, sectionId);
                        });
                    }
                }
            })
            .catch((error) => {
                console.error("Error loading products:", error);
            });
    }

    // Set section ID when "Add Product" button is clicked
    sectionList.addEventListener("click", function (event) {
        if (
            event.target &&
            event.target.classList.contains("btn-add-product")
        ) {
            const sectionId = event.target.dataset.sectionId;
            productSectionIdInput.value = sectionId;
        }
    });

    // Create new product
    createProductBtn.addEventListener("click", function () {
        const productName = productNameInput.value.trim();
        const productSku = productSkuInput.value.trim();
        const productQuantity = productQuantityInput.value.trim();
        const sectionId = productSectionIdInput.value;

        if (!productName) {
            alert("Please enter a product name.");
            return;
        }

        if (!sectionId) {
            alert("Section ID is missing.");
            return;
        }

        fetch(`/warehouses/${warehouseId}/sections/${sectionId}/products`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                product_name: productName,
                sku: productSku || null,
                quantity: productQuantity || 0,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("createProductModal")
                    );
                    modal.hide();
                    productNameInput.value = "";
                    productSkuInput.value = "";
                    productQuantityInput.value = "0";
                    addProductItem(data.product, sectionId);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error creating product");
            });
    });

    // Delete product (event delegation)
    sectionList.addEventListener("click", function (event) {
        if (
            event.target &&
            event.target.classList.contains("delete-product-btn")
        ) {
            const deleteBtn = event.target;
            const sectionId = deleteBtn.dataset.sectionId;
            const productId = deleteBtn.dataset.productId;

            if (!confirm("Are you sure you want to delete this product?"))
                return;

            deleteBtn.closest(".product-item").remove();

            fetch(
                `/warehouses/${warehouseId}/sections/${sectionId}/products/${productId}`,
                {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                }
            )
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        alert("Error deleting product");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error deleting product");
                });
        }
    });

    // Initially load sections
    loadSections();
});
