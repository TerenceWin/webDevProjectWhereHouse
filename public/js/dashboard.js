document.addEventListener("DOMContentLoaded", function () {
    const createBtn = document.getElementById("createWarehouseBtn");
    const warehouseInput = document.getElementById("warehouseName");
    const warehouseList = document.querySelector(".warehouse-list");

    // Function to create and append a warehouse box with a delete button
    function addWarehouseBox(warehouse) {
        const newDiv = document.createElement("div");
        newDiv.classList.add("warehouse-icon");
        newDiv.dataset.id = warehouse.id; // Store the warehouse ID in the div

        // Add warehouse name to the box using <a> tag for routing
        const warehouseLink = document.createElement("a");
        warehouseLink.classList.add("warehouse-name");
        warehouseLink.href = `/warehouses/${warehouse.id}`; // Routing to the warehouse page
        warehouseLink.textContent = warehouse.warehouse_name;
        newDiv.appendChild(warehouseLink);

        // Add delete button
        const deleteBtn = document.createElement("button");
        deleteBtn.classList.add("delete-warehouse-btn");
        deleteBtn.textContent = "x"; // Set delete button text
        deleteBtn.dataset.id = warehouse.id; // Ensure the data-id is correct
        newDiv.appendChild(deleteBtn);

        // Append the new warehouse box to the list
        warehouseList.appendChild(newDiv);
    }

    // Load existing warehouses via AJAX when the page loads or after creating a new one
    function loadWarehouses() {
        fetch("/warehouses")
            .then((response) => response.json())
            .then((data) => {
                if (data.warehouses) {
                    warehouseList.innerHTML = ""; // Clear the list before re-rendering
                    data.warehouses.forEach((warehouse) => {
                        addWarehouseBox(warehouse); // Add each warehouse to the list
                    });
                }
            })
            .catch((error) =>
                console.error("Error loading warehouses:", error)
            );
    }

    // Initially load existing warehouses
    loadWarehouses();

    // Event delegation for delete buttons - dynamically handles new and existing delete buttons
    warehouseList.addEventListener("click", function (event) {
        // Check if clicked element is a delete button
        if (
            event.target &&
            event.target.classList.contains("delete-warehouse-btn")
        ) {
            const deleteBtn = event.target;
            const warehouseId = deleteBtn.dataset.id;
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Confirm before deletion
            if (!confirm("Are you sure you want to delete this warehouse?"))
                return;

            // Immediately remove the warehouse box from the UI
            deleteBtn.parentElement.remove();

            // Send delete request to the server
            fetch(`/warehouses/${warehouseId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest", // Add this header
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        alert("Error deleting warehouse");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error deleting warehouse");
                });
        }
    });

    // Create new warehouse
    createBtn.addEventListener("click", function () {
        const warehouseName = warehouseInput.value.trim();
        if (!warehouseName) {
            alert("Please enter a warehouse name.");
            return;
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        // Send request to create new warehouse via AJAX
        fetch("/warehouses", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ warehouse_name: warehouseName }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("createWarehouseModal")
                    );
                    modal.hide();

                    // Add new warehouse box to the list
                    addWarehouseBox(data.warehouse);

                    warehouseInput.value = ""; // Clear input
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});
