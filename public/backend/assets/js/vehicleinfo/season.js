(async () => {
    "use strict";
    await loadTranslationFile('admin', 'rentals,common');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initFormValidation() {
        $("#seasonForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
            },
            messages: {
                name: {
                    required: _l('admin.rentals.season_name_required'),
                    minlength: _l('admin.rentals.season_name_minlength'),
                    maxlength: _l('admin.rentals.season_name_maxlength'),
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
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let seasonFormData = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: "/admin/store_season",
                    data: seasonFormData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('.submitbtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.submitbtn').attr('disabled', false).html($("#id").val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $("#add_season").modal('hide');
                            initTable();
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
    }

    function initTable() {
        let keyword = $('#keyword').val();
        let status = $(".statusfilter.active").data('status');
        $.ajax({
            url: "/admin/get_seasons",
            type: "GET",
            data: { keyword: keyword, status: status },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#seasonTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#seasonTable")) {
                    $("#seasonTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        tableBody += `<tr>
                                <td><h6 class="fw-medium text-black">${value.name}</h6></td>
                                <td><span class="badge ${value.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>${value.status == 1 ? `${_l('admin.common.active')}` : `${_l('admin.common.inactive')}` }
                                    </span>
                                </td>
                                ${hasPermission(permissions, 'vehicle_attributes', 'edit') || hasPermission(permissions, 'vehicle_attributes', 'delete') ?
                                `<td>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${hasPermission(permissions, 'vehicle_attributes', 'edit') ?
                                            `<li>
                                                <button type="button" class="dropdown-item rounded-1 editSeason" data-id="${value.id}">
                                                    <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                                </button>
                                            </li>`: ''}
                                            ${hasPermission(permissions, 'vehicle_attributes', 'delete') ?
                                            `<li>
                                                <button type="button" class="dropdown-item rounded-1 deleteSeason" data-id="${value.id}" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                                    <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                                </button>
                                            </li>`: ''}
                                        </ul>
                                    </div>
                                </td>`: ''}
                            </tr>`;
                    });

                } else {
                    tableBody += `
                            <tr>
                                <td colspan="4" class="text-center">${_l('admin.common.empty_table')}</td>
                            </tr>`;
                    $('.table-footer').empty();
                }
                $("#seasonTable tbody").html(tableBody);
                if ((response.data.length > 0)) {
                    $('#seasonTable').DataTable({
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
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast('error', error.responseJSON.message);
                } else {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                }
            }
        });
    }

    function initEvents() {
        $(document).on('input','#keyword', function(){
            initTable(); 
        });
    
        $(document).on("click", ".statusfilter", function () {
            let statusFilter = $(this).data('status');
            $(".statusfilter").removeClass("active");
            $(this).addClass("active");
            if (statusFilter == 1) {
                $("#status_text").text(_l('admin.common.active'));
            } else if (statusFilter == 0) {
                $("#status_text").text(_l('admin.common.inactive'));
            } else {
                $("#status_text").text(_l('admin.common.status'));
            }
            initTable();
        });
    
        $(document).on('click', '#add_new_season', function () {
            $("#add_season .modal-title").text(_l('admin.rentals.create_season'));
            $("#add_season .submitbtn").text(_l('admin.common.create_new'));
            $("#seasonForm")[0].reset();
            $("#seasonForm #id").val('');
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $('#statusDiv').addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
        });
        
        $("#deleteSeason").on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/admin/delete_season",
                data: $("#deleteSeason").serialize(),
                success: function (response) {
                    if (response.code === 200) {
                        showToast('success', response.message);
                        $("#delete-modal").modal('hide');
                        initTable();
                    } else {
                        showToast('error', response.message);
                        $("#delete-modal").modal('hide');
                    }
                },
                error: function (error) {
                    showToast('error', error.responseJSON.message);
                    $("#delete-modal").modal('hide');
                }
            });
        });
        
        $(document).on('click', '.editSeason', function (e) {
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: "/admin/get_season/" + id,
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#add_season #name").val(data.name);
                        $("#add_season #id").val(data.id);
                        if (data.status === 1) {
                            $("#add_season #status").prop('checked', true);
                        } else {
                            $("#add_season #status").prop('checked', false);
                        }
                        $("#add_season .modal-title").text(_l('admin.rentals.edit_season'));
                        $("#add_season .submitbtn").text(_l('admin.common.save_changes'));
                        $("#add_season").modal('show');
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $('#statusDiv').removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function (error) {
                    showToast('error', error.responseJSON.message);
                }
            });
        });
        
        $(document).on('click', '.deleteSeason', function (e) {
            let id = $(this).data('id');
            $("#delete_id").val(id);
        });
    }
})();

    
    
    