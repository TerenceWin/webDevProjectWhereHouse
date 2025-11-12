document.addEventListener("DOMContentLoaded", function () {
    const warehouseId = document
        .querySelector('meta[name="warehouse-id"]')
        .getAttribute("content");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const gridCanvas = document.getElementById("gridCanvas");
    const sectionsList = document.getElementById("sectionsList");
    const createSectionBtn = document.getElementById("createSectionBtn");
    const sectionNameInput = document.getElementById("sectionNameInput");
    const searchInput = document.getElementById("searchInput");
    const clearSearchBtn = document.getElementById("clearSearchBtn");

    const productNameInput = document.getElementById("productName");
    const productSkuInput = document.getElementById("productSku");
    const productQuantityInput = document.getElementById("productQuantity");
    const productSectionIdInput = document.getElementById("productSectionId");
    const addProductBtn = document.getElementById("addProductBtn");
    const modalSectionName = document.getElementById("modalSectionName");

    let sections = [];
    let creatingSection = false;
    let newSectionName = "";
    let draggedSection = null;
    let currentSearchTerm = ""; // Track current search

    // ============ GRID GENERATION ============

    function generateGrid() {
        gridCanvas.innerHTML = "";
        for (let y = 0; y < 20; y++) {
            for (let x = 0; x < 30; x++) {
                const cell = document.createElement("div");
                cell.classList.add("grid-cell");
                cell.dataset.x = x;
                cell.dataset.y = y;

                cell.addEventListener("click", () => handleCellClick(x, y));
                cell.addEventListener("dragover", (e) => handleDragOver(e));
                cell.addEventListener("drop", (e) => handleDrop(e, x, y));

                gridCanvas.appendChild(cell);
            }
        }
    }

    // ============ SECTION MANAGEMENT ============

    function renderSections() {
        // Clear existing sections from grid
        document.querySelectorAll(".grid-cell").forEach((cell) => {
            cell.classList.remove("occupied");
            cell.textContent = "";
            cell.removeAttribute("draggable");
            cell.dataset.sectionId = "";
        });

        // Place sections on grid
        sections.forEach((section) => {
            if (section.grid_x !== null && section.grid_y !== null) {
                const cell = document.querySelector(
                    `.grid-cell[data-x="${section.grid_x}"][data-y="${section.grid_y}"]`
                );
                if (cell) {
                    cell.classList.add("occupied");
                    cell.textContent = section.section_name;
                    cell.dataset.sectionId = section.id;
                    cell.draggable = true;
                    cell.addEventListener("dragstart", (e) =>
                        handleDragStart(e, section)
                    );
                    cell.addEventListener("dragend", handleDragEnd);
                }
            }
        });

        // Re-apply search highlights if there's an active search
        if (currentSearchTerm) {
            performSearch(currentSearchTerm);
        }
    }

    function loadSections() {
        fetch(`/warehouses/${warehouseId}/sections`)
            .then((response) => response.json())
            .then((data) => {
                if (data.sections) {
                    sections = data.sections;
                    renderSections();
                    renderSidebar();
                }
            })
            .catch((error) => console.error("Error loading sections:", error));
    }

    function renderSidebar() {
        sectionsList.innerHTML = "";

        sections.forEach((section) => {
            const sectionItem = document.createElement("div");
            sectionItem.classList.add("section-item");
            sectionItem.dataset.sectionId = section.id;
            sectionItem.dataset.gridX = section.grid_x;
            sectionItem.dataset.gridY = section.grid_y;

            const header = document.createElement("div");
            header.classList.add("section-item-header");

            const name = document.createElement("span");
            name.classList.add("section-item-name");
            name.textContent = section.section_name;

            const actions = document.createElement("div");
            actions.classList.add("section-item-actions");

            const addBtn = document.createElement("button");
            addBtn.classList.add("btn-add-product");
            addBtn.textContent = "+";
            addBtn.dataset.sectionId = section.id;
            addBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                openProductModal(section.id, section.section_name);
            });

            const deleteBtn = document.createElement("button");
            deleteBtn.classList.add("btn-delete-section");
            deleteBtn.textContent = "×";
            deleteBtn.dataset.sectionId = section.id;
            deleteBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                deleteSection(section.id);
            });

            actions.appendChild(addBtn);
            actions.appendChild(deleteBtn);
            header.appendChild(name);
            header.appendChild(actions);

            const productsContainer = document.createElement("div");
            productsContainer.classList.add("section-item-products");

            // Load products for this section
            loadProductsForSection(section.id, productsContainer);

            sectionItem.appendChild(header);
            sectionItem.appendChild(productsContainer);

            // Click to highlight on grid
            sectionItem.addEventListener("click", () =>
                highlightSectionOnGrid(section.id)
            );

            sectionsList.appendChild(sectionItem);
        });
    }

    function handleCellClick(x, y) {
        if (creatingSection) {
            placeSectionOnGrid(x, y);
        }
    }

    createSectionBtn.addEventListener("click", () => {
        const modal = new bootstrap.Modal(
            document.getElementById("createSectionModal")
        );
        modal.show();
    });

    // FIX #1: Handle modal close properly
    const createSectionModalElement =
        document.getElementById("createSectionModal");

    createSectionModalElement.addEventListener("hidden.bs.modal", () => {
        const name = sectionNameInput.value.trim();

        if (name && !creatingSection) {
            // User entered a name and modal was closed naturally (not cancelled)
            newSectionName = name;
            creatingSection = true;
            alert("Click on an empty grid cell to place the section");
        } else if (creatingSection) {
            // Modal was reopened, don't start a new creation
            return;
        } else {
            // User clicked X or cancel without entering name
            cancelSectionCreation();
        }
    });

    // Handle X button and Cancel button clicks
    createSectionModalElement.addEventListener("click", (e) => {
        if (
            e.target.classList.contains("btn-close") ||
            e.target.classList.contains("btn-secondary")
        ) {
            cancelSectionCreation();
        }
    });

    function cancelSectionCreation() {
        creatingSection = false;
        newSectionName = "";
        sectionNameInput.value = "";
    }

    function placeSectionOnGrid(x, y) {
        if (!creatingSection || !newSectionName) return;

        // Check if cell is occupied
        const cell = document.querySelector(
            `.grid-cell[data-x="${x}"][data-y="${y}"]`
        );
        if (cell.classList.contains("occupied")) {
            alert("This cell is already occupied!");
            return;
        }

        fetch(`/warehouses/${warehouseId}/sections`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                section_name: newSectionName,
                grid_x: x,
                grid_y: y,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    sections.push(data.section);
                    renderSections();
                    renderSidebar();
                    cancelSectionCreation(); // Clean up after placing
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error creating section");
            });
    }

    function deleteSection(sectionId) {
        if (!confirm("Delete this section and all its products?")) return;

        fetch(`/warehouses/${warehouseId}/sections/${sectionId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    sections = sections.filter((s) => s.id != sectionId);
                    renderSections();
                    renderSidebar();
                } else {
                    alert("Error deleting section");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error deleting section");
            });
    }

    // ============ DRAG AND DROP ============

    function handleDragStart(e, section) {
        draggedSection = section;
        e.target.classList.add("dragging");
    }

    function handleDragEnd(e) {
        e.target.classList.remove("dragging");
        document.querySelectorAll(".grid-cell").forEach((cell) => {
            cell.classList.remove("available-drop");
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        const cell = e.target.closest(".grid-cell");
        if (cell && !cell.classList.contains("occupied")) {
            cell.classList.add("available-drop");
        }
    }

    function handleDrop(e, x, y) {
        e.preventDefault();
        if (!draggedSection) return;

        const cell = e.target.closest(".grid-cell");
        if (
            cell.classList.contains("occupied") &&
            cell.dataset.sectionId != draggedSection.id
        ) {
            alert("This cell is already occupied!");
            return;
        }

        updateSectionPosition(draggedSection.id, x, y);
        draggedSection = null;
    }

    function updateSectionPosition(sectionId, x, y) {
        // FIX: Clear highlights before moving
        clearHighlights();

        fetch(`/warehouses/${warehouseId}/sections/${sectionId}/position`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                grid_x: x,
                grid_y: y,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const section = sections.find((s) => s.id == sectionId);
                    if (section) {
                        section.grid_x = x;
                        section.grid_y = y;
                        renderSections();
                        renderSidebar();

                        // FIX: If there was an active search, re-apply it after moving
                        if (currentSearchTerm) {
                            performSearch(currentSearchTerm);
                        }
                    }
                } else {
                    alert("Error updating position");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error updating position");
            });
    }

    // ============ PRODUCT MANAGEMENT ============

    function loadProductsForSection(sectionId, container) {
        fetch(`/warehouses/${warehouseId}/sections/${sectionId}/products`)
            .then((response) => response.json())
            .then((data) => {
                if (data.products) {
                    container.innerHTML = "";
                    data.products.forEach((product) => {
                        addProductToSidebar(product, sectionId, container);
                    });
                }
            })
            .catch((error) => console.error("Error loading products:", error));
    }

    function addProductToSidebar(product, sectionId, container) {
        const productItem = document.createElement("div");
        productItem.classList.add("product-item-small");
        productItem.dataset.productId = product.id;

        const name = document.createElement("span");
        name.classList.add("product-item-name");
        name.textContent = product.product_name;

        const qty = document.createElement("span");
        qty.classList.add("product-item-qty");
        qty.textContent = `Qty: ${product.quantity}`;

        const deleteBtn = document.createElement("button");
        deleteBtn.classList.add("btn-delete-product");
        deleteBtn.textContent = "×";
        deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            deleteProduct(sectionId, product.id);
        });

        productItem.appendChild(name);
        productItem.appendChild(qty);
        productItem.appendChild(deleteBtn);
        container.appendChild(productItem);
    }

    function openProductModal(sectionId, sectionName) {
        productSectionIdInput.value = sectionId;
        modalSectionName.textContent = sectionName;
        const modal = new bootstrap.Modal(
            document.getElementById("createProductModal")
        );
        modal.show();
    }

    addProductBtn.addEventListener("click", () => {
        const name = productNameInput.value.trim();
        const sku = productSkuInput.value.trim();
        const quantity = productQuantityInput.value.trim();
        const sectionId = productSectionIdInput.value;

        if (!name) {
            alert("Please enter a product name");
            return;
        }

        fetch(`/warehouses/${warehouseId}/sections/${sectionId}/products`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                product_name: name,
                sku: sku || null,
                quantity: quantity || 0,
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

                    // Refresh sidebar
                    renderSidebar();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error creating product");
            });
    });

    function deleteProduct(sectionId, productId) {
        if (!confirm("Delete this product?")) return;

        fetch(
            `/warehouses/${warehouseId}/sections/${sectionId}/products/${productId}`,
            {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
            }
        )
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    renderSidebar();
                } else {
                    alert("Error deleting product");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error deleting product");
            });
    }

    // ============ SEARCH FUNCTIONALITY ============

    searchInput.addEventListener("input", function () {
        const searchTerm = searchInput.value.trim().toLowerCase();
        currentSearchTerm = searchTerm; // FIX #3: Track current search

        if (!searchTerm) {
            clearHighlights();
            currentSearchTerm = "";
            return;
        }

        clearHighlights();
        performSearch(searchTerm);
    });

    // FIX #3: Separate search logic into reusable function
    function performSearch(searchTerm) {
        sections.forEach((section) => {
            fetch(`/warehouses/${warehouseId}/sections/${section.id}/products`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.products) {
                        const matchingProducts = data.products.filter(
                            (product) =>
                                product.product_name
                                    .toLowerCase()
                                    .includes(searchTerm) ||
                                (product.sku &&
                                    product.sku
                                        .toLowerCase()
                                        .includes(searchTerm))
                        );

                        if (matchingProducts.length > 0) {
                            highlightSection(section.id);
                        }
                    }
                })
                .catch((error) =>
                    console.error("Error searching products:", error)
                );
        });
    }

    clearSearchBtn.addEventListener("click", function () {
        searchInput.value = "";
        currentSearchTerm = "";
        clearHighlights();
    });

    function highlightSection(sectionId) {
        // FIX #3: Find section by ID to get current position
        const section = sections.find((s) => s.id == sectionId);
        if (!section || section.grid_x === null || section.grid_y === null)
            return;

        // Highlight on grid using current position
        const gridCell = document.querySelector(
            `.grid-cell[data-x="${section.grid_x}"][data-y="${section.grid_y}"]`
        );
        if (gridCell) {
            gridCell.classList.add("highlighted");
        }

        // Highlight in sidebar
        const sidebarItem = document.querySelector(
            `.section-item[data-section-id="${sectionId}"]`
        );
        if (sidebarItem) {
            sidebarItem.classList.add("highlighted");
            sidebarItem.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }

    function highlightSectionOnGrid(sectionId) {
        clearHighlights();
        highlightSection(sectionId);
    }

    function clearHighlights() {
        document.querySelectorAll(".grid-cell.highlighted").forEach((cell) => {
            cell.classList.remove("highlighted");
        });

        document
            .querySelectorAll(".section-item.highlighted")
            .forEach((item) => {
                item.classList.remove("highlighted");
            });
    }

    // ============ INITIALIZATION ============

    generateGrid();
    loadSections();
});
