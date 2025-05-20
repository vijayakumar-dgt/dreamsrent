let ticketData = [];
"use strict";
(async () => {
    await loadTranslationFile('admin', 'common, support');
    const permissions = await loadUserPermissions();

$(document).ready(function () {
     $(document).on('click', '.edit-ticket-btn', function () {
        var button = $(this);

        var ticketId = button.data('ticket-id');
        var assigneeId = button.data('assignee-id');
        var categoryId = button.data('category-id');
        var priority = button.data('priority');
        var status = button.data('status');
        var reply = button.data('reply');

        populateEditForm(ticketId, assigneeId, categoryId, priority, status, reply);
    });
     $(document).on('click', '.ticket-history-btn', function () {
        const ticketId = $(this).data('ticket-id');
        showTicketHistory(ticketId);
    });
      $(document).on('click', '.delete-ticket-btn', function () {
        var ticketId = $(this).data('id');
        $("#delete_id").val(ticketId);
    });
    $('.summernote').summernote({
        height: 150,
        placeholder: 'Type your Description here...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    $("#editTicketstatus").validate({
        rules: {
            assign_staff: {
                required: true
            },
        },
        messages: {
            assign_staff: {
                required: _l('admin.support.assign_staff_required'),
            },
        },
        errorPlacement: function (error, element) {
            var errorId = element.attr("id") + "Error";
            $("#" + errorId).text(error.text());
        },
        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
            var errorId = $(element).attr("id") + "Error";
            $("#" + errorId).text("");
        },
        onkeyup: function (element) {
            $(element).valid();
        },
        onchange: function (element) {
            $(element).valid();
        },
        submitHandler: function (form) {
            let editData = new FormData(form);
            $(".btn-primary").text('Please Wait...').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/admin/ticket/update/assign",
                data: editData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('.submitbtn').attr('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                    `);
                },
                complete: function () {
                    $('.submitbtn').attr('disabled', false).html(_l('admin.common.update'));
                },
                success: function (resp) {
                    if (resp.code === 200) {
                        showToast('success', resp.message);
                        $("#edit_ticket").modal("hide");
                        // Reload tickets list
                        ticketTable();
                    }
                },
                error: function (error) {
                    $(".error-message").text("");
                    $(".form-control").removeClass("is-invalid is-valid");

                    if (error.responseJSON.code === 422) {
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "Error").text(val[0]);
                        });
                    } else {
                        showToast('error', error.responseJSON.message);
                    }
                }
            });
        }
    });

    ticketTable();

});
$(document).ready(function () {
    $('.filterbox .links.text-purple').on('click', function () {
        updateFilterCount();
        ticketTable();
    });

    // Reset filters and reload ticket table
    $('.filterbox .links.text-danger').on('click', function () {
        $('input[name="priority[]"], input[name="status[]"]').prop('checked', false);
        $('input[name="search"]').val('');
        $('#current_sort').attr('data-sort', 'latest').text(_l('admin.common.latest'));
        updateFilterCount();
        ticketTable();
    });

    let searchTimeout;
    $('input[name="search"]').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            ticketTable();
        }, 300);
    });

    // Handle sorting based on dropdown selection
    $('.sort_by_list .dropdown-item').on('click', function () {
        const selectedSort = $(this).data('sort');
        $('#current_sort').attr('data-sort', selectedSort).text($(this).text());
        ticketTable();
    });

    // Filter dropdown options when searching
    $('.filter-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filterType = $(this).data('filter');
        $(`input[name="${filterType}[]"]`).each(function() {
            const labelText = $(this).parent().text().toLowerCase();
            $(this).closest('li').toggle(labelText.includes(searchTerm));
        });
    });
});

function updateFilterCount() {
    const totalFilters = $('input[name="priority[]"]:checked, input[name="status[]"]:checked').length;
    $('.filtercollapse .badge').text(totalFilters).toggleClass('d-none', totalFilters === 0);
}

