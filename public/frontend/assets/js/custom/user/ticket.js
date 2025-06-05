(async () => {
    "use strict";
    await loadTranslationFile("web", "user,common");
    $(document).ready(function () {
        let ticketData = [];
        initEvents();
        TicketTable();
        initSummerNote();
        initValidation();

        function initEvents() {
            $(document).on("click", ".view-reply-btn", function () {
                const ticketId = $(this).data("id");
                const assigneeId = $(this).data("assignee-id");
                const subject = $(this).data("subject");
                const priority = $(this).data("priority");
                const status = $(this).data("status");
                const reply = $(this).data("reply");
                const description = $(this).data("description");

                populateEditForm(
                    ticketId,
                    assigneeId,
                    subject,
                    priority,
                    status,
                    reply,
                    description
                );
                showTicketHistory(ticketId);
            });
            $(document).on("click", ".delete-ticket-btn", function () {
                const ticketId = $(this).data("id");
                deleteTicket(ticketId);
            });
            $(document).on("click", ".show-ticket-history", function () {
                const ticketId = $(this).data("id");
                showTicketHistory(ticketId);
            });

            $("#delete_ticket_form").on("submit", function (e) {
                e.preventDefault();
                $.ajax({
                    url: "/ticket/delete",
                    type: "POST",
                    data: {
                        id: $("#delete_id").val(),
                    },
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            showToast("success", response.message);
                            $("#delete_ticket").modal("hide");
                            TicketTable();
                        }
                    },
                    error: function (res) {
                        if (res.responseJSON && res.responseJSON.code === 500) {
                            showToast("error", res.responseJSON.error);
                        } else {
                            showToast("error", "An error occurred while deleting!");
                        }
                    },
                });
            });
        }
        
        function initSummerNote(){
            $(".summernote").summernote({
                height: 150, // Set the height of the editor
                placeholder: _l("web.user.description_placeholder"),
                toolbar: [
                    ["style", ["bold", "italic", "underline", "clear"]],
                    ["font", ["strikethrough", "superscript", "subscript"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["insert", ["link", "picture", "video"]],
                    ["view", ["fullscreen", "codeview", "help"]],
                ],
            });
        }
        
        function initValidation() {
             $("#addTicket").validate({
            rules: {
                category: {
                    required: true,
                },
                priority: {
                    required: true,
                },
                description: {
                    required: true,
                    maxlength: 600,
                },
                "document[]": {
                    accept: "application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                },
            },
            messages: {
                category: {
                    required: _l("web.user.subject_required"),
                },
                priority: {
                    required: _l("web.user.priority_required"),
                },
                description: {
                    required: _l("web.user.description_required"),
                    maxlength: _l("web.user.desc_max_60_words"),
                },
                "document[]": {
                    accept: _l("web.user.ticket_doc_extension"),
                },
            },
            errorPlacement: function (error, element) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = $(element).attr("id") + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let ticketData = new FormData(form);

                $(".btn-primary")
                    .text(_l("web.user.plz_wait"))
                    .prop("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "/user/ticket/store",
                    data: ticketData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_ticket").modal("hide");
                            $("#addTicket")[0].reset();
                            $(".btn-primary")
                                .text(_l("web.user.create"))
                                .prop("disabled", false);
                            TicketTable();
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }

                        $(".btn-primary")
                            .text(_l("web.user.create"))
                            .prop("disabled", false);
                    },
                });
            },
        });
        $("#editTicketstatus").validate({
            rules: {
                assign_staff: {
                    required: false,
                },
                status: {
                    required: true,
                },
                reply: {
                    required: true,
                    maxlength: 60,
                },
            },
            messages: {
                assign_staff: {
                    required: _l("web.user.select_staff_member"),
                },
                status: {
                    required: _l("web.user.select_status"),
                },
                reply: {
                    required: _l("web.user.plz_enter_reply"),
                    maxlength: _l("web.user.desc_max_60_words"),
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
                $(".btn-primary")
                    .text(_l("web.user.plz_wait"))
                    .prop("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "/ticket/update",
                    data: editData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#edit_ticket").modal("hide");
                            $(".btn-primary")
                                .text("Update")
                                .prop("disabled", false);
                            TicketTable();
                        }
                    },
                    error: function (error) {
                        $(".error-message").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "Error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }

                        $(".btn-primary")
                            .text(_l("web.user.update"))
                            .prop("disabled", false);
                    },
                });
            },
        });
        }
       
        function TicketTable() {
            $.ajax({
                url: "/ticket/list",
                type: "GET",
                beforeSend: () => {
                    $(".table-loader").removeClass("d-none");
                    $(".real-table").addClass("d-none");
                },
                success: function (response) {
                    ticketData = response.data;
                    let tableBody = "";

                    if ($.fn.DataTable.isDataTable("#ticketTable")) {
                        $("#ticketTable").DataTable().destroy();
                    }

                    if (response.data.length > 0) {
                        let data = response.data;
                        $.each(data, function (index, value) {
                            let subjectName = value.subject;
                            let assigneeName =
                                value.assignee && value.assignee.user_detail
                                    ? value.assignee.user_detail.first_name +
                                    " " +
                                    value.assignee.user_detail.last_name
                                    : value.assignee
                                    ? value.assignee.name
                                    : _l("web.user.unassigned");
                            let createdDate = new Date(
                                value.created_at
                            ).toLocaleDateString();
                            let assigneeImage = value.assignee?.user_detail
                                ?.profile_image
                                ? "/storage/" + value.assignee.user_detail.profile_image
                                : "/backend/assets/img/default-profile.png";

                            // Priority badge
                            let priorityBadge = `<span class="badge badge-secondary bg-secondary-transparent ticket-badge">${_l(
                                "web.user.unknown"
                            )}</span>`;
                            if (value.priority === "High") {
                                priorityBadge = `<span class="badge badge-danger bg-danger-transparent ticket-badge">${_l(
                                    "web.user.high"
                                )}</span>`;
                            } else if (value.priority === "Medium") {
                                priorityBadge = `<span class="badge badge-primary bg-primary-transparent ticket-badge">${_l(
                                    "web.user.medium"
                                )}</span>`;
                            } else if (value.priority === "Low") {
                                priorityBadge = `<span class="badge badge-success bg-success-transparent ticket-badge">${_l(
                                    "web.user.low"
                                )}</span>`;
                            }

                            // Status badge
                            let statusBadge = `<span class="badge badge-secondary bg-secondary-transparent ticket-badge">${_l(
                                "web.user.unknown"
                            )}</span>`;
                            if (value.status === 1) {
                                statusBadge = `<span class="badge badge-primary bg-primary-transparent ticket-badge">${_l(
                                    "web.user.ticket_open"
                                )}</span>`;
                            } else if (value.status === 2) {
                                statusBadge = `<span class="badge badge-warning bg-warning-transparent ticket-badge">${_l(
                                    "web.user.ticket_assigned"
                                )}</span>`;
                            } else if (value.status === 3) {
                                statusBadge = `<span class="badge badge-secondary bg-secondary-transparent ticket-badge">${_l(
                                    "web.user.ticket_in_progress"
                                )}</span>`;
                            } else if (value.status === 4) {
                                statusBadge = `<span class="badge badge-danger bg-danger-transparent ticket-badge">${_l(
                                    "web.user.ticket_closed"
                                )}</span>`;
                            }

                            tableBody += `
                                    <tr>
                                        <td>#${
                                            value.ticket_id ? value.ticket_id : "N/A"
                                        }</td>
                                        <td>${subjectName}</td>
                                        <td>${value.formatted_created_at}</td>
                                        <td>${priorityBadge}</td>
                                        <td>
                                            ${
                                                value.assignee
                                                    ? `<div class="d-flex align-items-center">
                                                    
                                                            <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                                                <img src="${assigneeImage}" class="rounded-circle" alt="">
                                                            </a>
                                                            <h6><a href="javascript:void(0);" class="fs-14 fw-semibold">${assigneeName}</a></h6>
                                                        </div>`: 
                                                        `<div class="d-flex justify-content-center w-50">
                                                                    <h6 class="">-</h6>
                                                        </div>`
                                            }</td>
                                        <td>${statusBadge}</td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                <button 
                                                        type="button" 
                                                        class="dropdown-item rounded-1 view-reply-btn" 
                                                        data-id="${value.id}" 
                                                        data-assignee-id="${
                                                            value.assignee_id
                                                        }" 
                                                        data-subject="${
                                                            value.subject
                                                        }" 
                                                        data-priority="${
                                                            value.priority
                                                        }" 
                                                        data-status="${value.status}" 
                                                        data-reply='${JSON.stringify(
                                                            value.reply_description
                                                        )}' 
                                                        data-description="${value.description.replace(
                                                            /"/g,
                                                            "&quot;"
                                                        )}" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#edit_ticket"
                                                    >
                                                        <i class="feather-edit me-1"></i>${_l(
                                                            "web.common.view_reply"
                                                        )}
                                                    </button>

                                                    <button 
                                                        type="button" 
                                                        class="dropdown-item delete-ticket-btn" 
                                                        data-id="${value.id}" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#delete_ticket"
                                                    >
                                                        <i class="feather-trash-2"></i> ${_l(
                                                            "web.common.delete"
                                                        )}
                                                    </button>

                                                    <button 
                                                        type="button" 
                                                        class="dropdown-item rounded-1 d-none show-ticket-history" 
                                                        data-id="${value.id}" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#histroy_ticket"
                                                    >
                                                        <i class="feather-eye me-1"></i> ${_l(
                                                            "web.user.history"
                                                        )}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
                        });
                    } else {
                        tableBody += `
                            <tr>
                                <td colspan="7" class="text-center">${_l(
                                    "web.common.empty_table"
                                )}</td>
                            </tr>`;
                        $(".table-footer").empty();
                    }

                    $("#ticketTable tbody").html(tableBody);

                    // Initialize DataTable
                    if (response.data.length > 0) {
                        $("#ticketTable").DataTable({
                            ordering: false,
                            searching: false,
                            pageLength: 10,
                            lengthChange: false,
                            drawCallback: function () {
                                $(".dataTables_info").addClass("d-none");
                                $(".dataTables_wrapper .dataTables_paginate").addClass(
                                    "d-none"
                                );

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
                        });
                    }
                },
                error: function (error) {
                    if (error.responseJSON && error.responseJSON.error) {
                        showToast("error", error.responseJSON.error);
                    } else {
                        showToast(
                            "error",
                            "An error occurred while retrieving ticket data!"
                        );
                    }
                },
                complete: () => {
                    $(".table-loader").addClass("d-none");
                    $(".real-table").removeClass("d-none");
                },
            });
        }

        function showTicketHistory(ticketId) {
            let ticket = ticketData.find((t) => t.id === ticketId); // Use global ticketData
            const $historyContainer = $(".ticket_histroy").empty(); // Clear previous history

            if (!ticket || !ticket.ticket_histories.length) {
                $historyContainer.append(
                    $('<p>').addClass('text-center ticket_no_data').text(_l("web.common.empty_table"))
                );
                return;
            }

            ticket.ticket_histories.forEach((history) => {
                const userImage = (history.user?.user_detail?.profile_image)
                    ? `/storage/${encodeURIComponent(history.user.user_detail.profile_image)}`
                    : "/backend/assets/img/profiles/avatar-01.jpg";

                const firstName = history.user?.user_detail?.first_name || '';
                const lastName = history.user?.user_detail?.last_name || '';
                const userName = firstName && lastName ? `${firstName} ${lastName}` : (history.user?.name || "Unknown User");

                const createdAt = new Date(history.created_at).toLocaleString();
                const description = history.description || '';

                const $commentItem = $('<div>').addClass('comment-item mt-3');

                const $userInfo = $('<div>').addClass('d-flex align-items-center mb-1');
                
                const $avatarImg = $('<img>', {
                    src: userImage,
                    alt: 'User Profile Image',
                    class: 'img-fluid rounded-circle'
                });

                const $avatar = $('<span>').addClass('avatar avatar-l me-2 flex-shrink-0').append($avatarImg);

                const $userDetails = $('<div>').append(
                    $('<h6>').addClass('mb-1').text(userName),
                    $('<p>').append(
                        $('<i>').addClass('ti ti-calendar-bolt me-1'),
                        document.createTextNode(`${_l('admin.common.updated_on')} ${createdAt}`)
                    )
                );

                $userInfo.append($avatar, $userDetails);

                const $commentText = $('<div>').addClass('border-bottom p-2').append(
                    $('<p>').text(description)
                );

                $commentItem.append($userInfo, $commentText);
                $historyContainer.append($commentItem);
            });
        }

        function populateEditForm(
            ticketId,
            assigneeId,
            categoryId,
            priority,
            status,
            reply,
            description
        ) {
            $("#editTicketstatus").attr("data-ticket-id", ticketId);
            $("#ticketid").val(ticketId);

            $("#assignStaff").val(assigneeId).change();

            $("#category").val(categoryId);

            $("#priority").val(priority).change();

            $("#status").val(status).change();

            $("#reply").val(reply);
            let plainText = $("<div>").html(description).text();

            plainText = plainText.charAt(0).toUpperCase() + plainText.slice(1);

            $(".description").text(plainText);
        }

        function deleteTicket(id) {
            $("#delete_id").val(id);
        }
    });
})();
