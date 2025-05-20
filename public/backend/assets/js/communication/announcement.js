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
        
                $('#announcement_type').val('').trigger('change');
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
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;

                            $('#id').val(data.id);
                            $('#edit_announcement_title').val(data.announcement_title);
                            $('#edit_announcement_type').val(data.announcement_type).trigger('change');
                            $('#edit_user_type').val(data.user_type).trigger('change');
                            $('#edit_description').summernote('code', data.description);
                            $('#status').prop('checked', data.status == 1);

                        } else {
                            
                        }
                    },
                    error: function (xhr) {
                        showToast('error', xhr.responseJSON.message);
                    }
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
                    success: function(response) {
                        if(response.code === 200){
                            showToast('success', response.message);
                            $("#delete_announcement_modal").modal('hide');
                            window.location.reload();
                            announcementTable();
                        }
                    },
                    error: function(res) {
                        if(res.responseJSON.code === 500){
                            showToast('error', res.responseJSON.message);
                        } else {
                            showToast('error', _l('admin.common.default_delete_error'));
                        }
                    }
                });
            });
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
                    announcement_type: {
                        required: true,
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
                    announcement_type: {
                        required: _l('admin.support.announcement_type_required'),
                    },
                    user_type: {
                        required: _l('admin.support.user_type_required'),
                    },
                    description: {
                        required: _l('admin.support.description_required'),
                        maxlength: _l('admin.support.description_maxlength'),
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
                    var errorId = element.id + "_error";
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
                        beforeSend: function () {
                            $('.submitbtn').attr('disabled', true).html(`
                                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                            `);
                        },
                        complete: function () {
                            $('.submitbtn').attr('disabled', false).html(_l('admin.common.create_new'));
                        },
                        success: function(resp) {
                            $(".error-text").text("");
                            $(".form-control").removeClass("is-invalid is-valid");
                            if (resp.code === 200) {
                                showToast('success', resp.message);
                                $("#add_announcement_modal").modal('hide');
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
        
            $("#editAnnouncementForm").validate({
                rules: {
                    edit_announcement_title: {
                        required: true,
                        maxlength: 100,
                    },
                    edit_announcement_type: {
                        required: true,
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
                    edit_announcement_type: {
                        required: _l('admin.support.announcement_type_required'),
                    },
                    edit_user_type: {
                        required: _l('admin.support.user_type_required'),
                    },
                    edit_description: {
                        required: _l('admin.support.description_required'),
                        maxlength: _l('admin.support.description_maxlength'),
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
                    var errorId = element.id + "_error";
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
                        formData.append('announcement_type', $('#edit_announcement_type').val());
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
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete: function () {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass("d-none");
                },
                success: function (response) {
                    let tableBody = "";

                    if ($.fn.DataTable.isDataTable("#announcementTable")) {
                        $("#announcementTable").DataTable().destroy();
                    }

                    if (response.success && response.data.length > 0) {
                        let data = response.data;
                        $.each(data, function (index, value) {
                            tableBody += `<tr>
                                <td>${new Date(value.created_at).toLocaleDateString()}</td>
                                <td><strong>${value.announcement_title.length > 80 ? value.announcement_title.substring(0, 80) + "..." : value.announcement_title}</strong></td>
                                <td>${value.type_name}</td>
                                <td>
                                    <span class="badge ${(value.status == 1) ? 'badge-success-transparent' : 'badge-danger-transparent'} d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>${(value.status == 1) ? `${_l('admin.support.published')}` : `${_l('admin.support.unpublished')}`}
                                    </span>
                                </td>
                                ${hasPermission(permissions, 'announcements', 'edit') || hasPermission(permissions, 'announcements', 'delete') ?
                                `<td>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${hasPermission(permissions, 'announcements', 'edit') ?
                                `<li><a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_announcement_modal" class="edit_data dropdown-item"
                                    data-id="${value.id}" data-title="${value.announcement_title}" data-type="${value.announcement_type}" data-user="${value.user_type}" data-status="${value.status}">
                                    <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                </a></li>` : ''}
                                            ${hasPermission(permissions, 'announcements', 'delete') ?
                                `<li><button type="button" class="dropdown-item delete-announcement-btn" data-id="${value.id}" data-bs-toggle="modal" data-bs-target="#delete_announcement_modal">
                                    <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                </button></li>` : ''}
                                        </ul>
                                    </div>
                                </td>` : ''}
                            </tr>`;
                        });

                    } else {
                        tableBody = `<tr><td colspan="5" class="text-center">${_l('admin.common.empty_table')}</td></tr>`;
                    }

                    $("#announcementTable tbody").html(tableBody);

                    if (response.data.length > 0) {
                        $("#announcementTable").DataTable({
                            ordering: false,
                            searching: false,
                            pageLength: 10,
                            lengthChange: false,
                            drawCallback: function () {
                                $(".dataTables_info, .dataTables_paginate").addClass("d-none");
                                var info = $('.dataTables_info');
                                var pagination = $('.dataTables_paginate');
                                $('.table-footer').html(
                                    `<div class="d-flex justify-content-between align-items-center w-100">
                                        <div class="datatable-info">${info.clone(true).html()}</div>
                                        <div class="datatable-pagination">${pagination.clone(true).html()}</div>
                                    </div>`
                                );
                                $(".table-footer .dataTables_paginate").removeClass("d-none");
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
                    showToast('error', _l('admin.common.default_retrieve_error'));
                }
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