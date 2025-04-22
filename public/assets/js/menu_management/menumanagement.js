$(document).ready(function () {
    var menuId = localStorage.getItem("menu_id");

    if (menuId) {
        $("#menu_name").val(menuId).change();
    }

    $("#menu_name").on("change", function () {
        var selectedMenuId = $(this).val();
        localStorage.setItem("menu_id", selectedMenuId);
        menuTable();
    });
});

//custom menu
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".add-custom-menu").addEventListener("click", function () {
        let urlInput = document.querySelector("#customUrl");
        let labelInput = document.querySelector("#customLabel");
        let menuContainer = document.getElementById("simple-list"); // Get the container

        let url = urlInput.value.trim();
        let label = labelInput.value.trim();

        if (url === "" || label === "") {
            showToast('error', "Both URL and Label fields are required.");
            return;
        }

        if (!isValidUrl(url)) {
            showToast('error', "Enter a valid URL.");
            return;
        }

        // Check if the menu item already exists
        if (isMenuItemExists(label, url)) {
            showToast('error', "This menu item already exists.");
            return;
        }

        let uniqueId = `menu-${Date.now()}`;

        let newItem = `
        <li class="list-group-item" data-title="${label}" data-link="${url}"> <!-- Added data-title and data-link -->
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-${uniqueId}" aria-expanded="false" aria-controls="collapse-${uniqueId}">
                            <span class="me-2"><i class="ti ti-grid-dots"></i></span>${label}
                        </button>
                    </h2>
                    <div id="collapse-${uniqueId}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <!-- Menu Name Field -->
                            <div class="mb-3">
                                <label for="menu_name_${uniqueId}" class="form-label">Menu <span class="text-danger">*</span></label>
                                <input type="text" id="menu_name" name="menu_name" class="form-control" value="${label}" required>
                                <span class="error-message text-danger d-none">Menu name is required.</span>
                            </div>

                            <!-- Permalink Field -->
                            <div class="mb-2">
                                <label for="menu_link_${uniqueId}" class="form-label">Permalink</label>
                                <input type="text" id="menu_link" name="menu_link" class="form-control" value="${url}">
                                <span class="error-message text-danger d-none">Please enter a valid link.</span>
                            </div>

                            <!-- Preview Link -->
                            <p>Preview : <a href="${url}" target="_blank" class="text-info">${url}</a></p>

                            <!-- Status Toggle -->
                            <div class="form-check form-check-md form-switch me-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="menu_status" name="menu_status" checked>
                                <label for="menu_status_${uniqueId}" class="form-check-label form-label mt-0 mb-0">
                                    Status
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>`;

        menuContainer.insertAdjacentHTML("beforeend", newItem);

        // Show success message
        showToast('success', "Custom menu added successfully.");

        // Reset fields
        urlInput.value = "http://";
        labelInput.value = "";
    });

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    function isMenuItemExists(title, link) {
        const existingItems = document.querySelectorAll("#simple-list li.list-group-item");

        for (const item of existingItems) {
            const existingTitle = item.dataset.title ? item.dataset.title.trim().toLowerCase() : "";
            const existingLink = item.dataset.link ? item.dataset.link.trim().toLowerCase() : "";

            if (existingTitle === title.toLowerCase() || existingLink === link.toLowerCase()) {
                return true;
            }
        }
        return false;
    }
});

