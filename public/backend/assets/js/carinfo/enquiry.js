"use strict";
document.addEventListener("DOMContentLoaded", function () {
    const userPhoneInput = document.querySelector(".driver_phone_number");
    const intlPhoneInput = document.querySelector("#international_phone_number");

    if (userPhoneInput) {
        const iti = intlTelInput(userPhoneInput, {
            loadUtilsOnInit: true,
            separateDialCode: true,
            geoIpLookup: function (callback) {
                fetch("https://ipapi.co/json/")
                    .then((response) => response.json())
                    .then((data) => callback(data.country_code))
                    .catch(() => callback("in"));
            },
            preferredCountries: ["in", "us", "gb"],
        });

        userPhoneInput.classList.add("iti");
        userPhoneInput.parentElement.classList.add("intl-tel-input");

        const initialPhoneNumber = userPhoneInput.value.trim();
        if (initialPhoneNumber) {
            iti.setNumber(initialPhoneNumber);
        }


    }
});

(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, bookings');
    const permissions = await loadUserPermissions();

$(document).ready(function() {


    initTable();

    $("#enquiryForm").validate({
        rules: {
            "assigned_cars[]": {
                required: true,
            },
            customer_name: {
                required: true,
                maxlength: 100,
                pattern: /^[a-zA-Z\s]+$/
            },
            email: {
                required: true,
                email: true,
            },
            phone_number: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            enquiry_details: {
                required: true,
                maxlength: 500
            },
            "documents[]": {
                extension: "jpeg|jpg|png|pdf",
                filesize: 2048,
            },
        },
        messages: {
            "assigned_cars[]": {
                required: "Please select at least one car.",
            },
            customer_name: {
                required: "Customer name is required.",
                maxlength: "Customer name should not exceed 100 characters.",
                pattern: "Customer name should only contain alphabetic characters.",
            },
            email: {
                required: "Email is required.",
                email: "Please enter a valid email address.",
            },
            phone_number: {
                required: "Phone number is required.",
                digits: "Phone number should only contain digits.",
                minlength: "Phone number should be at least 10 digits.",
                maxlength: "Phone number should not exceed 15 digits.",
            },
            enquiry_details: {
                required: "Enquiry details are required.",
                maxlength: "Enquiry details should not exceed 500 characters.",
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
                type: "POST",
                url: "/admin/enquiry/save",
                data: formData,
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                    if (resp.code === 200) {
                        showToast('success', resp.message);
                        $("#add_enquiry_modal").modal('hide');
                        initTable();
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
                required: "Comment is required.",
            },
            status: {
                required: "Status is required.",
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

    $.validator.addMethod("filesize", function (value, element, param) {
        if (element.files.length === 0) return true;
        return element.files[0].size <= param * 1024;
    }, "File size must be less than {0} KB.");

});

$('#image').on('change', function (event) {
    if ($(this).val() !== '') {
        $(this).valid();
    }
    let reader = new FileReader();
    reader.onload = function (e) {
        $('#imagePreview').attr('src', e.target.result).show();
        $('.upload_icon').hide();
    };
    reader.readAsDataURL(event.target.files[0]);
});

$(document).ready(function() {
    // Initialize date range picker
    $('.enquirerange').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoUpdateInput: false
    });

    $('.enquirerange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        initTable();
    });

    $('.enquirerange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        initTable();
    });

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

    // Handle search input with debounce
    let searchTimer;
    $('.enquiresearch').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            initTable();
        }, 500);
    });

    // Initial table load
    initTable();
});

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
                    let vehicleImageUrl = value.vehicle_image ? `/storage/${value.vehicle_image}` : '/backend/assets/img/car/default.jpg';

                    tableBody += `<tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="" class="avatar me-2 flex-shrink-0">
                                    <img src="${vehicleImageUrl}" alt="Vehicle Image" class="avatar-img">
                                </a>
                                <div>
                                    <a href="" class="fw-semibold d-block">${value.car_name}</a>
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
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                            data-bs-toggle="modal" data-bs-target="#edit_enquiry_modal"
                                            onclick="populateEditEnquiry(
                                                ${value.id},
                                                '${value.car_name}',
                                                '${value.customer_name}',
                                                '${value.email}',
                                                '${value.phone}',
                                                '${value.enquiry_date}',
                                                '${value.enquiry_details}',
                                                '${value.status}'
                                            );">
                                                <i class="ti ti-eye me-1"></i>${_l('admin.common.view')}
                                            </a>
                                        </li>` : ''}

                                    ${hasPermission(permissions, 'enquiries', 'delete') ? `
                                        <li>
                                            <a class="dropdown-item rounded-1 text-danger" href="javascript:void(0);"
                                            data-bs-toggle="modal" data-bs-target="#delete-modal"
                                            onclick="deleteEnquiry(${value.id});">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </a>
                                        </li>` : ''}
                                </ul>
                            </div>
                        </td>`:''}
                    </tr>`;
                });
            } else {
                tableBody = `<tr><td colspan="9" class="text-center">${_l('admin.common.empty_table')}</td></tr>`;
            }

            $("#enquiryTable tbody").html(tableBody);
            $('[data-bs-toggle="tooltip"]').tooltip();
            if (response.data.length > 0) {
                $("#enquiryTable").DataTable({
                    ordering: false,
                    searching: false,
                    pageLength: 10,
                    lengthChange: false,
                    drawCallback: function() {
                        $(".dataTables_info, .dataTables_paginate").addClass('d-none');
                        $('.table-footer').empty().append(`
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="datatable-info">${$('.dataTables_info').clone().html()}</div>
                                <div class="datatable-pagination">${$('.dataTables_paginate').clone().html()}</div>
                            </div>
                        `);
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
        error: function(error){
            showToast('error', error.responseJSON?.message || "An error occurred while retrieving!");
        }
    });
}




$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

$("#add_driver").on('click', function() {
    $("#driverForm")[0].reset();
    $("#id").val('');
    $(".error-text").text("");
    $(".form-control").removeClass("is-invalid is-valid");
    $('#assigned_cars').val('').trigger('change');
    $(".upload_icon").show();
    $('#imagePreview').hide();
});

$('#gender').on('change', function () {
    $(this).valid();
});
$('#assigned_cars').on('change', function () {
    $(this).valid();
});

let removedDocuments = [];
$(document).on('click', '.remove-document', function() {
    let documentId = $(this).data('id');
    console.log(documentId);
    removedDocuments.push(documentId);
    $("#removed_documents").val(removedDocuments.join(","));
    $(this).closest('.document-preview').remove();
});

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

}) ();
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


