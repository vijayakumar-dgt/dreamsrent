(async () => {
    "use strict";
    await loadTranslationFile('admin', 'cms,common');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        $(document).on('click', '.edit-faq-btn', function () {
            const id = $(this).data('id');
            const question = $(this).data('question');
            const answer = $(this).data('answer');
            const status = $(this).data('status');
            const languageId = $(this).data('language-id');

            $('#editFaqQuestion').val(question);
            $('#editFaqAnswer').val(answer);
            $('#editFaqStatus').prop('checked', status == 1);
            $('#id').val(id);
            $('#editFaqLanguage').val(languageId).trigger('change');

            $('#edit_FAQ').modal('show');
        });
        $(document).on('click', '.delete-faq-btn', function () {
            const id = $(this).data('id');
            $("#delete_id").val(id);
        });

        $('#add_FAQ').on('show.bs.modal', function () {
            $('#addFaq')[0].reset();
            $('.text-danger').text('');
            $('.form-control').removeClass('is-invalid');
        });

        $("#addFaq").validate({
            rules: {
                language: {
                    required: true
                },
                question: {
                    required: true,
                    minlength: 5
                },
                answer: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                language: {
                    required: _l('admin.cms.language_required')
                },
                question: {
                    required: _l('admin.cms.question_required'),
                    minlength: _l('admin.cms.question_minlength')
                },
                answer: {
                    required: _l('admin.cms.answer_required'),
                    minlength: _l('admin.cms.answer_minlength')
                }
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
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let faqData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/faq/store",
                    data: faqData,
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
                    success: function (resp) {
                        if (resp.code === 200) {
                            faqTable();
                            showToast('success', resp.message);
                            $("#addFaq")[0].reset();
                            $('#add_FAQ').modal('hide');
                            $("#addFaq").find(".form-control").removeClass("is-valid is-invalid");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
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

        $("#editFaqForm").validate({
            rules: {
                language: {
                    required: true
                },
                question: {
                    required: true,
                    minlength: 5
                },
                answer: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                language: {
                    required: _l('admin.cms.language_required')
                },
                question: {
                    required: _l('admin.cms.question_required'),
                    minlength: _l('admin.cms.question_minlength')
                },
                answer: {
                    required: _l('admin.cms.answer_required'),
                    minlength: _l('admin.cms.answer_minlength')
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
                var errorId = element.id + "Error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let faqData = new FormData(form);
                let faqId = $("#editFaqForm input[name=faq_id]").val();

                let status = $("#editFaqStatus").is(":checked") ? 1 : 0;
                faqData.append("status", status);

                $.ajax({
                    type: "POST",
                    url: "/admin/faq/update",
                    data: faqData,
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
                    success: function (resp) {
                        if (resp.code === 200) {
                            faqTable();
                            showToast('success', resp.message);
                            $("#editFaqForm")[0].reset();
                            $('#edit_FAQ').modal('hide');
                            $("#editFaqForm").find(".form-control").removeClass("is-valid is-invalid");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
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
    });
    function faqTable(filters = {}) {
        const selectedLang = $('#language_id').val();
    filters.language_id = selectedLang;
        $.ajax({
            url: "/admin/faq/list",
            type: "GET",
            data: filters,
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            success: function(response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#faqTable")) {
                    $("#faqTable").DataTable().clear().destroy();
                }

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, value) {
                        tableBody += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a href="javascript:void(0);" class="fw-semibold">
                                            ${value.question.length > 50 ? value.question.substring(0, 50) + "..." : value.question}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-gray-9 text-truncate">
                                    ${value.answer.length > 50 ? value.answer.substring(0, 50) + "..." : value.answer}
                                </p>
                            </td>
                            <td>
                                <span class="badge badge-${value.status === 1 ? 'soft-success' : 'soft-danger'}">
                                    <i class="ti ti-point-filled"></i> ${value.status === 1 ? `${_l('admin.cms.published')}` : `${_l('admin.cms.unpublished')}`}
                                </span>
                            </td>
                            ${hasPermission(permissions, 'faq', 'edit') || hasPermission(permissions, 'faq', 'delete') ?
                            `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        ${hasPermission(permissions, 'faq', 'edit') ?
                                        `<li>
                                           <button 
                                                type="button" 
                                                class="dropdown-item rounded-1 edit-faq-btn" 
                                                data-id="${value.id}" 
                                                data-question="${value.question}" 
                                                data-answer="${value.answer}" 
                                                data-status="${value.status}" 
                                                data-language-id="${value.language_id}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#edit_FAQ">
                                                <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                            </button>
                                        </li>`:''}
                                        ${hasPermission(permissions, 'faq', 'delete') ?
                                        `<li>
                                            <button 
                                                type="button" 
                                                class="dropdown-item rounded-1 delete-faq-btn" 
                                                data-id="${value.id}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#delete_FAQ"
                                            >
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </button>
                                        </li>`:''}
                                    </ul>
                                </div>
                            </td>`:''}
                        </tr>`;
                    });
                } else {
                    tableBody = `<tr><td colspan="4" class="text-center">${_l('admin.cms.empty_table')}</td></tr>`;
                    $('.table-footer').empty();
                }

                $("#faqTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $('#faqTable').DataTable({
                        ordering: true,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function() {
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
            error: function(error) {
                showToast('error', error.responseJSON?.error || "An error occurred while retrieving the FAQ list!");
            },
            complete: function() {
                $(".table-loader, .input-loader, .label-loader").hide();
                $('.real-table, .real-label, .real-input, .table-footer').removeClass('d-none');
            },
        });
    }

    $(document).ready(function() {

        $('.sort-option').on('click', function() {
            $('.sort-option').removeClass('active');
            $(this).addClass('active');
            let sortBy = $(this).data('sort');
            faqTable({ sort_by: sortBy });
        });

        $('.filter-option').on('click', function() {
            $('.filter-option').removeClass('active');
            $(this).addClass('active');
            let status = $(this).data('status');
            faqTable({ status: status });
        });

        $('#applyFilters').on('click', function() {
            let selectedStatus = $('.filter-option.active').data('status') ?? ''; // Get active status filter
            let selectedSort = $('.sort-option.active').data('sort') ?? 'desc'; // Get active sorting option
            faqTable({ status: selectedStatus, sort_by: selectedSort });
        });

        $('#clearFilters').on('click', function() {
            $('.filter-option, .sort-option').removeClass('active');
            faqTable({});
        });

        $('#language_id').on('change', function () {
            faqTable();
        });
        faqTable();
    });


    $("#deleteFaq").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url:"/admin/faq/delete",
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
                    $("#delete_FAQ").modal('hide');
                    faqTable();
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

})();

function editFAQ(id, question, answer,status, languageId) {
    $('#editFaqQuestion').val(question);
    $('#editFaqAnswer').val(answer);
    $('#editFaqStatus').prop('checked', status == 1);
    $('#id').val(id);
    $('#editFaqLanguage').val(languageId).trigger('change');

    $('#edit_FAQ').modal('show');
}

function deleteFAQ(id){
    $("#delete_id").val(id);
}