//select menu
document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all");
    const checkboxes = document.querySelectorAll(".page-checkbox");

    selectAllCheckbox.addEventListener("change", function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all");
    const checkboxes = document.querySelectorAll(".page-checkbox");
    const addToMenuButton = document.getElementById("add-to-menu");
    const menuContainer = document.getElementById("simple-list");

    const BASE_URL = window.location.origin;

    selectAllCheckbox.addEventListener("change", function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    addToMenuButton.addEventListener("click", function () {
        let added = false;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const title = checkbox.dataset.title.trim();
                console.log(title);
                let link = checkbox.dataset.link.trim();
                link = `${BASE_URL}/${link.replace(/^\/+/, '')}`;

                // Validate title and link
                if (!title) {
                    showToast('error', "Menu title is required.");
                    return;
                }

                if (!isValidUrl(link)) {
                    showToast('error', `Invalid URL: ${link}`);
                    return;
                }
                console.log(isMenuItemExists(title, link));
                // Check if this item already exists in the menu
                if (isMenuItemExists(title, link) === true) {
                    showToast('error', `The menu item "${title}" is already added.`);
                    return;
                }

                const uniqueId = `menu-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

                // Create new menu item
                const newItem = `
                    <li class="list-group-item" data-title="${title}" data-link="${link}">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-${uniqueId}" aria-expanded="true" aria-controls="collapse-${uniqueId}">
                                        <span class="me-2"><i class="ti ti-grid-dots"></i></span>${title}
                                    </button>
                                </h2>
                                <div id="collapse-${uniqueId}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <!-- Menu Name Field -->
                                        <div class="mb-3">
                                            <input type="hidden" name="debug_title" value="${title}">
                                            <input type="hidden" name="debug_link" value="${link}">
                                            <label for="menu_name_${uniqueId}" class="form-label">Menu <span class="text-danger">*</span></label>
                                            <input type="text" id="menu_name_${uniqueId}" name="menu_name" class="form-control" value="${title}" required>
                                            <span class="error-message text-danger d-none">Menu name is required.</span>
                                        </div>

                                        <!-- Permalink Field -->
                                        <div class="mb-2">
                                            <label for="menu_link_${uniqueId}" class="form-label">Permalink</label>
                                            <input type="text" id="menu_link_${uniqueId}" name="menu_link" class="form-control" value="${link}">
                                            <span class="error-message text-danger d-none">Please enter a valid link.</span>
                                        </div>

                                        <!-- Preview Link -->
                                        <p>Preview : <a href="${link}" target="_blank" class="text-info">${link}</a></p>

                                        <!-- Status Toggle -->
                                        <div class="form-check form-check-md form-switch me-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="menu_status_${uniqueId}" name="menu_status" checked>
                                            <label for="menu_status_${uniqueId}" class="form-check-label form-label mt-0 mb-0">
                                                Status
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>`;

                // Append the new item to the menu container
                menuContainer.insertAdjacentHTML("beforeend", newItem);
                added = true;
            }
        });

        if (added) {
            showToast('success', "Selected pages added to the menu.");
        }
        //  else {
        //     showToast('error', "No pages selected or all items were already added.");
        // }

        // Uncheck all checkboxes after adding to menu
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        selectAllCheckbox.checked = false;
    });

    // Function to check if a menu item already exists
    function isMenuItemExists(title, link) {
        const existingItems = menuContainer.querySelectorAll("li.list-group-item");

        for (const item of existingItems) {

            const existingTitle = item.dataset.title ? item.dataset.title.trim().toLowerCase() : "";
            const existingLink = item.dataset.link ? item.dataset.link.trim().toLowerCase() : "";


            if (existingTitle === title.toLowerCase() || existingLink === link.toLowerCase()) {
                return true;
            }
        }

        return false;
    }

    // Function to validate URLs
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
});


//update
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#menuManagement").addEventListener("submit", function (event) {
        event.preventDefault();

        let menuData = [];

        let menuId = localStorage.getItem("menu_id");

        document.querySelectorAll(".list-group-item").forEach(item => {
            let labelInput = item.querySelector("[id^='menu_name']");
            let linkInput = item.querySelector("[id^='menu_link']");
            let statusInput = item.querySelector("[id^='menu_status']");

            if (labelInput && linkInput && statusInput) {
                let label = labelInput.value.trim();
                let link = linkInput.value.trim();
                let status = statusInput.checked;

                menuData.push({
                    label: label,
                    link: link,
                    status: status
                });
            }
        });

        let requestData = {
            menu_id: menuId,
            menu_items: menuData
        };

        let jsonData = JSON.stringify(requestData, null, 2);
        console.log(jsonData);

        fetch("/admin/menu-management/update", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: jsonData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message || "Menu updated successfully");
                menuTable();
            } else {
                showToast('success', data.message || "Something went wrong");
                menuTable();

            }
        })
        .catch(error => {
            console.error("Error:", error);
            showToast('error', "Failed to update menu. Please try again.");
        });

    });

});

//list
menuTable();
function menuTable() {
    let menuId = localStorage.getItem('menu_id');

    if (menuId) {
        $.ajax({
            url: `/admin/menus/list?id=${menuId}`,
            type: "GET",
            success: function (response) {
                let menuList = $("#simple-list");
                menuList.empty();

                if (response.code === 200 && response.data) {
                    let menu = response.data;

                    // Check if menu.menus is null or empty
                    if (!menu.menus || menu.menus.trim() === "") {
                        menuList.html(`<li class="list-group-item text-center text-muted">No data found</li>`);
                        return;
                    }

                    try {
                        let menuItems = JSON.parse(menu.menus);

                        menuItems.forEach(item => {
                            let uniqueId = `menu-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

                            let newItem = `
                                <li class="list-group-item" data-title="${item.label}" data-link="${item.link}">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-${uniqueId}" aria-expanded="false" aria-controls="collapse-${uniqueId}">
                                                    <span class="me-2"><i class="ti ti-grid-dots"></i></span>${item.label}
                                                </button>
                                            </h2>
                                            <div id="collapse-${uniqueId}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <!-- Menu Name Field -->
                                                    <div class="mb-3">
                                                        <label for="menu_name_${uniqueId}" class="form-label">Menu <span class="text-danger">*</span></label>
                                                        <input type="text" id="menu_name_${uniqueId}" name="menu_name" class="form-control" value="${item.label}" required>
                                                        <span class="error-message text-danger d-none">Menu name is required.</span>
                                                    </div>

                                                    <!-- Permalink Field -->
                                                    <div class="mb-2">
                                                        <label for="menu_link_${uniqueId}" class="form-label">Permalink</label>
                                                        <input type="text" id="menu_link" name="menu_link" class="form-control" value="${item.link}">
                                                        <span class="error-message text-danger d-none">Please enter a valid link.</span>
                                                    </div>

                                                    <!-- Preview Link -->
                                                    <p>Preview : <a href="${item.link}" target="_blank" class="text-info">${item.link}</a></p>

                                                    <!-- Status Toggle -->
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="menu_status" name="menu_status" ${item.status ? 'checked' : ''}>
                                                        <label for="menu_status_${uniqueId}" class="form-check-label form-label mt-0 mb-0">
                                                            Status
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>`;

                            menuList.append(newItem); // Append the item to the list
                        });
                    } catch (error) {
                        console.error("Error parsing menu JSON:", error);
                        menuList.html(`<li class="list-group-item text-center text-danger">Error loading menu data</li>`);
                    }
                } else {
                    menuList.html(`<li class="list-group-item text-center text-muted">No data found</li>`);
                }
            },
            error: function (error) {
                console.error("Error fetching menus:", error);
                $("#simple-list").html(`<li class="list-group-item text-center text-danger">Failed to load menus</li>`);
            },
            complete: function() {
                $(".table-loader, .input-loader, .label-loader").hide();
                $('.real-table, .real-label, .real-input').removeClass('d-none');
            },
        });
    } else {
        console.log("No menu_id found in localStorage");
        $("#simple-list").html(`<li class="list-group-item text-center text-muted">No menu selected</li>`);
    }
}