let initialPhoneNumber = null;
function editDriver(id){
    $('#editDriverForm').trigger('reset');
    removedDocuments = [];
    $.ajax({
       type:"GET",
       url:"/admin/driver/edit/"+id,
       success: function(response) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            if(response.code === 200){
                let data = response.data;

                $("#id").val(data.id);
                $("#edit_driver_name").val(data.driver_name);
                $("#edit_gender").val(data.gender).trigger('change');
                $("#edit_email").val(data.email);
                $("#edit_address").val(data.address);
                $("#edit_date_of_issue").val(data.date_of_issue);
                $("#edit_valid_date").val(data.valid_date);
                $("#edit_card_number").val(data.card_number);
                $("#status").prop('checked', data.status == 1);
                // $("#edit_phone_number").val(data.phone_number);
                var phone_number = data.phone_number;

                if (data.assigned_cars) {
                    let assignedCars = data.assigned_cars.split(",");
                    $("#edit_assigned_cars").val(assignedCars).trigger('change');
                }
                if (data.image) {
                    $('#editImagePreview').attr('src', data.image).show();
                    $(".upload_icon").hide();
                } else {
                    $(".upload_icon").show();
                    $('#editImagePreview').hide()
                }
                $('.document-preview-container').empty();
                if (data.documents && data.documents.length > 0) {
                    $.each(response.data.documents, function(index, value) {
                        $('.document-preview-container').append(
                            `<div class="document-preview me-2">
                                <a href="/storage/drivers/${value.document}" target="_blank" class="btn btn-sm btn-light me-0" ><i class="ti ti-file fs-40"></i></a>
                                <button type="button" class="btn btn-sm btn-light remove-document" data-id="${value.id}"><i class="ti ti-trash"></i></button>
                            </div>`
                        );
                    });
                }

                const phoneNumber = phone_number.trim();
                const phoneInput = document.querySelector(".edit_driver_phone_number");
                const hiddenInput = document.querySelector("#edit_international_phone_number");

                if ($(phoneInput).data('itiInstance')) {
                    $(phoneInput).data('itiInstance').destroy();
                }
                const iti = intlTelInput(phoneInput, {
                    utilsScript: window.location.origin + "/backend/assets/plugins/intltelinput/js/utils.js",
                    separateDialCode: true,
                });
                $(phoneInput).data('itiInstance', iti);

                if (phoneNumber) {
                    iti.setNumber(phoneNumber);
                    hiddenInput.value = iti.getNumber();
                    initialPhoneNumber = phoneNumber;
                }

                phoneInput.addEventListener("countrychange", function() {
                    const currentPhoneNumber = iti.getNumber();
                    if (currentPhoneNumber !== initialPhoneNumber) {
                        hiddenInput.value = currentPhoneNumber;
                    }
                });

                if (!hiddenInput.value) {
                    hiddenInput.value = initialPhoneNumber;
                }

                $("#edit_driver_modal").modal('show');
            }
       }
    });
}

function deleteEnquiry(id){
    $("#delete_id").val(id);
}
