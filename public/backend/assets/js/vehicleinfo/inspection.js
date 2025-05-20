(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();
    const placeholders = {
        inspection_by: _l('admin.rentals.user'),
    };

    $(document).ready(function(){
        initTable();
        initSelect2();
        initFormValidation();
        initDatePicker();
        initEvents();
    });
    
    function initDatePicker() {
        $('.inspection_date').datetimepicker({
            format: 'DD-MM-YYYY',
            minDate: moment().format('YYYY-MM-DD'),
            icons: {
                up: "fas fa-angle-up",
                down: "fas fa-angle-down",
                next: 'fas fa-angle-right',
                previous: 'fas fa-angle-left'
            }
        });
    }

    function initSelect2() {
        $.each(placeholders, function(id, text) {
            $(`#${id}`).select2({
                dropdownParent: $("#add_inspection"),
                placeholder: text,
                allowClear: true
            });
        });
    }

    function initFormValidation() {
        $("#inspectionForm").validate({
            rules: {
                vehicle_info_id: {
                    required: true,
                },
                inspection_date: {
                    required: true,
                    uniqueInspection: true
                },
                inspection_by: {
                    required: true,
                },
                odometer: {
                    required: true,
                    pattern : /^[0-9]+$/,
                    min: 0,
                    max: 999999
                },
                fuel: {
                    required: true,
                    pattern : /^[0-9]+$/,
                    min: 0,
                    max: 50,
                },
                inspection_status: {
                    required: true
                },
                repair_status: {
                    required: true
                },
                'checklist_id[]': {
                    required: true,
                }
            },
            messages:{
                vehicle_info_id: {
                    required: _l('admin.rentals.vehicle_required'),
                },
                inspection_date: {
                    required: _l('admin.rentals.inspection_date_required'),
                    uniqueInspection: _l('admin.rentals.inspection_date_overlap'),
                },
                inspection_by: {
                    required: _l('admin.rentals.inspection_by_required'),
                },
                odometer: {
                    required: _l('admin.rentals.odometer_required'),
                    pattern: _l('admin.rentals.odometer_valid'),
                    min: _l('admin.rentals.odometer_valid'),
                },
                fuel: {
                    required: _l('admin.rentals.fuel_required'),
                    pattern: _l('admin.rentals.fuel_valid'),
                    min: _l('admin.rentals.fuel_valid'),
                },
                inspection_status: {
                    required: _l('admin.rentals.inspection_status_required'),
                },
                repair_status: {
                    required: _l('admin.rentals.repair_status_required'),
                },
                'checklist_id[]': {
                    required: _l('admin.rentals.checklist_required'),
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr('name') === 'checklist_id[]') {
                    $("#checklist_error").text(error.text());
                } else {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                }
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                const $el = $(element);
                if ($el.attr('name') === 'checklist_id[]') {
                    $el.removeClass("is-invalid").addClass("is-valid");
                    $("#checklist_error").text("");
                } else {
                    $el.removeClass("is-invalid").addClass("is-valid");
                    var errorId = element.id + "_error";
                    $("#" + errorId).text("");
                }
            },        
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
                let locationFormData = new FormData(form);
                $.ajax({
                    type:"POST",
                    url:"/admin/store_inspection",
                    data:locationFormData,
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
                    success:function(resp){
                        if (resp.code == 200) {
                            showToast('success', resp.message);
                            $("#add_inspection").modal('hide');
                            initTable();
                            $("#inspectionForm")[0].reset();
                            $("#inspectionForm #id").val('');
                            $("#inspectionForm input[type=checkbox]").prop('checked', false);
                            $(".error-text").text("");
                            $(".form-control").removeClass("is-invalid is-valid");
                            $('#vehicle_info_id').val(null).trigger('change');
                            $('#inspection_by').val(null).trigger('change');
                            $('#inspection_status').val(null).trigger('change');
                            $('#repair_status').val(null).trigger('change');
                            $('#checklist_id').val(null).trigger('change');
                        }
                    },
                    error:function(error){
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

        $.validator.addMethod("uniqueInspection", function(value, element) {
            let isValid = false;
            const vehicleId = $('#vehicle_info_id').val();
            const inspectionId = $('#id').val();
        
            if (!vehicleId || !value) return false;
        
            $.ajax({
                url: '/admin/check-vehicle-inspection',
                type: 'POST',
                data: {
                    vehicle_info_id: vehicleId,
                    inspection_date: value,
                    inspection_id: inspectionId, 
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    isValid = !response.exists;
                }
            });
        
            return isValid;
        }, "This vehicle already has an inspection on the selected date.");
    }

    function initEvents() {
        $('#add_inspection').on('shown.bs.modal', function (e) {
            const $vehicle = $("#vehicle_info_id");
            if ($vehicle.hasClass("select2-hidden-accessible")) {
                $vehicle.select2('destroy');
            }
            $vehicle.select2({
                dropdownParent: $("#add_inspection"),
                placeholder: _l('admin.rentals.vehicles'),
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '/admin/get_vehicles',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data
                        };
                    }
                }
            });
            const vehicleId = $('#id').val();
            const selectedVehicleId = $('#vehicle_info_id').data('selected-id');
            const selectedVehicleText = $('#vehicle_info_id').data('selected-text');
        
            if (selectedVehicleId && selectedVehicleText) {
                const option = new Option(selectedVehicleText, selectedVehicleId, true, true);
                $vehicle.append(option).trigger('change');
            }
        });

        $(document).on('keyup', '#search', function() {
            let search = $(this).val();
           setTimeout(function () {
               if(search.length >= 2 || search.length == 0){
                  initTable();
               }
           }, 300);
        });
        
        $(document).on('click', '.statusfilter', function () {
            let statusLabel = $(this).text();
            let statusFilter = $(this).data('status');
            $("#status_text").text(statusLabel);
            initTable(statusFilter);
        });

        $(document).on('click', '#deletebtn', function(){
            let id = $(this).data('id');
            $("#deleteInspection #delete_id").val(id);
        });

        $("#deleteInspection").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type:"POST",
                url:"/admin/delete_inspection",
                data:$("#deleteInspection").serialize(),
                success:function(response){
                    if(response.code === 200){
                        showToast('success', response.message);
                        $("#delete-modal").modal('hide');
                        initTable();
                    } else {
                        showToast('error', response.message);
                        $("#delete-modal").modal('hide');
                    }
                },
                error:function(error){
                    showToast('error', error.responseJSON.message);
                }
            });
        });

        $(document).on('click','#add_new_inspection', function(){
            $("#add_inspection .modal-title").text(_l('admin.rentals.create_inspection'));
            $("#add_inspection .submitbtn").text(_l('admin.common.create_new'));
            $("#inspectionForm")[0].reset();
            $("#inspectionForm #id").val('');
            $("#inspectionForm input[type=checkbox]").prop('checked', false);
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $('#statusDiv').addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
            $('#vehicle_info_id').val(null).trigger('change');
            $('#inspection_by').val(null).trigger('change');
            $('#inspection_status').val(null).trigger('change');
            $('#repair_status').val(null).trigger('change');
            $('#checklist_id').val(null).trigger('change');
        });

        $(document).on('click','#editInspection', function() {
            let id = $(this).data('id');
            editInspection(id);
        });
    }

    function initTable(statusFilter = null){
        let search = $("#search").val();
        $.ajax({
            url:"/admin/get_inspections",
            type:"GET",
            data:{
                search:search,
                status:statusFilter
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#inspectionTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success:function(response){
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#inspectionTable")) {
                    $("#inspectionTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;                
                    
                    $.each(data, function(index, value) {
                        let inspection_status = formatInspectionStatus(value.inspection_status);
                        let repair_status = formatRepairStatus(value.repair_status);
                        
                        tableBody += `<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="#" class="avatar me-2 flex-shrink-0">
                                                    <img src="${value.car.vehicle_image}" alt="${_l('admin.common.image')}">
                                                </a>
                                                <div>
                                                    <a href="#" class="fw-semibold d-block">${value.car ? value.car.name : '-'}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><p class="text-gray-9 mb-0">${value.inspectiondate}</p></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0"><img class="rounded-circle" src="${value.inspector.profile_image}" alt=""></a>
                                                <div>
                                                    <a href="javascript:void(0);" class="fw-semibold d-block">${value.inspector ? value.inspector.name : ''}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${inspection_status} </td>
                                        <td>${repair_status} </td>
                                    ${ hasPermission(permissions, 'inspections', 'edit') || hasPermission(permissions, 'inspections', 'delete') ? 

                                        `<td>
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${ hasPermission(permissions, 'inspections', 'edit') ? 

                                                    `<li>
                                                        <button type="button" class="dropdown-item rounded-1" data-vehicle-id="${value.id}" data-vehicle-text="${value.car ? value.car.name : ''}" data-id="${value.id}" id="editInspection"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</button>
                                                    </li>`:''}
                                            ${ hasPermission(permissions, 'inspections', 'delete') ? 

                                                    `<li>
                                                        <button type="button" class="dropdown-item rounded-1" data-id="${value.id}" id="deletebtn" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</button>
                                                    </li>`:''}
                                                </ul>
                                            </div>
                                        </td>`:''}
                                    </tr>`;
                    });

                }else{
                    tableBody += `
                                <tr>
                                    <td colspan="8" class="text-center">${_l('admin.common.empty_table')}</td>
                                </tr>`;
                    $('.table-footer').empty();
                }
                $("#inspectionTable tbody").html(tableBody);
                if ((response.data.length != 0) && !$.fn.DataTable.isDataTable('#inspectionTable')) {
                    $('#inspectionTable').DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        "drawCallback": function() {
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
            error:function(error){
                showToast('error', error.responseJSON.message);
            }
        });
    }
    
    function editInspection(id){
        $.ajax({
            type:"GET",
            url:"/admin/get_inspection/"+id,
            success:function(response){
                if(response.code === 200){
                    let data = response.data;
    
                    const $vehicleSelect = $("#add_inspection #vehicle_info_id");
                    const vehicleId = data.vehicle_info_id;
                    const vehicleText = data.car ? data.car.name : '';
    
                    if ($vehicleSelect.find("option[value='" + vehicleId + "']").length === 0) {
                        const newOption = new Option(vehicleText, vehicleId, true, true);
                        $vehicleSelect.append(newOption).trigger('change');
                    } else {
                        $vehicleSelect.val(vehicleId).trigger('change');
                    }
                    let formatedDate; //format DD-MM-YYYY
                    if(data.inspection_date){
                        formatedDate = moment(data.inspection_date).format('DD-MM-YYYY');
                    }
                    $("#add_inspection #id").val(data.id);
                    $("#add_inspection #inspection_date").val(formatedDate);
                    $("#add_inspection #inspection_by").val(data.inspector_id).trigger('change');
                    $("#add_inspection #odometer").val(data.odometer);
                    $("#add_inspection #fuel").val(data.fuel);
    
                    let checklist = data.check_list ? JSON.parse(data.check_list) : [];
                    if (checklist && checklist.length > 0) {
                        $.each(checklist, function (index, value) {
                            $("#add_inspection #checklist_id_" + value).prop('checked', true);
                        });
                    }
    
                    $("#add_inspection #notes").val(data.notes);
                    $("#add_inspection #inspection_status").val(data.inspection_status).trigger('change');
                    $("#add_inspection #repair_status").val(data.repair_status).trigger('change');
                    $("#add_inspection .modal-title").text(_l('admin.rentals.edit_inspection'));
                    $("#add_inspection .submitbtn").text(_l('admin.common.save_changes'));
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                    $("#add_inspection").modal('show');
                } else{
                    showToast('error', response.message);
                }
            },
            error:function(error){
                showToast('error', error.responseJSON.message);
            }
         });
    }
    
    function formatInspectionStatus(_status){
        let inspection_status = "";
        switch (_status) {
            case 'completed':
                inspection_status = `
                    <span class="badge badge-soft-success d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.completed')}
                    </span>`;
                break;
            case 'inprogress':
                inspection_status = `
                    <span class="badge badge-soft-info d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.inprogress')}
                    </span>`;
                break;
            case 'pending':
                inspection_status = `
                    <span class="badge badge-soft-purple d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.pending')}
                    </span>`;
                break;
            case 'onhold':
                inspection_status = `
                    <span class="badge badge-soft-warning d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.onhold')}
                    </span>`;
                break;
            case 'rejected':
                inspection_status = `
                    <span class="badge badge-soft-danger d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.rejected')}
                    </span>`;
                break;
           
        }
    
        return inspection_status;
    }
    
    function formatRepairStatus(_status){
        let repair_status = "";
        switch (_status) {
            case 'completed':
                repair_status = `
                    <span class="badge badge-success-transparent d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.completed')}
                    </span>`;
                break;
            case 'inprogress':
                repair_status = `
                    <span class="badge badge-purple-transparent d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.inprogress')}
                    </span>`;
                break;
            case 'pending':
                repair_status = `
                    <span class="badge badge-info-transparent d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.pending')}
                    </span>`;
                break;
            case 'onhold':
                repair_status = `
                    <span class="badge badge-warning-transparent d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.onhold')}
                    </span>`;
                break;
            case 'rejected':
                repair_status = `
                    <span class="badge badge-danger-transparent d-inline-flex align-items-center">
                        <i class="ti ti-circle-filled fs-5 me-1"></i>${_l('admin.rentals.rejected')}
                    </span>`;
                break;
           
        }
    
        return repair_status;
    }
}) ();