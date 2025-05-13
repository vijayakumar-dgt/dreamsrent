(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();

$(document).ready(function() {
    initTable();
    $('.custom_date_picker').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: 'fas fa-angle-right',
            previous: 'fas fa-angle-left'
        },
        minDate: moment().startOf('day')
    });

    $('#vehicle_id').select2({
        dropdownParent: $("#maintenance_modal"),
    });

    $('#sort_by_date').val('');
    
    $("#maintenanceForm").validate({
        rules: {
            vehicle_id: {
                required: true,
            },
            odometer: {
                required: true,
            },
            start_date: {
                required: true,
            },
            end_date: {
                required: true,
            },
            details: {
                required: true,
            },
            status: {
                required: true,
            }
        },
        messages:{
            vehicle_id: {
                required: _l('admin.rentals.vehicle_required'),
            },
            odometer: {
                required: _l('admin.rentals.odometer_required'),
            },
            start_date: {
                required: _l('admin.rentals.start_date_required'),
            },
            end_date: {
                required: _l('admin.rentals.end_date_required'),
            },
            details: {
                required: _l('admin.rentals.details_required'),
            },
            status: {
                required: _l('admin.rentals.status_required'),
            }
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            }
        },
        highlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next(".select2-container").addClass("is-invalid").removeClass('is-valid');
            }
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next(".select2-container").removeClass("is-invalid").addClass('is-valid');
            }
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
                type:"POST",
                url:"/admin/maintenance/save",
                data: formData,
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
                success:function(resp){
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                    $(".submitbtn").removeAttr("disabled").html($("#id").val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
                    if (resp.code === 200) {
                        showToast('success', resp.message);
                        $("#maintenance_modal").modal('hide');
                        $("#maintenanceTable").DataTable().ajax.reload();
                    }
                },
                error:function(error){
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                    $(".submitbtn").removeAttr("disabled").html($("#id").val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
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
    $.validator.addMethod("filesize", function (value, element, param) {
        if (element.files.length === 0) return true;
        return element.files[0].size <= param * 1024;
    }, "File size must be less than {0} KB.");
});

function initTable(sort_by_date = '') {
    $("#maintenanceTable").DataTable({
        serverSide: true,
        processing: false,
        destroy: true,
        ajax: {
            url: "/admin/maintenance/list",
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.status = $('.status_checkbox:checked').map(function() { return $(this).val(); }).get();
                d.search = $('#overall_search').val();
                d.sort_by_date = sort_by_date;
                d.sort_by = $('#sort_by_input').val();
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
                if($('#maintenanceTable').DataTable().rows().count() == 0){
                    $(".table-footer").addClass('d-none');
                } else {
                    $(".table-footer").removeClass('d-none');
                }
            },
        },
        columns: [
            {
                data: "vehicle_name",
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0"><img src="${row.vehicle_image}" alt=""></a>
                            <div>
                                <a class="d-block fw-semibold" href="javascript:void(0);">${row.vehicle_name ? row.vehicle_name : ''}</a>
                                <span class="fs-13">${row.vehicle_type ? row.vehicle_type : ''}</span>
                            </div>
                        </div>`;
                }
            },
            { data: "start_date" },
            { data: "end_date" },
            { data: "odometer", render: function(data) { return `${data} ${_l('admin.common.km')}`; } },
            {
                data: "status",
                render: function(data, type, row) {
                    let statusClass = '';
                    if (data == 1) {
                        statusClass = 'badge-soft-purple';
                    } else if (data == 2) {
                        statusClass = 'badge-soft-info';
                    } else if (data == 3) {
                        statusClass = 'badge-soft-success';
                    }
                    return `
                        <span class="badge ${statusClass} d-inline-flex align-items-center">
                            <i class="ti ti-circle-filled fs-5 me-1"></i>${row.status_text}
                        </span>`;
                }
            },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-2">
                             ${ hasPermission(permissions, 'maintenance', 'edit') ? 

                                `<li>
                                    <button type="button" class="dropdown-item rounded-1 edit-maintenance" data-id="${data}">
                                        <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                    </button>
                                </li>`:''}
                             ${ hasPermission(permissions, 'maintenance', 'delete') ? 

                                `<li>
                                    <button type="button" class="dropdown-item rounded-1 delete-maintenance" data-id="${data}" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                        <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                    </button>
                                </li>`:''}
                            </ul>
                        </div>`;
                },
                visible: hasPermission(permissions, 'maintenance', 'edit') || hasPermission(permissions, 'maintenance', 'delete')

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
            zeroRecords: _l("admin.common.no_matching_records"),
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


$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

$(document).on('click', '#apply_filter', function () {
    let pickup_location_ids = [];
    let drop_location_ids = [];
    let status = [];

    $('.pickup_location_checkbox:checked').each(function() {
        pickup_location_ids.push($(this).val());
    });

    $('.drop_location_checkbox:checked').each(function() {
        drop_location_ids.push($(this).val());
    });

    $('.status_checkbox:checked').each(function() {
        status.push($(this).val());
    });

    $('#maintenanceTable').DataTable().ajax.reload();

});

$(document).on('click', '#reset_filter', function () {
    $('#pickUpLocationList input:checkbox').prop('checked', false);
    $('#dropOffLocationList input:checkbox').prop('checked', false);
    $('#statusList input:checkbox').prop('checked', false);
    $('#sort_by_date').val('').trigger('change');
    $('#sort_by_input').val('');
    $('#maintenanceTable').DataTable().ajax.reload();
});

$('#overall_search').on('keyup', function(e) {
    $('#maintenanceTable').DataTable().ajax.reload();
});

$(document).on('click', '.sort_by_list .dropdown-item', function () {
    let sortBy = $(this).data('sort');
    $('#sort_by_input').val(sortBy);
    $('#current_sort').text(
        sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase()
    );
    $('.sort_by_list .dropdown-item').removeClass('active');
    $(this).addClass('active');
    $('#maintenanceTable').DataTable().ajax.reload();
});

$('#sort_by_date').on('change', function() {
    let sort_by_date = $(this).val();
    initTable(sort_by_date);
});

$("#add_maintenance").on('click', function() {
    $(".modal-title").text(_l('admin.rentals.create_maintenance'));
    $(".submitbtn").text(_l('admin.common.create_new'));
    $("#maintenanceForm")[0].reset();
    $('#vehicle_id').val('').trigger('change');
    $('#status').val('').trigger('change');
    $("#id").val('');
    $(".error-text").text("");
    $(".form-control, .select2-container").removeClass("is-invalid is-valid");
});

$("#odometer").on("input", function () {
    $(this).val($(this).val().replace(/[^0-9]/g, "").slice(0, 8));
});
$('#vehicle_id').on('change', function () {
    $(this).valid();
});
$('#status').on('change', function () {
    $(this).valid();
});

$(document).on('click', '.delete-maintenance', function() {
    let id = $(this).data('id');
    $("#delete_id").val(id);
});

$("#maintenanceDeleteForm").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/maintenance/delete",
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
                $("#delete-modal").modal('hide');
                $('#maintenanceTable').DataTable().ajax.reload();
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

$(document).on('click', '.edit-maintenance', function() {
    let id = $(this).data('id');
    $.ajax({
        type:"GET",
        url:"/admin/maintenance/edit/"+id,
        success: function(response) {
            $(".error-text").text("");
            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
            if(response.code === 200){
                let data = response.data;
                $("#odometer").val(data.odometer);
                $("#start_date").val(data.start_date);
                $("#end_date").val(data.end_date);
                $("#details").val(data.details);
                $("#status").val(data.status).trigger('change');
                $("#vehicle_id").val(data.vehicle_id).trigger('change');
                $("#id").val(data.id);
                $("#maintenance_modal .modal-title").text(_l('admin.rentals.edit_maintenance'));
                $(".submitbtn").text(_l('admin.common.save_changes'));
                $("#maintenance_modal").modal('show');
            }
        }
    });
});

}) ();