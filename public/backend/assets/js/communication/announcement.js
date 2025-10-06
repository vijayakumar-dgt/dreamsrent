(async () => {
    "use strict";
    await loadTranslationFile('admin', 'support,common');
    const permissions = await loadUserPermissions();

    $(document).ready(function() {
        announcementTable();
        initEvents();
        initSummernote();
        initValidation();
        function initEvents() {
            $(document).on('click', '.delete-announcement-btn', function () {
                const id = $(this).data('id');
                deleteAnnouncement(id);
            });

            $("#add_announcement").on('click', function() {
                $("#announcementForm")[0].reset();
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                $('#user_type').val('').trigger('change');
            });

            $(document).on('click', '.edit_data', function (e) {
                e.preventDefault();

                const announcementId = $(this).data('id');

                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (!announcementId) return;

                $.ajax({
                    url: "/admin/announcement/list",
                    type: "GET",
                    data: { id: announcementId },
                    success: handleAnnouncementSuccess,
                    error: handleAnnouncementError
                });
            });

            $("#annoncementDeleteForm").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url:"/admin/annoncement/delete",
                    type:"POST",
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
                $("#delete_announcement_modal").modal('hide');
                announcementTable();
            }
        }

        function handleDeleteError(res) {
            if (res.responseJSON?.code === 500) {
                showToast('error', res.responseJSON.message);
            } else {
                showToast('error', _l('admin.common.default_delete_error'));
            }
        }

        function handleAnnouncementSuccess(response) {
            if (!response.success) {
                return;
            }

            const data = response.data;

            $('#id').val(data.id);
            $('#edit_announcement_title').val(data.announcement_title);
            $('#edit_user_type').val(data.user_type).trigger('change');
            $('#edit_description').summernote('code', data.description);
            $('#status').prop('checked', data.status == 1);
        }

        function handleAnnouncementError(xhr) {
            if (xhr.responseJSON?.message) {
                showToast('error', xhr.responseJSON.message);
            } else {
                showToast('error', 'Something went wrong while fetching announcement.');
            }
        }

        function initSummernote() {
            $('#description').summernote({
                height: 300, // Editor height
                minHeight: 150, // Minimum height
                maxHeight: 500, // Maximum height
                focus: true, // Set focus on load
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('#edit_description').summernote({
                height: 300, // Editor height
                minHeight: 150, // Minimum height
                maxHeight: 500, // Maximum height
                focus: true, // Set focus on load
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        function initValidation(){
            $("#announcementForm").validate({
                rules: {
                    announcement_title: {
                        required: true,
                        maxlength: 100,
                    },
                    user_type: {
                        required: true,
                    },
                    description: {
                        required: true,
                        maxlength: 500,
                    },
                },
                messages: {
                    announcement_title: {
                        required: _l('admin.support.announcement_title_required'),
                        maxlength: _l('admin.support.announcement_title_maxlength'),
                    },
                    user_type: {
                        required: _l('admin.support.user_type_required'),
                    },
                    description: {
                        required: _l('admin.cms.description_required'),
                        maxlength: _l('admin.support.description_maxlength'),
                    },
                },
                errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                },
                highlight: function (element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                    const errorId = element.id + "_error";
                    $("#" + errorId).text("");
                },
                onkeyup: function(element) {
                    $(element).valid();
                },
                onchange: function(element) {
                    $(element).valid();
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);

                    $.ajax({
                        type: "POST",
                        url: "/admin/announcement/save",
                        data: formData,
                        enctype: "multipart/form-data",
                        processData: false,
                        contentType: false,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: handleBeforeSend,
                        complete: handleComplete,
                        success: handleSaveSuccess,
                        error: handleSaveError
                    });
                }
            });

            $("#editAnnouncementForm").validate({
                rules: {
                    edit_announcement_title: {
                        required: true,
                        maxlength: 100,
                    },
                    edit_user_type: {
                        required: true,
                    },
                    edit_description: {
                        required: true,
                        maxlength: 500,
                    },
                },
                messages: {
                    edit_announcement_title: {
                        required: _l('admin.support.announcement_title_required'),
                        maxlength: _l('admin.support.announcement_title_maxlength'),
                    },
                    edit_user_type: {
                        required: _l('admin.support.user_type_required'),
                    },
                    edit_description: {
                        required: _l('admin.cms.description_required'),
                        maxlength: _l('admin.support.description_maxlength'),
                    },
                },
                errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                },
                highlight: function (element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                    const errorId = element.id + "_error";
                    $("#" + errorId).text("");
                },
                onkeyup: function(element) {
                    $(element).valid();
                },
                onchange: function(element) {
                    $(element).valid();
                },
                submitHandler: function(form) {
                    let formData = new FormData();
                        formData.append('id', $('#id').val());
                        formData.append('announcement_title', $('#edit_announcement_title').val());
                        formData.append('user_type', $('#edit_user_type').val());
                        formData.append('description', $('#edit_description').val());
                        formData.append('status', $('#status').prop('checked') ? 1 : 0);
                    $.ajax({
                        type: "POST",
                        url: "/admin/announcement/save", // Same endpoint as 'announcementForm'
                        data: formData,
                        enctype: "multipart/form-data",
                        processData: false,
                        contentType: false,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function () {
                            $('.savebtn').attr('disabled', true).html(`
                                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                            `);
                        },
                        complete: function () {
                            $('.savebtn').attr('disabled', false).html(_l('admin.common.save_changes'));
                        },
                        success: function(resp) {
                            $(".error-text").text("");
                            $(".form-control").removeClass("is-invalid is-valid");
                            if (resp.code === 200) {
                                showToast('success', resp.message);
                                $("#edit_announcement_modal").modal('hide');
                                announcementTable();
                            }
                        },
                        error: function(error) {
                            $(".error-text").text("");
                            $(".form-control").removeClass("is-invalid is-valid");
                            if (error.responseJSON.code === 422) {
                                $.each(error.responseJSON.errors, function(key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                });
                            } else {
                                showToast('error', error.responseJSON.message);
                            }
                        }
                    });
                }
            });
        }

        function handleBeforeSend() {
            $('.submitbtn')
                .attr('disabled', true)
                .html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                `);
        }

        function handleComplete() {
            $('.submitbtn')
                .attr('disabled', false)
                .html(_l('admin.common.create_new'));
        }

        function handleSaveSuccess(resp) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");

            if (resp.code === 200) {
                showToast('success', resp.message);
                $("#add_announcement_modal").modal('hide');
                announcementTable();
            }
        }

        function highlightValidationErrors(errors) {
            $.each(errors, function (key, val) {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        }

        function handleSaveError(error) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");

            if (error.responseJSON?.code === 422) {
                highlightValidationErrors(error.responseJSON.errors);
            } else {
                showToast('error', error.responseJSON?.message || _l('admin.common.default_error'));
            }
        }

        function announcementTable() {
            const sort = $('#sortDropdownBtn').attr('data-sort') || 'latest';
            const status = $('#statusDropdownBtn').attr('data-status') || 'all';
            const search = $('#announcementSearch').val();

            $.ajax({
                url: "/admin/announcement/list",
                type: "GET",
                data: {
                    sort,
                    status,
                    title: search
                },
                beforeSend: showTableLoader,
                complete: hideTableLoader,
                success: (response) => handleListSuccess(response, permissions),
                error: handleListError,
            });
        }

        $(document).on('click', '.sort-filter', function () {
            $('.sort-filter').removeClass('active');
            $(this).addClass('active');
            $('#currentSort').text($(this).text());
            $('#sortDropdownBtn').attr('data-sort', $(this).data('sort')); // Add this line
            announcementTable();
        });

        $(document).on('click', '.status-filter', function () {
            $('.status-filter').removeClass('active');
            $(this).addClass('active');
            $('#currentStatus').text($(this).text());
            $('#statusDropdownBtn').attr('data-status', $(this).data('status')); // Add this line
            announcementTable();
        });

        $('#announcementSearch').on('input', function () {
            clearTimeout($.data(this, 'timer'));
            let wait = setTimeout(announcementTable, 300); // debounce
            $(this).data('timer', wait);
        });

        function deleteAnnouncement(id){
            $("#delete_id").val(id);
        }
    });
})();