function ticketTable() {
    const priority = [];
    $('input[name="priority[]"]:checked').each(function() {
        priority.push($(this).val());
    });

    const status = [];
    $('input[name="status[]"]:checked').each(function() {
        status.push($(this).val());
    });

    const sortBy = $('#current_sort').data('sort') || 'latest';
    const search = $('input[name="search"]').val() || '';

    $.ajax({
        url: "/admin/ticket/list",
        type: "GET",
        data: {
            priority: priority,
            status: status,
            sort_by: sortBy,
            search: search,
            _token: $('meta[name="csrf-token"]').attr('content')
        },

        beforeSend: function () {
            $(".table-loader").show();
            $(".real-table, .table-footer").addClass("d-none");
        },
        complete: function () {
            $(".table-loader, .input-loader, .label-loader").hide();
            $(".real-table, .real-label, .real-input").removeClass("d-none");
            if ($("#adminTicketTable").length === 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        },
        success: function (response) {
            ticketData = response.data;
            let tableBody = "";

            if ($.fn.DataTable.isDataTable("#adminTicketTable")) {
                $("#adminTicketTable").DataTable().destroy();
            }

            if (response.code === 200 && response.data.length > 0) {
                let tickets = response.data;

                $.each(tickets, function (index, ticket) {
                    let userImage = ticket.user?.user_detail?.profile_image
                    ? "/storage/" + ticket.user.user_detail.profile_image
                    : "/backend/assets/img/default-profile.png";

                let assigneeImage = ticket.assignee?.user_detail?.profile_image
                    ? "/storage/" + ticket.assignee.user_detail.profile_image
                    : "/backend/assets/img/default-profile.png";


                    let priorityClass = "";
                    if (ticket.priority === "High") priorityClass = "text-danger";
                    else if (ticket.priority === "Medium") priorityClass = "text-warning";
                    else priorityClass = "text-success";

                    let statusBadge = "";
                    switch (ticket.status) {
                        case 1:
                            statusBadge = `<span class="badge bg-violet-transparent"><i class="ti ti-point-filled text-violet me-1"></i>${_l('admin.support.open')}</span>`;
                            break;
                        case 2:
                            statusBadge = `<span class="badge bg-primary-transparent"><i class="ti ti-point-filled text-primary me-1"></i>${_l('admin.support.assigned')}</span>`;
                            break;
                        case 3:
                            statusBadge = `<span class="badge bg-info-transparent"><i class="ti ti-point-filled text-info me-1"></i>${_l('admin.support.inprogress')}</span>`;
                            break;
                        case 4:
                            statusBadge = `<span class="badge bg-success-transparent"><i class="ti ti-point-filled text-success me-1"></i>${_l('admin.support.closed')}</span>`;
                            break;
                        default:
                            statusBadge = `<span class="badge bg-secondary-transparent">Unknown</span>`;
                    }


                    tableBody += `<tr>
                        <td><p >#${ticket.ticket_id}</p></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                    <img src="${userImage}" class="rounded-circle" alt="">
                                </a>
                                <h6>
                                    <a href="javascript:void(0);" class="fs-14 fw-semibold">
                                        ${
                                        ticket.user?.user_detail?.first_name && ticket.user?.user_detail?.last_name
                                            ? `${ticket.user.user_detail.first_name} ${ticket.user.user_detail.last_name}`
                                            : (ticket.user?.name || "Unknown")
                                        }
                                    </a>
                                </h6>
                            </div>
                        </td>
                        <td><p class="text-gray-9">${ticket.category.name}</p></td>
                        <td><p class="text-gray-9">${new Date(ticket.created_at).toLocaleDateString()}</p></td>
                        <td>
                            <span class="badge badge-dark-transparent rounded-pill"><i class="ti ti-point-filled ${priorityClass}"></i> ${ticket.priority}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                              ${
                                ticket.assignee
                                    ? `<a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                            <img src="${assigneeImage}" class="rounded-circle" alt="">
                                    </a>
                                    <h6>
                                        <a href="javascript:void(0);" class="fs-14 fw-semibold">
                                            ${
                                                ticket.assignee.user_detail?.first_name && ticket.assignee.user_detail?.last_name
                                                    ? `${ticket.assignee.user_detail.first_name} ${ticket.assignee.user_detail.last_name}`
                                                    : (ticket.assignee.name || '-')
                                            }
                                        </a>
                                    </h6>`
                                    : `<div class="d-flex justify-content-center w-100">
                                                <h6 class="text-muted mb-0">-</h6>
                                        </div>`
                            }
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        ${(hasPermission(permissions, 'tickets', 'edit') || hasPermission(permissions, 'tickets', 'delete') || hasPermission(permissions, 'tickets', 'view')) ?
                        `<td>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                ${ hasPermission(permissions, 'tickets', 'edit') ?
                                    `<li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item rounded-1 edit-ticket-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#edit_ticket"
                                            data-ticket-id="${ticket.id}"
                                            data-assignee-id="${ticket.assignee_id}"
                                            data-category-id="${ticket.category.id}"
                                            data-priority="${ticket.priority}"
                                            data-status="${ticket.status}"
                                            data-reply='${JSON.stringify(ticket.reply_description)}'
                                        >
                                            <i class="ti ti-edit me-1"></i>${_l('admin.common.assign')}
                                        </button>
                                    </li>` : ''}
                                   
                                   ${ticket.assignee_id ? `
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item rounded-1 ticket-history-btn" 
                                            data-ticket-id="${ticket.id}"
                                        >
                                            <i class="ti ti-eye me-1"></i> ${_l('admin.common.history')}
                                        </button>
                                    </li>` : ''}
                                     ${ hasPermission(permissions, 'tickets', 'delete') ?
                                    `<li>
                                       <button 
                                            type="button" 
                                            class="dropdown-item rounded-1 delete-ticket-btn" 
                                            data-id="${ticket.id}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#delete_ticket"
                                        >
                                            <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                        </button>

                                    </li>` : ''}
                                </ul>
                            </div>
                        </td>` : ''}
                    </tr>`;
                });
            } else {
                tableBody = `<tr><td colspan="9" class="text-center">${_l('admin.common.empty_table')}</td></tr>`;
            }

            $("#adminTicketTable tbody").html(tableBody);

            if (response.data.length > 0) {
                $("#adminTicketTable").DataTable({
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

        },
        error: function (error) {
            showToast('error', error.responseJSON?.error || "An error occurred while retrieving tickets!");
        },
    });
}

}) ();

function showTicketHistory(ticketId) {
    let ticket = ticketData.find(t => t.id === ticketId); // Use global ticketData
    localStorage.setItem('ticketId', ticketId);
    window.location.href = "/admin/ticket-details";

    if (!ticket || !ticket.ticket_histories.length) {
        $(".ticket_histroy").html('<p class="text-center">No history available for this ticket.</p>');
        return;
    }

    let historyHtml = "";

    ticket.ticket_histories.forEach(history => {
        let userImage = history.user && history.user.user_detail && history.user.user_detail.profile_image
            ? '/storage/' + history.user.user_detail.profile_image
            : '/backend/assets/img/profiles/avatar-20.jpg';

        let userName = history.user?.user_detail?.first_name && history.user?.user_detail?.last_name
            ? `${history.user.user_detail.first_name} ${history.user.user_detail.last_name}`
            : (history.user?.name || "Unknown User");

        let createdAt = new Date(history.created_at).toLocaleString();

        historyHtml += `
            <div class="comment-item mt-3">
                <div class="d-flex align-items-center mb-1">
                    <span class="avatar avatar-l me-2 flex-shrink-0">
                        <img src="${userImage}" alt="User Profile Image" class="img-fluid rounded-circle">
                    </span>
                    <div>
                        <h6 class="mb-1">${userName}</h6>
                        <p><i class="ti ti-calendar-bolt me-1"></i> Updated on ${createdAt}</p>
                    </div>
                </div>
                <div class="border-bottom p-2">
                    <p>${history.description}</p>
                </div>
            </div>
        `;
    });

    $(".ticket_histroy").html(historyHtml);
}
function populateEditForm(ticketId, assigneeId, categoryId, priority, status, reply) {
    $('#editTicketstatus').attr('data-ticket-id', ticketId);
    $('#ticketid').val(ticketId);

    $('#assignStaff').val(assigneeId).change();

    $('#category').val(categoryId);

    $('#priority').val(priority).change();

    $('#status').val(status).change();

    $('#reply').val(reply);
}
function deleteTicket(id){
    $("#delete_id").val(id);
}

$("#delete_ticket_form").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url: "/admin/ticket/delete",
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
                $("#delete_ticket").modal('hide');
                ticketTable();
            }
        },
        error: function(res) {
            if(res.responseJSON && res.responseJSON.code === 500){
                showToast('error', res.responseJSON.error);
            } else {
                showToast('error', _l('admin.common.default_delete_error'));
            }
        }
    });
});
