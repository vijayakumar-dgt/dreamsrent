(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, support');
    const permissions = await loadUserPermissions();

$(document).ready(function () {
    contactTable();

    $(".sort-dropdown .dropdown-item").on("click", function () {
        let sortBy = $(this).data("sort");
        $(".sort-dropdown-toggle").html(`<i class="ti ti-filter me-1"></i> ${_l('admin.common.sort_by')}: ${$(this).text()}`);
        contactTable(sortBy, $(".search-input").val());
    });

    $(".search-input").on("keyup", function () {
        let searchQuery = $(this).val();
        contactTable("", searchQuery);
    });
});

function contactTable(sortBy = "latest", searchQuery = "") {
    $.ajax({
        url: "/admin/contact-message/list",
        type: "GET",
        data: { sort_by: sortBy, search: searchQuery },
        beforeSend: function () {
            $(".table-loader").show();
            $(".real-table, .table-footer").addClass("d-none");
        },
        complete: function () {
            $(".table-loader, .input-loader, .label-loader").hide();
            $(".real-table, .real-label, .real-input").removeClass("d-none");
            if ($("#contactTable").length === 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        },
        success: function (response) {
            let tableBody = "";

            if ($.fn.DataTable.isDataTable("#contactTable")) {
                $("#contactTable").DataTable().clear().destroy();
            }

            if (response.success && response.data.length > 0) {
                $.each(response.data, function (index, value) {
                    let imageSrc = value.image ? value.image : "/assets/img/profiles/avatar-20.jpg";

                    tableBody += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                    <img src="${imageSrc}" class="rounded-circle" alt="">
                                </a>
                                <h6><a href="javascript:void(0);" class="fs-14 fw-semibold">${value.name}</a></h6>
                            </div>
                        </td>
                        <td><p class="text-gray-9">${value.phone_number}</p></td>
                        <td><p class="text-gray-9">${value.email}</p></td>
                        <td><p class="text-gray-9">${value.created_at ? new Date(value.created_at).toLocaleDateString() : '-'}</p></td>
                        <td>
                            <span class="avatar avatar-md bg-light rounded-circle tooltip-trigger"
                                  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                  title="${value.message}">
                                <i class="ti ti-file-invoice text-gray-9"></i>
                            </span>
                        </td>
                        ${hasPermission(permissions, 'contact_messages', 'delete') ?
                        `<td>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                    ${hasPermission(permissions, 'contact_messages', 'delete') ?
                                    `<li>
                                        <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal"
                                           data-bs-target="#delete_contact" onclick="deleteContact(${value.id});">
                                            <i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}
                                        </a>
                                    </li>` : ''}
                                </ul>
                            </div>
                        </td>` : ''}
                    </tr>`;
                });
            } else {
                tableBody = `<tr><td colspan="6" class="text-center">${_l("admin.common.empty_table")}</td></tr>`;
            }

            $("#contactTable tbody").html(tableBody);

            if ((response.data.length > 0)) {
                $("#contactTable").DataTable({
                    ordering: false,
                    searching: false,
                    pageLength: 10,
                    lengthChange: false,
                    "drawCallback": function () {
                        $(".dataTables_info").addClass('d-none');
                        $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');

                        var tableWrapper = $(this).closest('.dataTables_wrapper');
                        var info = tableWrapper.find('.dataTables_info');
                        var pagination = tableWrapper.find('.dataTables_paginate');

                        $('.table-footer').empty()
                            .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                                .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                                .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                            );
                        $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
                    },
                    language: {
                        emptyTable: _l("admin.common.empty_table"),
                        info: _l("admin.common.showing") + " _START_ " + _l("admin.common.to") + " _END_ " + _l("admin.common.of") + " _TOTAL_ " + _l("admin.common.entries"),
                        infoEmpty: _l("admin.common.showing") + " 0 " + _l("admin.common.to") + " 0 " + _l("admin.common.of") + " 0 " + _l("admin.common.entries"),
                        infoFiltered: "(" + _l("admin.common.filtered_from") + " _MAX_ " + _l("admin.common.total_entries") + ")",
                        lengthMenu: _l("admin.common.show") + " _MENU_ " + _l("admin.common.entries"),
                        search: _l("admin.common.search") + ":",
                        zeroRecords: _l("admin.common.no_matching_records"),
                        paginate: {
                            first: _l("admin.common.first"),
                            last: _l("admin.common.last"),
                            next: _l("admin.common.next"),
                            previous: _l("admin.common.previous"),
                        },
                    },
                });
            }

            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        error: function (error) {
            showToast('error', error.responseJSON?.error || "An error occurred while retrieving contacts!");
        },
    });
}

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
        success: function(response) {
            if(response.code === 200){
                showToast('success', response.message);
                $("#delete_contact").modal('hide');
                contactTable();
            }
        },
        error: function(res) {
            if(res.responseJSON && res.responseJSON.code === 500){
                showToast('error', res.responseJSON.error);
            } else {
                showToast('error', 'An error occurred while deleting!');
            }
        }
    });
});

})();

function deleteContact(id){
    $("#delete_id").val(id);
}
