(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, others');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initEvents();
        initTable();
    });

    function initTable() {
        $("#newsletterTable").DataTable({
            serverSide: true,
            processing: false,
            destroy: true,
            ajax: {
                url: "/admin/newsletter/list",
                type: "POST",
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.search = $('#search').val();
                    d.sort_by = $('.sort_by_list .dropdown-item.active').data('sort');
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.code === 500) {
                        showToast('error', error.responseJSON.message);
                    } else {
                        showToast('error', _l('admin.common.default_retrieve_error'));
                    }
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $('.real-table, .table-footer').addClass('d-none');
                },
                complete: function() {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $('.real-table, .real-label, .real-input').removeClass('d-none');
                    if($('#newsletterTable').DataTable().rows().count() == 0){
                        $(".table-footer").addClass('d-none');
                    } else {
                        $(".table-footer").removeClass('d-none');
                    }
                },
            },
            columns: [
                { data : "id", orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="form-check form-check-md">
                                <input class="form-check-input select-multiple" type="checkbox" value="${row.id}" data-email="${row.email}">
                            </div>`;
                    },
                },
                { data: "email" },
                { data: "created_at",
                    render: function(data, type, row) {
                        return `${row.created_date}`;
                    }
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                 ${hasPermission(permissions, 'newsletters', 'delete') ?
                                   `<li>
                                   <button 
                                        type="button" 
                                        class="dropdown-item rounded-1 delete-newsletter-btn" 
                                        data-id="${row.id}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#delete_modal">
                                        <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                    </button>
                                    </li>`:''}
                                </ul>
                            </div>`;
                    },
                    visible: hasPermission(permissions, 'newsletters', 'delete') 
                }
            ],
            order: [[0, "asc"]],
            ordering: true,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            responsive: true,
            autoWidth: false,
            language: {
                emptyTable: _l("admin.common.empty_table"),
                info: _l("admin.common.showing") + " _START_ " + _l("admin.common.to") + " _END_ " + _l("admin.common.of") + " _TOTAL_ " + _l("admin.common.entries"),
                infoEmpty: _l("admin.common.showing") + " 0 " + _l("admin.common.to") + " 0 " + _l("admin.common.of") + " 0 " + _l("admin.common.entries"),
                infoFiltered: "(" + _l("admin.common.filtered_from") + " _MAX_ " + _l("admin.common.total_entries") + ")",
                lengthMenu: _l("admin.common.show") + " _MENU_ " + _l("admin.common.entries"),
                search: _l("admin.common.search") + ":",
                zeroRecords: _l("admin.common.empty_table"),
                paginate: {
                    first: _l("admin.common.first"),
                    last: _l("admin.common.last"),
                    next: _l("admin.common.next"),
                    previous: _l("admin.common.previous"),
                },
            },
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
            }
        });
    }

    function initEvents() {
        $(document).on('click', '.dataTables_paginate a', function() {
            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
        });

        $('#search').on('keyup', function(e) {
            $('#newsletterTable').DataTable().ajax.reload();
        });

        $(document).on('click', '.sort_by_list .dropdown-item', function () {
            let sortBy = $(this).data('sort');
            $('#sort_by_input').val(sortBy);
            $('#current_sort').text(
                sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase()
            );
            $('.sort_by_list .dropdown-item').removeClass('active');
            $(this).addClass('active');
            $('#newsletterTable').DataTable().ajax.reload();
        });

        $(document).on('click', '.delete-newsletter-btn', function () {
            const id = $(this).data('id');
            $("#delete_id").val(id);
        });

        $("#newsletterDeleteForm").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/newsletter/delete",
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
                        $("#delete_modal").modal('hide');
                        $('#newsletterTable').DataTable().ajax.reload();
                    }
                },
                error: function(res) {
                    if(res.responseJSON.code === 500){
                        showToast('success', res.responseJSON.message);
                    } else {
                        showToast('error', _l('admin.common.default_delete_error'));
                    }
                }
            });
        });

        $('#select-all').on('change', function () {
            $('.select-multiple').prop('checked', $(this).prop('checked'));
        });

        $('#send_newsletter').on('click', function () {
            let selectedEmails = [];

            $('.select-multiple:checked').each(function () {
                var email = $(this).data('email');
                if (email) {
                    selectedEmails.push(email);
                }
            });

            if (selectedEmails.length === 0) {
                showToast('error', _l('admin.common.select_atleast_one_item'));
                return;
            }

            $.ajax({
                url: '/admin/send-newsletter', 
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    email: selectedEmails,
                },
                beforeSend: function () {
                    $('#send_newsletter').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle me-2" role="status" aria-hidden="true"></span> ${_l('admin.common.sending')}..
                    `);
                },
                success: function (response) {
                    if(response.code === 200){
                        $('#send_newsletter').prop('disabled', false).html(`<i class="ti ti-mail me-2"></i>`+_l('admin.others.send_newsletter'));
                        showToast('success', response.message);
                        $('#select-all').prop('checked', false);
                    }
                },
                error: function (res) {
                    $('#send_newsletter').prop('disabled', false).html(`<i class="ti ti-mail me-2"></i>`+_l('admin.others.send_newsletter'));
                    $('#select-all').prop('checked', false);
                    if(res.responseJSON.code === 500){
                        showToast('error', res.responseJSON.message);
                    } else {
                        showToast('error', _l('admin.common.default_status_error'));
                    }
                },
            });
        });
    }
}) ();