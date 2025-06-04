
(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, support');

$(document).ready(function () {

    $('.summernote').summernote({
        height: 150,
        placeholder: 'Type your Reply here...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    ticketDetails();
});
$("#editTickets").validate({
    rules: {
        assign_staff: {
            required: false
        },
        status: {
            required: true
        },
        reply: {
            required: true,
            maxlength: 60
        }
    },
    messages: {
        assign_staff: {
            required: "Please select a staff member"
        },
        status: {
            required: "Please select a status"
        },
        reply: {
            required: "Please enter a reply",
            maxlength: "Reply must be a maximum of 60 words"
        }
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
        let ticketId = localStorage.getItem("ticketId");

        if (!ticketId) {
            showToast('error', 'Ticket ID not found. Please refresh and try again.');
            return;
        }

        $('#reply').val($('.summernote').summernote('code'));

        let editData = new FormData(form);
        editData.append("ticketid", ticketId);

        $.ajax({
            type: "POST",
            url: "/admin/ticket/update",
            data: editData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $('.send_reply_btn').attr('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.sending')}..
                `);
            },
            complete: function () {
                $('.send_reply_btn').attr('disabled', false).html(_l('admin.support.send_reply'));
            },
            success: function (resp) {
                if (resp.code === 200) {
                    showToast('success', resp.message);
                    $("#edit_ticket").modal("hide");
                    $("#editTickets")[0].reset();
                    $('.summernote').summernote('code', '');
                    ticketDetails();
                    window.location.href = '/admin/ticket';
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
                    showToast('error', error.responseJSON.message || "Something went wrong.");
                }
            }
        });
    }


});

function ticketDetails() {
    let ticketId = localStorage.getItem("ticketId");
    if (!ticketId) {
        console.warn("No ticketId found in localStorage. Skipping API call.");
        return;
    }
    $.ajax({
        url: "/admin/ticket/list",
        type: "GET",
        data: {
            ticketId: ticketId
        },
        success: function (response) {
            let ticket = response.data[0];

            if (!ticket) {
                showToast('error', 'No ticket found');
                return;
            }

            $(".ticket_id").text(`#${ticket.ticket_id}`);
            $(".category_name").text(ticket.category?.name || '');
            $(".user_name").text(`${ticket.user?.user_detail?.first_name || ''} ${ticket.user?.user_detail?.last_name || ''}`);
            $(".Priority").text(ticket.priority);
            $(".assigne_name").text(ticket.assignee?.user_detail?.first_name || 'Unassigned');
            $(".created_at").text(ticket.formatted_created_at);
            $(".update_at").text(ticket.formatted_updated_at);

            let cleanDescription = DOMPurify.sanitize(ticket.description || '');
            $(".ticket_description").html(cleanDescription);

            $("#status").val(ticket.status).trigger('change');

            const attachmentContainer = $(".attachmentContainer").empty();

            if (ticket.attachment) {
                let attachments = [];

                try {
                    attachments = JSON.parse(ticket.attachment);
                } catch (e) {
                    attachments = ticket.attachment.split(',');
                }

                attachments.forEach(file => {
                    file = file.replace(/\\/g, '/');
                    const fileUrl = `/storage/${file}`;
                    const fileName = file.split('/').pop();
                    const ext = fileName.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                    const isPdf = ext === 'pdf';

                    const attachmentDiv = $('<div>').addClass('bg-light br-5 p-3 d-flex align-items-center border mb-2');
                    const avatarSpan = $('<span>').addClass('avatar bg-white d-flex align-items-center justify-content-center me-2');

                    if (isPdf) {
                        $('<img>', {
                            src: '/backend/assets/img/icons/pdf.svg',
                            alt: 'PDF File',
                            class: 'w-10 h-10'
                        }).appendTo(avatarSpan);
                    }

                    if (isImage) {
                        $('<img>', {
                            src: fileUrl,
                            alt: 'Image File',
                            class: 'w-10 h-10 rounded'
                        }).appendTo(avatarSpan);
                    }

                    const fileDetailsDiv = $('<div>').addClass('me-2');
                    $('<h6>').addClass('fs-14 fw-medium mb-0').text(fileName).appendTo(fileDetailsDiv);
                    $('<p>').addClass('fs-12 mb-0').text(`${ext.toUpperCase()} File`).appendTo(fileDetailsDiv);

                    const downloadLink = $('<a>', {
                        href: fileUrl,
                        target: '_blank',
                        rel: 'noopener noreferrer',
                        class: 'ms-auto btn btn-sm btn-primary d-flex align-items-center'
                    });

                    $('<i>').addClass('ti ti-download fs-16 me-1').appendTo(downloadLink);
                    downloadLink.append(document.createTextNode(_l('admin.common.download')));

                    attachmentDiv.append(avatarSpan, fileDetailsDiv, downloadLink);
                    attachmentContainer.append(attachmentDiv);
                });
            } else {
                attachmentContainer.append($('<p>').text(_l('admin.support.no_attachment_found')));
            }

            const historyContainer = $(".ticket_histroy").empty();

            if (!ticket.ticket_histories?.length) {
                historyContainer.append($('<p>').addClass('text-center').text(_l('admin.common.no_history_found')));
                return;
            }

           ticket.ticket_histories.forEach(history => {
                const rawImage = history.user?.user_detail?.profile_image || '';
                const isValidImage = /^[\w\-./]+$/.test(rawImage);
                const userImage = isValidImage
                    ? `/storage/${rawImage.replace(/\\/g, '/')}`
                    : '/backend/assets/img/default-profile.png';

                const userName = history.user?.user_detail?.first_name && history.user?.user_detail?.last_name
                    ? `${history.user.user_detail.first_name} ${history.user.user_detail.last_name}`
                    : (history.user?.name || "Unknown User");

                const createdAt = new Date(history.created_at).toLocaleString();

                const commentItem = $('<div>').addClass('comment-item mt-3');
                const userInfo = $('<div>').addClass('d-flex align-items-center mb-1');

                const avatarImg = $('<img>', {
                    src: userImage,
                    alt: 'User Profile Image',
                    class: 'img-fluid rounded-circle'
                });

                const avatar = $('<span>')
                    .addClass('avatar avatar-l me-2 flex-shrink-0')
                    .append(avatarImg);

                const userDetails = $('<div>').append(
                    $('<h6>').addClass('mb-1').text(userName),
                    $('<p>').html(`<i class="ti ti-calendar-bolt me-1"></i> ${_l('admin.common.updated_on')} ${createdAt}`)
                );

                const commentText = $('<div>').addClass('border-bottom p-2').append(
                    $('<p>').text(history.description)
                );

                userInfo.append(avatar, userDetails);
                commentItem.append(userInfo, commentText);
                historyContainer.append(commentItem);
            });

            const statusMap = {
                1: 'Open',
                2: 'Assigned',
                3: 'In Progress',
                4: 'Closed'
            };

            $(".status-text").text(statusMap[ticket.status] || 'Unknown');
        },
        error: function (error) {
            showToast('error', error.responseJSON?.error || "An error occurred while retrieving tickets!");
        },
        complete: function () {
            $('.skeleton').remove();
            $('.real-label').removeClass('d-none');
            $(".table-loader, .input-loader, .label-loader").hide();
            $('.real-table, .real-label, .real-input').removeClass('d-none');
        }
    });
}

}) ();
