(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, general_settings');
       const permissions = await loadUserPermissions();

$(document).ready(function() {
    initTable();
    $("#insuranceForm").validate({
        rules: {
            insurance_name: {
                required: true,
                minlength: 3,
                maxlength: 100,
            },
            price_type_id: {
                required: true,
            },
            price: {
                required: true,
            },
            "benefit[]": {
                required: true },
        },
        messages:{
            insurance_name: {
                required: _l('admin.general_settings.insurance_name_required'),
                minlength: _l('admin.general_settings.insurance_name_minlength'),
                maxlength: _l('admin.general_settings.insurance_name_maxlength'),
            },
            price_type_id: {
                required: _l('admin.general_settings.price_type_required'),
            },
            price: {
                required: _l('admin.general_settings.price_required'),
            },
            "benefit[]": {
                required: _l('admin.general_settings.benefit_required'),
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            }  else if (element.attr("name") === "price_type_id") {
                $("#price_type_error").text(error.text()).show();
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
            if ($('#id').val() != '') {
                formData.set('status', $("#status").is(":checked") ? 1 : 0);
            }

            $.ajax({
                type:"POST",
                url:"/admin/settings/insurance/save",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('.submitBtn').attr('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                    `);
                },
                success:function(resp){
                    $(".error-text").text("");
                    $(".form-control, .form-check-input").removeClass("is-invalid is-valid");
                    $(".submitBtn").removeAttr("disabled").html($("#id").val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
                    if (resp.code === 200) {
                        showToast('success', resp.message);
                        $("#insurance_modal").modal('hide');
                        $('#insuranceTable').DataTable().ajax.reload();
                    }
                },
                error:function(error){
                    $(".error-text").text("");
                    $(".form-control, .form-check-input").removeClass("is-invalid is-valid");
                    $(".submitBtn").removeAttr("disabled").html($("#id").val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
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
});

$('#search').on('keyup', function (e) {
    $('#insuranceTable').DataTable().ajax.reload();
});

function initTable(){

    $("#insuranceTable").DataTable({
        destroy: true,
        serverSide: true,
        ajax: {
            url: "/admin/settings/insurance/list",
            type: "POST",
            data: function(d) {
                d.search = $('#search').val();
            },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                if($('#insuranceTable').DataTable().rows().count() == 0){
                    $(".table-footer").addClass('d-none');
                } else {
                    $(".table-footer").removeClass('d-none');
                }
            },
        },
        columns: [
            { data: "insurance_name"},
            { data: "price", render: function(data) { return `$${data}`; }},
            { data: "insurance_benefits_count", render: function(data, type, row) {
                return `<div class="d-flex align-items-center">
                            ${data} ${data > 1 ? _l('admin.common.benefits') : _l('admin.common.benefit')}
                            <a href="#" class="btn btn-xs btn-info-light fs-14 extlinkbtn py-0 px-1 ms-1"
                               onclick='getBenefits(${JSON.stringify(row.insurance_benefits)})'
                               data-bs-toggle="modal" data-bs-target="#view-benifits">
                               <i class="ti ti-external-link"></i>
                            </a>
                        </div>`;
            }},
            { data: "status", render: function(data) {
                return `<span class="badge badge-dark-transparent d-inline-flex align-items-center badge-sm">
                            <i class="ti ti-point-filled me-1 ${data == 1 ? 'text-success' : 'text-danger'}"></i>
                            ${data == 1 ? _l('admin.common.active') : _l('admin.common.inactive')}
                        </span>`;
            }},
            { data: "id", orderable: false, searchable: false, render: function(data) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                        ${ hasPermission(permissions, 'rental_settings', 'edit') ?

                            `<li>
                                <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="editInsurance(${data})">
                                    <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                </a>
                            </li>`:''}
                         ${ hasPermission(permissions, 'rental_settings', 'delete') ?

                            `<li>
                                <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="deleteInsurance(${data})"
                                   data-bs-toggle="modal" data-bs-target="#delete-modal">
                                    <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                </a>
                            </li>`:''}
                        </ul>
                    </div>`;
            },
            visible: hasPermission(permissions, 'vehicle_attributes', 'edit') || hasPermission(permissions, 'vehicle_attributes', 'delete')

        }
        ],
        order: [[0, 'asc']],
        ordering: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        responsive: false,
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
        }
    });
}

$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

$(document).on('click', '#add_new_benefit', function() {
    let nameAttribute = $('#id').val() ? 'benefit[new][]' : 'benefit[]';
    $('.add-insurance-benefit').append(`
        <div class="mb-1 extra-benifit-row">
			<div class="d-flex align-items-center">
                <input type="text" class="form-control flex-fill mb-2 benefit" name="${nameAttribute}">
                <a href="#" class="delete-item btn btn-sm"><i class="ti ti-trash text-danger fs-16"></i></a>
            </div>
        </div>
    `);
});

$(document).on('click', '.delete-item', function() {
    $(this).closest('.extra-benifit-row').remove();
});

$("#add_insurance").on('click', function() {
    $(".modal-title").text(_l('admin.general_settings.create_insurance'));
    $(".submitBtn").text(_l('admin.common.create_new'));
    $("#insuranceForm")[0].reset();
    $("#id").val('');
    $(".error-text").text("");
    $(".form-control, .form-check-input").removeClass("is-invalid is-valid");
    $(".add-insurance-benefit").children("div").not(":first").remove();
    $('#benefit').attr('name', 'benefit[]');
    $('#statusDiv').addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
    let form = $("#insuranceForm");
    let validator = form.validate();
    let newInput = $('#benefit');
    validator.settings.ignore = "";
    newInput.rules("remove");
    newInput.rules("add", {
        required: true,
        messages: {
            required: _l('admin.general_settings.benefit_required')
        }
    });
});

$(".price_type").on("change", function () {
    const selectedType = $(this).data('price_type');
    if (selectedType === 'percentage') {
        $("#price").data("maxlen", 3);
    } else {
        $("#price").data("maxlen", 5);
    }

    $("#price").data("maxlen", maxLen);
});

$("#price").on("input", function () {
    let maxLen = $(this).data("maxlen");
    $(this).val($(this).val().replace(/[^0-9]/g, "").slice(0, maxLen));
});

$("#deleteInsurance").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/settings/insurance/delete",
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
                $('#insuranceTable').DataTable().ajax.reload();
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

}) ();

function getBenefits(benefits) {
    $('#benefitsList').empty();
    let benefitsArray = typeof benefits === "string" ? JSON.parse(benefits) : benefits;

    $.each(benefitsArray, function (index, value) {
        $('#benefitsList').append(`
            <p class="d-flex align-items-center mb-2"><i class="ti ti-checks text-success me-1"></i>${value.benefit}</p>
        `);
    });
}

function editInsurance(id){
    $.ajax({
        type:"GET",
        url:"/admin/settings/insurance/edit/"+id,
        success: function(response) {
            $("#insuranceForm")[0].reset();
            $(".error-text").text("");
            $(".form-control, .form-check-input").removeClass("is-invalid is-valid");
            if(response.code === 200){
                let data = response.data;
                $("#insurance_name").val(data.insurance_name);
                $("#price").val(data.price);
                $("#status").prop('checked', data.status == 1);
                $("#id").val(data.id);
                $("#language_id").val(data.language_id);
                $(`input[name="price_type_id"][value="${data.price_type_id}"]`).prop('checked', true);

                $("#insurance_modal .modal-title").text(_l('admin.general_settings.edit_insurance'));
                $(".submitBtn").text(_l('admin.general_settings.save_changes'));
                $('#statusDiv').removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                $('.add-insurance-benefit').children("div").not(":first").remove();

                if (data.insurance_benefits) {
                    let form = $("#insuranceForm");
                    let validator = form.validate();

                    $.each(data.insurance_benefits, function(index, value) {
                        if (index == 0) {
                            $('#benefit').attr('name', `benefit[${value.id}]`);
                            $('#benefit').val(value.benefit);
                        } else {
                            $('.add-insurance-benefit').append(`
                                <div class="mb-1 extra-benifit-row">
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control flex-fill mb-2 benefit" name="benefit[${value.id}]" value="${value.benefit}">
                                        <a href="#" class="delete-item btn btn-sm"><i class="ti ti-trash text-danger fs-16"></i></a>
                                    </div>
                                </div>
                            `);
                        }
                        let newInput = $('#benefit');
                        validator.settings.ignore = "";
                        newInput.rules("remove");
                        newInput.rules("add", {
                            required: true,
                            messages: {
                                required: _l('admin.general_settings.benefit_required')
                            }
                        });
                    });
                }

                $("#insurance_modal").modal('show');
            }
       }
    });
}

function deleteInsurance(id){
    $("#delete_id").val(id);
}
