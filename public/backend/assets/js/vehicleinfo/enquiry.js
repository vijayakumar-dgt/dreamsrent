(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, bookings');
    const permissions = await loadUserPermissions();
    let searchTimer;

    $(document).ready(function() {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initFormValidation() {
        $("#editEnquiryForm").validate({
            rules: {
                comment: {
                    required: true,
                },
                status: {
                    required: true,
                },
            },
            messages: {
                comment: {
                    required: _l('admin.bookings.comment_required'),
                },
                status: {
                    required: _l('admin.bookings.status_required'),
                },
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
            submitHandler: function (form) {
                let formData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/enquiry/update",
                    data: formData,
                    enctype: "multipart/form-data",
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $("#edit_enquiry_modal").modal('hide');
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

    function initEvents() {
        $(document).on("click", ".sort_by_list .dropdown-item", function(){
            $('.sort_by_list .dropdown-item').removeClass('active');
            $(this).addClass('active');
            $('#current_sort').text($(this).text());
            initTable();
        });
        
        $(document).on("click", ".dropdown-menu .active-status", function(){
            $('.dropdown-menu .active-status').removeClass('active');
            $(this).addClass('active');
            $('#current_status').text($(this).text());
            initTable();
        });

        $('.enquiresearch').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                initTable();
            }, 500);
        });

        $(document).on('click', '.dataTables_paginate a', function() {
            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
        });

        $(document).on('click', '.delete-enquiry', function(){
            let id = $(this).data('id');
            $("#delete_id").val(id);
        })

        $("#enquiryDeleteForm").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/enquiry/delete",
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
                        initTable();
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

        $(document).on('click', '.edit-enquiry', function(){
            let id = $(this).data('id');
            let car_name = $(this).data('car-name');
            let customer_name = $(this).data('customer-name');
            let email = $(this).data('email');
            let phone = $(this).data('phone');
            let enquiry_date = $(this).data('enquiry-date');
            let enquiry_details = $(this).data('enquiry-details');
            let status = $(this).data('status');
            populateEditEnquiry(id,car_name, customer_name, email, phone, enquiry_date, enquiry_details, status);
        });
    }

    function populateEditEnquiry(id, carId, customerName, email, phone, enquiryDate, enquiryDetails, status) {
        $('#edit_enquiry_modal .assigned_cars').text(carId);
        $("#id").val(id);
        $('#edit_enquiry_modal .enquiry_details').text(enquiryDetails);
        $('#edit_enquiry_modal .customer_name').text(customerName);
        $('#edit_enquiry_modal .email').text(email);
        $('#edit_enquiry_modal .phone_number').text(phone);
        $('#edit_enquiry_modal .enquiry_date').text(enquiryDate);
        $("#status").val(status).trigger('change');
        if (enquiryDetails.status) {
            $('#priority').val(enquiryDetails.status).change();
        }
    }

    function initTable() {
        const status = $('.dropdown-menu .active-status.active').data('value') || '';
        const sortBy = $('.sort_by_list .dropdown-item.active').data('value') || 'latest';
        const dateRange = $('.enquirerange').val();
        const search = $('.enquiresearch').val();

        $.ajax({
            url: "/admin/enquiry/list",
            type: "GET",
            data: {
                status: status,
                sort_by: sortBy,
                date_range: dateRange,
                search: search
            },
            beforeSend: function() {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#enquiryTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function(response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#enquiryTable")) {
                    $("#enquiryTable").DataTable().destroy();
                }

                if (response.success && response.data.length > 0) {
                    $.each(response.data, function(index, value) {

                        tableBody += `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2 flex-shrink-0">
                                        <img src="${value.vehicle_image}" alt="Vehicle Image" class="avatar-img">
                                    </div>
                                    <div>
                                        <div class="fw-semibold d-block text-black">${value.car_name}</div>
                                        <span class="fs-13">${value.type_name}</span>
                                    </div>
                                </div>
                            </td>
                            <td>${value.customer_name}</td>
                            <td>${value.email}</td>
                            <td>${value.phone}</td>
                            <td>${value.enquiry_date}</td>
                            <td>
                                <span class="avatar avatar-md bg-light rounded-circle tooltip-trigger"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                    title="${value.enquiry_details}">
                                    <i class="ti ti-file-invoice text-gray-9"></i>
                                </span>
                            </td>
                            <td>
                                <span class="badge ${
                                    (value.status == 1) ? 'badge-warning-transparent' :
                                    (value.status == 2) ? 'badge-info-transparent' :
                                    (value.status == 3) ? 'badge-success-transparent' :
                                    'badge-secondary-transparent'
                                } d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${
                                        (value.status == 1) ? `${_l('admin.common.not_opened')}` :
                                        (value.status == 2) ? `${_l('admin.common.opened')}` :
                                        (value.status == 3) ? `${_l('admin.common.closed')}` :
                                        'Unknown'
                                    }
                                </span>
                            </td>
                            ${ hasPermission(permissions, 'enquiries', 'edit') || hasPermission(permissions, 'enquiries', 'delete') ?
                            `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2 shadow-sm">
                                        ${hasPermission(permissions, 'enquiries', 'edit') ? `
                                            <li>
                                                <button type="button" class="dropdown-item rounded-1 edit-enquiry" data-id="${value.id}"
                                                    data-bs-toggle="modal" data-bs-target="#edit_enquiry_modal"
                                                    data-car-name="${value.car_name}"
                                                    data-customer-name="${value.customer_name}"
                                                    data-email="${value.email}"
                                                    data-phone="${value.phone}"
                                                    data-enquiry-date="${value.enquiry_date}"
                                                    data-enquiry-details="${value.enquiry_details}"
                                                    data-status="${value.status}">
                                                    <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                                </button>
                                            </li>` : ''}

                                        ${hasPermission(permissions, 'enquiries', 'delete') ? `
                                        <li>
                                            <button type="button" class="dropdown-item rounded-1 delete-enquiry" data-id="${value.id}"
                                                data-bs-toggle="modal" data-bs-target="#delete-modal">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </button>
                                        </li>` : ''}
                                    </ul>
                                </div>
                            </td>`:''}
                        </tr>`;
                    });
                } else {
                    tableBody = `<tr><td colspan="9" class="text-center">${_l('admin.common.empty_table')}</td></tr>`;
                    $('.table-footer').empty();
                }

                $("#enquiryTable tbody").html(tableBody);
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (response.data.length > 0) {
                    $("#enquiryTable").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function () {
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
            error: function(error){
                showToast('error', error.responseJSON?.message || "An error occurred while retrieving!");
            }
        });
    }
}) ();