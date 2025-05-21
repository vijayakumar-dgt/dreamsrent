(async () => {
    
    "use strict";

    await loadTranslationFile("admin", "rentals, common");

    const permissions = await loadUserPermissions();

    let selectedStatus = null;
    let currentSortType = "latest";

    function initTable(filterData) {
        $.ajax({
            url: "/admin/vehicle-list",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(filterData),
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#vehicleListIndex")) {
                    $("#vehicleListIndex").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    function formatDateTime(dateString) {
                        let date = new Date(dateString);

                        let optionsDate = {
                            day: "2-digit",
                            month: "short",
                            year: "numeric",
                        };
                        let formattedDate = date.toLocaleDateString(
                            "en-GB",
                            optionsDate
                        );

                        let optionsTime = {
                            hour: "2-digit",
                            minute: "2-digit",
                            hour12: true,
                        };
                        let formattedTime = date.toLocaleTimeString(
                            "en-US",
                            optionsTime
                        );

                        return { formattedDate, formattedTime };
                    }
                    function ucfirst(str) {
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    }

                    $.each(data, function (index, value) {
                        let priceObj = [];
                        let priceText = "";

                        try {
                            priceObj = JSON.parse(value.vehicle_price);
                            let prices = priceObj[0];

                            for (const [key, val] of Object.entries(prices)) {
                                if (val && val !== "0") {
                                    priceText = `$${val} (${key})`;
                                    break;
                                }
                            }
                        } catch (e) {}

                        tableBody += `<tr>
                            <td>
                                <div class="form-check form-check-md" data-id="${
                                    value.id
                                }">
                                                <input class="form-check-input" type="checkbox">
                                </div>
                            </td>
                            <td>
                            <div class="d-flex align-items-start">
								<div class="avatar me-2 flex-shrink-0">
									<img src="${value.vehicle_image}" class="rounded-3" alt="">
								</div>
								<div class="text-start">
									<h6><p class="fs-14 fw-semibold">${ucfirst(
                                        value.name.length > 14
                                            ? value.name.slice(0, 14) + ".."
                                            : value.name
                                    )}</p></h6>
									<p>${value.car_type ? value.car_type.name : ""}</p>
								</div>
							</div>
                            <h6 class="fw-medium"><a href="#"></a></h6></td>
                            <td>${
                                value.main_location.name.length > 15
                                    ? ucfirst(value.main_location.name).slice(
                                          0,
                                          15
                                      ) + ".."
                                    : ucfirst(value.main_location.name)
                            }</td>
                             <td>${priceText}</td>
                            <td>0${value.damage_count}</td>
                         <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-popular" type="checkbox"
                                    ${value.popular == 1 ? "checked" : ""}
                                    data-id="${value.id}">
                            </div>
                        </td>
                       <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-recommended" type="checkbox"
                                    ${value.recommended == 1 ? "checked" : ""}
                                    data-id="${value.id}">
                            </div>
                        </td>
                        <td class="text-start">${value.created_date}</td>
                          <td>
                            <span class="badge ${
                                value.status == 1
                                    ? "badge-success-transparent"
                                    : "badge-danger-transparent"
                            } d-inline-flex align-items-center badge-sm cursor-pointer"
                                data-id="${value.id}" data-status="${
                            value.status
                        }" data-bs-toggle="modal" data-bs-target="#status-modal">
                                <i class="ti ti-point-filled me-1"></i>
                                ${
                                    value.status == 1
                                        ? _l("admin.common.active")
                                        : _l("admin.common.inactive")
                                }
                            </span>
                        </td>
             ${
                 hasPermission(permissions, "vehicles", "edit") ||
                 hasPermission(permissions, "vehicles", "delete")
                     ? `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                          ${
                                              hasPermission(
                                                  permissions,
                                                  "vehicles",
                                                  "edit"
                                              )
                                                  ? `<li>
                                            <button class="dropdown-item edit-vehicles rounded-1 border-0 bg-white" data-id="${
                                                value.slug
                                            }">
                                                <i class="ti ti-edit me-1"></i>${_l(
                                                    "admin.common.edit"
                                                )}
                                            </button>
                                        </li>`
                                                  : ""
                                          }
                                               ${
                                                   hasPermission(
                                                       permissions,
                                                       "vehicles",
                                                       "delete"
                                                   )
                                                       ? `<li>
                                           <button 
                                                class="dropdown-item border-0 bg-white rounded-1 delete-vehicle" 
                                                data-id="${value.id}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#delete-modal">
                                                <i class="ti ti-trash me-1"></i>${_l(
                                                    "admin.common.delete"
                                                )}
                                            </button>
                                        </li>`
                                                       : ""
                                               }
                                    </ul>
                                </div>
                            </td>`
                     : ""
             }
                        </tr>`;
                    });
                } else {
                    tableBody += `
                            <tr>
                                <td colspan="8" class="text-center">${_l(
                                    "admin.common.empty_table"
                                )}e</td>
                            </tr>`;
                    $(".table-footer").empty();
                }
                $("#vehicleListIndex tbody").html(tableBody);
                if (response.data.length > 0) {
                    $("#vehicleListIndex").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(
                                ".dataTables_wrapper .dataTables_paginate"
                            ).addClass("d-none");
                            var tableWrapper = $(this).closest(
                                ".dataTables_wrapper"
                            );
                            var info = tableWrapper.find(".dataTables_info");
                            var pagination = tableWrapper.find(
                                ".dataTables_paginate"
                            );

                            $(".table-footer")
                                .empty()
                                .append(
                                    $(
                                        '<div class="d-flex justify-content-between align-items-center w-100"></div>'
                                    )
                                        .append(
                                            $(
                                                '<div class="datatable-info"></div>'
                                            ).append(info.clone(true))
                                        )
                                        .append(
                                            $(
                                                '<div class="datatable-pagination"></div>'
                                            ).append(pagination.clone(true))
                                        )
                                );
                            $(".table-footer")
                                .find(".dataTables_paginate")
                                .removeClass("d-none");
                        },
                        language: {
                            emptyTable: _l("admin.common.no_matching_records"),
                            info:
                                _l("admin.common.showing") +
                                " _START_ " +
                                _l("admin.common.to") +
                                " _END_ " +
                                _l("admin.common.of") +
                                " _TOTAL_ " +
                                _l("admin.common.entries"),
                            infoEmpty:
                                _l("admin.common.showing") +
                                " 0 " +
                                _l("admin.common.to") +
                                " 0 " +
                                _l("admin.common.of") +
                                " 0 " +
                                _l("admin.common.entries"),
                            infoFiltered:
                                "(" +
                                _l("admin.common.filtered_from") +
                                " _MAX_ " +
                                _l("admin.common.total_entries") +
                                ")",
                            lengthMenu:
                                _l("admin.common.show") +
                                " _MENU_ " +
                                _l("admin.common.entries"),
                            search: _l("admin.common.search") + ":",
                            zeroRecords: _l("admin.common.empty_table"),
                            paginate: {
                                first: _l("admin.common.first"),
                                last: _l("admin.common.last"),
                                next: _l("admin.common.next"),
                                previous: _l("admin.common.previous"),
                            },
                        },
                    });
                }
                $("#loader-table").hide();
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-input, .real-data").removeClass("d-none");
            },
            error: function (error) {},
        });
    }

    function getFilterData(includeStatus = false) {
        let vehicleIds = [];
        let vehicleTypeIds = [];
        let vehicleLocationIds = [];
        let sortByDate = $("#sort_by_date").val() || null;
        let name = $("#name").val().trim() || null;

        $("input[name='vehicle_id']:checked").each(function () {
            vehicleIds.push($(this).val());
        });

        $("input[name='vehicle_type_id']:checked").each(function () {
            vehicleTypeIds.push($(this).val());
        });

        $("input[name='vehicle_location_id']:checked").each(function () {
            vehicleLocationIds.push($(this).val());
        });

        let filterData = {
            name: name,
            vehicle_id: vehicleIds.length > 0 ? vehicleIds : null,
            vehicle_type_id: vehicleTypeIds.length > 0 ? vehicleTypeIds : null,
            vehicle_location_id:
                vehicleLocationIds.length > 0 ? vehicleLocationIds : null,
            sort_by: currentSortType || null,
            sort_by_date: sortByDate,
        };

        if (includeStatus && selectedStatus !== null) {
            filterData.status = selectedStatus;
        }

        return filterData;
    }

    function fetchFilteredData(includeStatus = false) {
        let filterData = getFilterData(includeStatus);
        initTable(filterData);
        $("#loader-table").show();
        $(".real-data").addClass("d-none");
    }

    function editVechileList(vehicleSlug) {
        $.ajax({
            url: "/admin/check-vehicle",
            type: "GET",
            data: { vehicle_slug: vehicleSlug },
            success: function (response) {
                if (response.exists === "yes") {
                    window.location.href = `/admin/edit-vehicle/${vehicleSlug}`;
                } else {
                    showToast("error", "Vehicle not found.");
                }
            },
            error: function (xhr, status, error) {
                showToast(
                    "error",
                    "Something went wrong while checking the vehicle."
                );
            },
        });
    }

    function deleteVehicleList(vehicleId) {
        $("#delete_id").val(vehicleId);
    }

    $(document).on("change", ".toggle-popular", function () {
        const vehicleId = $(this).data("id");
        const isChecked = $(this).is(":checked") ? 1 : 0;

        $.ajax({
            url: "/admin/set-popular",
            type: "GET",
            data: {
                id: vehicleId,
                popular: isChecked,
            },
            success: function (response) {
                showToast("success", "Popular status updated.");
            },
            error: function (xhr, status, error) {
                showToast("error", "Something went wrong.");
            },
        });
    });

    $(document).on("change", ".toggle-recommended", function () {
        const vehicleId = $(this).data("id");
        const isChecked = $(this).is(":checked") ? 1 : 0;

        $.ajax({
            url: "/admin/set-recommended",
            type: "GET",
            data: {
                id: vehicleId,
                recommended: isChecked,
            },
            success: function (response) {
                showToast("success", "Recommended status updated.");
            },
            error: function (xhr, status, error) {
                showToast("error", "Something went wrong.");
            },
        });
    });

    $(document).ready(function () {
        initTable();

        let $select = $("#sort_by");

        let savedSort = localStorage.getItem("sort_by") || "ascending";
        $select.val(savedSort);

        function updateSelectText() {
            let selectedText = $select.find("option:selected").text();
            $select.find("option:first").text("Selected : " + selectedText);
        }

        updateSelectText();

        $select.on("change", function () {
            localStorage.setItem("sort_by", $(this).val());
            updateSelectText();
        });

        $("#sort_by_date").val("");

        $("#name, #sort_by_date").on("change keyup", function () {
            fetchFilteredData();
        });

        $(document).on("click", "#applyFilter", function () {
            fetchFilteredData(true);
        });

        $(document).on("click", "#clearFilter", function () {
            $("input[type='checkbox']").prop("checked", false);
            $(".dropdown-menu-md .dropdown-item").removeClass("active");
            $("#name").val("");
            $("#sort_by_date").val("");
            selectedStatus = null;
            currentSortType = null;
            $("#sortLabel").text(_l("admin.common.latest"));
            fetchFilteredData();
        });

        $(document).on("click", ".statusFilter .dropdown-item", function () {
            $(".statusFilter .dropdown-item").removeClass("active");
            $(this).addClass("active");

            let statusText = $(this).text().trim();
            selectedStatus =
                statusText === "Active"
                    ? 1
                    : statusText === "Inactive"
                    ? 0
                    : null;
        });

        $(document).on("click", ".sort-option", function () {
            const sortType = $(this).data("sort");

            $("#sortFilter .sort-option").removeClass("active");
            $(this).addClass("active");

            currentSortType = sortType;
            $("#sortLabel").text($(this).text().trim());

            fetchFilteredData();
        });

        $("#deleteVehicle").on("submit", function (e) {
            e.preventDefault();

            var vehicleId = $("#delete_id").val();
            var $submitBtn = $(".submitbtn"); // Button for submission

            $submitBtn.prop("disabled", true); // Disable the button
            $submitBtn.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
            ); // Show loading spinner

            $.ajax({
                url: "/admin/vehicle/delete",
                method: "POST",
                data: {
                    delete_id: vehicleId,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        $("#delete-modal").modal("hide");
                        initTable();
                    } else {
                        alert("Failed to delete vehicle.");
                    }
                },
                error: function (error) {
                    alert("An error occurred while deleting the vehicle.");
                },
                complete: function () {
                    $submitBtn.prop("disabled", false); // Re-enable the button
                    $submitBtn.html("Yes, Delete"); // Reset the button text
                },
            });
        });

        $(document).on("click", "#deleteSelectedVehicles", function () {
            const vehicleIds = [];

            $(".form-check-input:checked").each(function () {
                const id = $(this).closest(".form-check").data("id");
                if (id) {
                    vehicleIds.push(id);
                }
            });

            if (vehicleIds.length === 0) {
                showToast("error", "No vehicles selected.");
                return;
            }

            $.ajax({
                url: "/admin/vehicle/multiple/delete",
                method: "POST",
                data: {
                    delete_id: vehicleIds,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        initTable();
                        showToast(
                            "success",
                            "Selected Vehicles delated successfully."
                        );
                    } else {
                        alert("Failed to delete vehicle(s).");
                    }
                },
                error: function () {
                    alert("An error occurred while deleting the vehicles.");
                },
            });
        });

        $(document).on("click", ".change-language", function () {
            var languageCode = $(this).data("language_code");

            $.ajax({
                url: "/admin/flag-change-language",
                type: "POST",
                data: { language_code: languageCode },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.status === "success") {
                        location.reload();
                    }
                },
            });
        });

        $(document).on("click", ".delete-vehicle", function () {
            const vehicleId = $(this).data("id");
            deleteVehicleList(vehicleId);
        });

        $(document).on("click", ".edit-vehicles", function () {
            const vehicleSlug = $(this).data("id");
            editVechileList(vehicleSlug);
        });

        $("#statusVehicleForm").on("submit", function (e) {
            e.preventDefault();

            let vehicleId = $("#status_vehicle_id").val();
            let status = $("#vehicle_status").val();

            $.ajax({
                url: "/admin/set-status",
                method: "GET",
                data: {
                    vehicle_id: vehicleId,
                    status: status,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        initTable();
                        $("#status-modal").modal("hide");
                        showToast("success", "Vehicle status updated.");
                    } else {
                        showToast("error", "Failed to update status.");
                    }
                },
                error: function () {
                    showToast("error", "An error occurred.");
                },
            });
        });

        $(document).on(
            "click",
            "[data-bs-target='#status-modal']",
            function () {
                const vehicleId = $(this).data("id");
                const status = $(this).data("status");

                $("#status_vehicle_id").val(vehicleId);
                $("#vehicle_status").val(status).trigger("change"); // Important for Select2
            }
        );
    });
})();
