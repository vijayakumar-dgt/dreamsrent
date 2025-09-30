(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, support');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        contactTable();
        initEvents();

        function initEvents(){
            $(document).on('click', '.delete-contact-btn', function () {
                const id = $(this).data('id');
                deleteContact(id);
            });

            $(".sort-dropdown .dropdown-item").on("click", function () {
                let sortBy = $(this).data("sort");
                $(".sort-dropdown-toggle").html(`<i class="ti ti-filter me-1"></i> ${_l('admin.common.sort_by')}: ${$(this).text()}`);
                contactTable(sortBy, $(".search-input").val());
            });

            $(".search-input").on("keyup", function () {
                let searchQuery = $(this).val();
                contactTable("", searchQuery);
            });

            $("#contactDeleteForm").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: "/admin/contact-message/delete",
                    type: "POST",
                    data: {
                        id: $('#delete_id').val()
                    },
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: handleDeleteSuccess,
                    error: handleDeleteError
                });
            });
        }

        function handleDeleteSuccess(response) {
            if (response.code === 200) {
                showToast('success', response.message);
                $("#delete_contact").modal('hide');
                contactTable();
            }
        }

        // Error handler
        function handleDeleteError(res) {
            if (res.responseJSON && res.responseJSON.code === 500) {
                showToast('error', res.responseJSON.error);
            } else {
                showToast('error', 'An error occurred while deleting!');
            }
        }

        // Render a single row
        function renderContactRow(value) {
            const imageSrc = value.image || "/backend/assets/img/profiles/avatar-01.jpg";

            const deleteBtn = hasPermission(permissions, 'contact_messages', 'delete')
                ? `<td>
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <button type="button" class="dropdown-item rounded-1 delete-contact-btn"
                                    data-id="${value.id}" data-bs-toggle="modal" data-bs-target="#delete_contact">
                                    <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                </button>
                            </li>
                        </ul>
                    </div>
                </td>` : '';

            return `<tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-2 flex-shrink-0">
                            <img src="${imageSrc}" class="rounded-circle" alt="">
                        </div>
                        <h6 class="fs-14 fw-semibold text-black">${value.name}</h6>
                    </div>
                </td>
                <td><p class="text-gray-9">${value.phone_number}</p></td>
                <td><p class="text-gray-9">${value.email}</p></td>
                <td><p class="text-gray-9">${value.created_date}</p></td>
                <td>
                    <span class="avatar avatar-md bg-light rounded-circle tooltip-trigger"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                        title="${value.message}">
                        <i class="ti ti-file-invoice text-gray-9"></i>
                    </span>
                </td>
                ${deleteBtn}
            </tr>`;
        }

        // Render table body
        function renderContactTableBody(data) {
            if (!data || data.length === 0) {
                $('.table-footer').empty();
                return `<tr><td colspan="6" class="text-center">${_l("admin.common.empty_table")}</td></tr>`;
            }
            return data.map(renderContactRow).join('');
        }

        // Initialize DataTable and footer
        function initializeContactDataTable() {
            if ($("#contactTable").length === 0) return;

            $("#contactTable").DataTable({
                ordering: false,
                searching: false,
                pageLength: 10,
                lengthChange: false,
                drawCallback: function () {
                    const tableWrapper = $(this).closest('.dataTables_wrapper');
                    const info = tableWrapper.find('.dataTables_info').clone(true);
                    const pagination = tableWrapper.find('.dataTables_paginate').clone(true);

                    $(".table-footer").empty()
                        .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                            .append($('<div class="datatable-info"></div>').append(info))
                            .append($('<div class="datatable-pagination"></div>').append(pagination))
                        );

                    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
                },
                language: {
                    emptyTable: _l("admin.common.empty_table"),
                    info: `${_l("admin.common.showing")} _START_ ${_l("admin.common.to")} _END_ ${_l("admin.common.of")} _TOTAL_ ${_l("admin.common.entries")}`,
                    infoEmpty: `${_l("admin.common.showing")} 0 ${_l("admin.common.to")} 0 ${_l("admin.common.of")} 0 ${_l("admin.common.entries")}`,
                    infoFiltered: `(${_l("admin.common.filtered_from")} _MAX_ ${_l("admin.common.total_entries")})`,
                    lengthMenu: `${_l("admin.common.show")} _MENU_ ${_l("admin.common.entries")}`,
                    search: `${_l("admin.common.search")}:`,
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

        // Show/hide loaders
        function toggleContactTableLoader(show) {
            if (show) {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            } else {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#contactTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            }
        }

        // Main function
        function contactTable(sortBy = "latest", searchQuery = "") {
            toggleContactTableLoader(true);

            $.ajax({
                url: "/admin/contact-message/list",
                type: "GET",
                data: { sort_by: sortBy, search: searchQuery },
                complete: function () { toggleContactTableLoader(false); },
                success: function (response) {
                    if ($.fn.DataTable.isDataTable("#contactTable")) {
                        $("#contactTable").DataTable().clear().destroy();
                    }

                    const tableBody = renderContactTableBody(response.data);
                    $("#contactTable tbody").html(tableBody);

                    if (response.data.length > 0) {
                        initializeContactDataTable();
                    }

                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                error: function (error) {
                    showToast('error', error.responseJSON?.error || "An error occurred while retrieving contacts!");
                },
            });
        }


        function deleteContact(id){
            $("#delete_id").val(id);
        }
    });
})();