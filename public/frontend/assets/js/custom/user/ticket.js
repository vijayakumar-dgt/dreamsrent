/* global $, loadTranslationFile, FormData, document, showToast, _l */
(async () => {
    "use strict";

    await loadTranslationFile("web", "user,common");

    $(document).ready(() => {
        initEvents();
        TicketTable();
        initSummerNote();
        initValidation();
    });

    function initEvents() {
        $(document).on("click", ".view-reply-btn", function () {
            const $this = $(this);
            const ticketId = $this.data("id");
            const assigneeId = $this.data("assignee-id");
            const subject = $this.data("subject");
            const priority = $this.data("priority");
            const status = $this.data("status");
            const reply = $this.data("reply");
            const description = $this.data("description");
            let ticketHistory = $this.data("ticket-data");

            populateEditForm(ticketId, assigneeId, subject, priority, status, reply, description);
            showTicketHistory(ticketId, ticketHistory);
        });

        $(document).on("click", ".delete-ticket-btn", function () {
            const ticketId = $(this).data("id");
            deleteTicket(ticketId);
        });

        $("#delete_ticket_form").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/ticket/delete",
                type: "POST",
                data: {
                    id: $("#delete_id").val()
                },
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                },
                success: (response) => {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $("#delete_ticket").modal("hide");
                        TicketTable();
                    }
                },
                error: (res) => {
                    const errorMsg = res.responseJSON?.error || "An error occurred while deleting!";
                    showToast("error", errorMsg);
                }
            });
        });

        $(document).on('click', '#add_ticket_btn', function () {
            $("#addTicket")[0].reset();
            $("#description").summernote("code", "");
            $("#priority").val('').trigger("change");
            $('.error-text').text('');
            $(".form-control").removeClass("is-invalid is-valid");
        });
    }

    function initSummerNote() {
        $(".summernote").summernote({
            height: 150,
            placeholder: _l("web.user.description_placeholder"),
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough", "superscript", "subscript"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]]
            ]
        });
    }

    function initValidation() {
        "use strict";

        const getCsrfToken = () => $("meta[name='csrf-token']").attr("content");

        const ajaxRequest = (url, data, successCallback, errorCallback) => {
            $.ajax({
                type: "POST",
                url,
                data,
                processData: false,
                contentType: false,
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken()
                },
                success: successCallback,
                error: errorCallback
            });
        };

        const handleValidationError = (error, isEdit = false) => {
            $(".error-text, .error-message").text("");
            $(".form-control").removeClass("is-invalid is-valid");

            if (error.responseJSON?.code === 422) {
                $.each(error.responseJSON.errors, (key, val) => {
                    const errorId = isEdit ? `${key}Error` : `${key}_error`;
                    $(`#${key}`).addClass("is-invalid");
                    $(`#${errorId}`).text(val[0]);
                });
            } else {
                showToast("error", error.responseJSON?.message || "An error occurred");
            }
        };

        const toggleButtonState = ($btn, text, disabled = false) => {
            $btn.text(text).prop("disabled", disabled);
        };

        const resetFormFields = ($form) => {
            $form[0].reset();
            $(".form-control").removeClass("is-invalid is-valid");
        };

        const initFormValidation = (selector, url, successBtnText, isEdit = false) => {
            const handleAjaxSuccess = (resp, form, $submitBtn) => {
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    if (!isEdit) {
                        $("#add_ticket").modal("hide");
                        resetFormFields($(form));
                    } else {
                        $("#edit_ticket").modal("hide");
                    }
                    toggleButtonState($submitBtn, successBtnText);
                    TicketTable();
                }
            };

            const handleAjaxError = (error, $submitBtn) => {
                handleValidationError(error, isEdit);
                toggleButtonState($submitBtn, successBtnText);
            };

            const submitFormHandler = (form) => {
                const formData = new FormData(form);
                const $submitBtn = $(".submitbtn");
                toggleButtonState($submitBtn, _l("web.user.plz_wait"), true);

                ajaxRequest(url, formData,
                    (resp) => handleAjaxSuccess(resp, form, $submitBtn),
                    (error) => handleAjaxError(error, $submitBtn)
                );
            };

            $(selector).validate({
                rules: {
                    category: { required: !isEdit },
                    priority: { required: !isEdit },
                    description: { required: !isEdit, maxlength: 600 },
                    "document[]": {
                        accept: "application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    },
                    assign_staff: { required: false },
                    status: { required: isEdit },
                    reply: { required: isEdit, maxlength: 60 }
                },
                messages: {
                    category: { required: _l("web.user.subject_required") },
                    priority: { required: _l("web.user.priority_required") },
                    description: {
                        required: _l("web.user.description_required"),
                        maxlength: _l("web.user.desc_max_60_words")
                    },
                    "document[]": { accept: _l("web.user.ticket_doc_extension") },
                    assign_staff: { required: _l("web.user.select_staff_member") },
                    status: { required: _l("web.user.select_status") },
                    reply: {
                        required: _l("web.user.plz_enter_reply"),
                        maxlength: _l("web.user.desc_max_60_words")
                    }
                },
                errorPlacement(error, element) {
                    const errorId = isEdit ? `${element.attr("id")}Error` : `${element.attr("id")}_error`;
                    $(`#${errorId}`).text(error.text());
                },
                highlight(element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight(element) {
                    const $el = $(element);
                    $el.removeClass("is-invalid").addClass("is-valid");
                    const errorId = isEdit ? `${$el.attr("id")}Error` : `${$el.attr("id")}_error`;
                    $(`#${errorId}`).text("");
                },
                onkeyup(element) { $(element).valid(); },
                onchange(element) { $(element).valid(); },
                submitHandler: submitFormHandler
            });
        };

        // Initialize both forms
        initFormValidation("#addTicket", "/user/ticket/store", _l("web.user.create"));
        initFormValidation("#editTicketstatus", "/ticket/update", _l("web.user.update"), true);
    }

    function TicketTable() {
        $.ajax({
            url: "/ticket/list",
            type: "GET",
            beforeSend: function () {
                $(".table-loader").removeClass("d-none");
                $(".real-table").addClass("d-none");
            },
            success: function (response) {
                const data = response.data || [];
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#ticketTable")) {
                    $("#ticketTable").DataTable().destroy();
                }

                if (data.length > 0) {
                    data.forEach(function (value) {
                        const ticketId = value.ticket_id || "N/A";
                        const subject = value.subject;
                        const createdAt = value.formatted_created_at;
                        const assignee = value.assignee

                        const assigneeName =
                            value.assignee?.user_detail
                                ? `${value.assignee.user_detail.first_name} ${value.assignee.user_detail.last_name}`
                                : value.assignee?.name ?? _l("web.user.unassigned");

                        const assigneeImage =
                            value.assignee?.user_detail?.profile_image
                                ? `/storage/${value.assignee.user_detail.profile_image}`
                                : "/backend/assets/img/default-profile.png";


                        const priorityMap = {
                            High: "danger",
                            Medium: "primary",
                            Low: "success"
                        };
                        const priority = value.priority || "unknown";
                        const priorityClass = priorityMap[priority] || "secondary";
                        const priorityBadge = `<span class="badge badge-${priorityClass} bg-${priorityClass}-transparent ticket-badge">${_l("web.user." + priority.toLowerCase())}</span>`;

                        const statusMap = {
                            1: { class: "primary", label: "ticket_open" },
                            2: { class: "warning", label: "ticket_assigned" },
                            3: { class: "secondary", label: "ticket_in_progress" },
                            4: { class: "danger", label: "ticket_closed" }
                        };
                        const status = statusMap[value.status] || { class: "secondary", label: "unknown" };
                        const statusBadge = `<span class="badge badge-${status.class} bg-${status.class}-transparent ticket-badge">${_l("web.user." + status.label)}</span>`;

                        const assigneeHTML = assignee
                            ? `<div class="d-flex align-items-center">
                                <div class="avatar me-2 flex-shrink-0">
                                    <img src="${assigneeImage}" class="rounded-circle" alt="profile">
                                </div>
                                <h6><span class="fs-14 fw-semibold">${assigneeName}</span></h6>
                            </div>`
                            : "<div class='d-flex justify-content-center w-50'><h6>-</h6></div>";

                        const descriptionEscaped = value.description.replace(/"/g, "&quot;");

                        tableBody += `<tr>
                            <td>#${ticketId}</td>
                            <td>${subject}</td>
                            <td>${createdAt}</td>
                            <td>${priorityBadge}</td>
                            <td>${assigneeHTML}</td>
                            <td>${statusBadge}</td>
                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button type="button" class="dropdown-item rounded-1 view-reply-btn"
                                            data-id="${value.id}"
                                            data-assignee-id="${value.assignee_id}"
                                            data-subject="${subject}"
                                            data-priority="${value.priority}"
                                            data-status="${value.status}"
                                            data-reply='${JSON.stringify(value.reply_description)}'
                                            data-description="${descriptionEscaped}"
                                            data-ticket-data='${JSON.stringify(value.ticket_histories)}'
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit_ticket">
                                            <i class="feather-edit me-1"></i>${_l("web.common.view_reply")}
                                        </button>
                                        <button type="button" class="dropdown-item delete-ticket-btn"
                                            data-id="${value.id}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete_ticket">
                                            <i class="feather-trash-2"></i> ${_l("web.common.delete")}
                                        </button>
                                        <button type="button" class="dropdown-item rounded-1 d-none show-ticket-history"
                                            data-id="${value.id}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#histroy_ticket">
                                            <i class="feather-eye me-1"></i> ${_l("web.user.history")}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    tableBody = `<tr><td colspan="7" class="text-center">${_l("web.common.empty_table")}</td></tr>`;
                    $(".table-footer").empty();
                }

                $("#ticketTable tbody").html(tableBody);

                if (data.length > 0) {
                    $("#ticketTable").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

                            const wrapper = $(this).closest(".dataTables_wrapper");
                            const info = wrapper.find(".dataTables_info");
                            const pagination = wrapper.find(".dataTables_paginate");

                            $(".table-footer").empty().append(
                                $("<div>").addClass("d-flex justify-content-between align-items-center w-100")
                                    .append($("<div>").addClass("datatable-info").append(info.clone(true)))
                                    .append($("<div>").addClass("datatable-pagination").append(pagination.clone(true)))
                            );

                            $(".table-footer .dataTables_paginate").removeClass("d-none");
                        }
                    });
                }
            },
            error: function (error) {
                const msg = error.responseJSON?.error || "An error occurred while retrieving ticket data!";
                showToast("error", msg);
            },
            complete: function () {
                $(".table-loader").addClass("d-none");
                $(".real-table").removeClass("d-none");
            }
        });
    }

    function showTicketHistory(ticketId, ticketHistory = []) {
        const $historyContainer = $(".ticket_histroy").empty();

        if (!ticketHistory || ticketHistory.length === 0) {
            $historyContainer.append(
                $("<p>").addClass("text-center ticket_no_data").text(_l("web.common.empty_table"))
            );
            return;
        }

        ticketHistory.forEach(function (history) {
            const userDetail = history.user?.user_detail;
            const userImage = userDetail?.profile_image
                ? "/storage/" + (userDetail.profile_image)
                : "/backend/assets/img/profiles/avatar-01.jpg";

            const firstName = userDetail?.first_name || "";
            const lastName = userDetail?.last_name || "";
            const userName = firstName && lastName ? firstName + " " + lastName : (history.user?.name || "Unknown User");

            const createdAt = new Date(history.created_at).toLocaleString();
            const description = history.description || "";

            const $commentItem = $("<div>").addClass("comment-item mt-3");
            const $userInfo = $("<div>").addClass("d-flex align-items-center mb-1");

            const $avatarImg = $("<img>", {
                src: userImage,
                alt: "User Profile Image",
                class: "img-fluid rounded-circle"
            });

            const $avatar = $("<span>").addClass("avatar avatar-l me-2 flex-shrink-0").append($avatarImg);

            const $userDetails = $("<div>").append(
                $("<h6>").addClass("mb-1").text(userName),
                $("<p>").append(
                    $("<i>").addClass("ti ti-calendar-bolt me-1"),
                    document.createTextNode(_l("web.common.updated_on") + " " + createdAt)
                )
            );

            $userInfo.append($avatar, $userDetails);
            const $commentText = $("<div>").addClass("border-bottom p-2").append($("<p>").text(description));

            $commentItem.append($userInfo, $commentText);
            $historyContainer.append($commentItem);
        });
    }

    function populateEditForm(ticketId, assigneeId, categoryId, priority, status, reply, description) {
        $("#editTicketstatus").attr("data-ticket-id", ticketId);
        $("#ticketid").val(ticketId);
        $("#assignStaff").val(assigneeId).trigger("change");
        $("#category").val(categoryId);
        $("#priority").val(priority).trigger("change");
        $("#status").val(status).trigger("change");

        let plainText = $("<div>").html(description).text();
        plainText = plainText.charAt(0).toUpperCase() + plainText.slice(1);
        $(".description").text(plainText);
        $("#reply").summernote("code", "");
    }

    function deleteTicket(id) {
        $("#delete_id").val(id);
    }
})();