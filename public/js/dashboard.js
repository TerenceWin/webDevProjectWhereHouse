document.addEventListener("DOMContentLoaded", function () {
    const createBtn = document.getElementById("createWarehouseBtn");
    const warehouseInput = document.getElementById("warehouseName");
    const myWarehousesList = document.getElementById("myWarehouses");
    const sharedWarehousesList = document.getElementById("sharedWarehouses");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // Function to create and append a warehouse box with a delete button
    function addWarehouseBox(warehouse, isOwned = true) {
        const newDiv = document.createElement("div");
        newDiv.classList.add("warehouse-icon");
        newDiv.dataset.id = warehouse.id;

        // Add warehouse name to the box using <a> tag for routing
        const warehouseLink = document.createElement("a");
        warehouseLink.classList.add("warehouse-name");
        warehouseLink.href = `/warehouses/${warehouse.id}`;
        warehouseLink.textContent = warehouse.warehouse_name;
        newDiv.appendChild(warehouseLink);

        // Only show delete button for owned warehouses
        if (isOwned) {
            const deleteBtn = document.createElement("button");
            deleteBtn.classList.add("delete-warehouse-btn");
            deleteBtn.textContent = "x";
            deleteBtn.dataset.id = warehouse.id;
            newDiv.appendChild(deleteBtn);
        } else {
            // Add shared indicator
            newDiv.classList.add("shared");
        }

        return newDiv;
    }

    // Load existing warehouses via AJAX
    function loadWarehouses() {
        fetch("/warehouses", {
            headers: {
                Accept: "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.warehouses) {
                    // Clear both lists
                    myWarehousesList.innerHTML = "";
                    sharedWarehousesList.innerHTML = "";

                    // Get current user ID (we'll need to fetch this)
                    fetch("/api/user")
                        .then((res) => res.json())
                        .then((userData) => {
                            const currentUserId = userData.id;

                            // Separate owned vs shared warehouses
                            const ownedWarehouses = [];
                            const sharedWarehouses = [];

                            data.warehouses.forEach((warehouse) => {
                                // Check if user is the creator
                                if (warehouse.user_id === currentUserId) {
                                    ownedWarehouses.push(warehouse);
                                } else {
                                    sharedWarehouses.push(warehouse);
                                }
                            });

                            // Add owned warehouses
                            if (ownedWarehouses.length > 0) {
                                ownedWarehouses.forEach((warehouse) => {
                                    const warehouseBox = addWarehouseBox(
                                        warehouse,
                                        true
                                    );
                                    myWarehousesList.appendChild(warehouseBox);
                                });
                            } else {
                                myWarehousesList.innerHTML =
                                    '<p class="no-warehouses">No warehouses yet. Create one to get started!</p>';
                            }

                            // Add shared warehouses
                            if (sharedWarehouses.length > 0) {
                                sharedWarehouses.forEach((warehouse) => {
                                    const warehouseBox = addWarehouseBox(
                                        warehouse,
                                        false
                                    );
                                    sharedWarehousesList.appendChild(
                                        warehouseBox
                                    );
                                });
                            } else {
                                sharedWarehousesList.innerHTML =
                                    '<p class="no-warehouses">No shared warehouses</p>';
                            }
                        })
                        .catch((error) => {
                            console.error("Error getting user data:", error);
                            // Fallback: show all warehouses in "My Warehouses"
                            data.warehouses.forEach((warehouse) => {
                                const warehouseBox = addWarehouseBox(
                                    warehouse,
                                    true
                                );
                                myWarehousesList.appendChild(warehouseBox);
                            });
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
    myWarehousesList.addEventListener("click", function (event) {
        // Check if clicked element is a delete button
        if (
            event.target &&
            event.target.classList.contains("delete-warehouse-btn")
        ) {
            const deleteBtn = event.target;
            const warehouseId = deleteBtn.dataset.id;

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
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        alert("Error deleting warehouse");
                        loadWarehouses(); // Reload to restore
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error deleting warehouse");
                    loadWarehouses(); // Reload to restore
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

                    // Add new warehouse box to "My Warehouses"
                    const warehouseBox = addWarehouseBox(data.warehouse, true);

                    // Remove "no warehouses" message if it exists
                    const noWarehousesMsg =
                        myWarehousesList.querySelector(".no-warehouses");
                    if (noWarehousesMsg) {
                        noWarehousesMsg.remove();
                    }

                    myWarehousesList.appendChild(warehouseBox);
                    warehouseInput.value = ""; // Clear input
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});
