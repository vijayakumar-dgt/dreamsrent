$(document).ready(function() {
    initTable();
    $("#pricingTypeForm").validate({
        rules: {
            pricing_type: {
                required: true,
            },
        },
        messages:{
            pricing_type: {
                required: 'Pricing type is required.',
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
            let formData = new FormData();
            formData.append('pricing_type', $('#pricing_type').val());
            if ($('#id').val() != '') {
                formData.append('id', $("#id").val());
                formData.append('status', $("#status").is(":checked") ? 1 : 0);
            }

            $.ajax({
                type:"POST",
                url:"/admin/pricing-type/save",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(resp){
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                    if (resp.code === 200) {
                        toastr.success(resp.message);
                        $("#pricing_type_modal").modal('hide');
                        initTable();
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
                        toastr.error(error.responseJSON.message);
                    }
                }
            });
        }
    });
});

function initTable(){
    $.ajax({
        url:"/admin/pricing-type/list",
        type:"GET",
        success:function(response){
            let tableBody = "";
            if ($.fn.DataTable.isDataTable("#pricingTypeTable")) {
                $("#pricingTypeTable").DataTable().destroy();
            }

            if (response.code === 200 && response.data.length > 0) {
                let data = response.data;
                
                $.each(data, function(index, value) {
                    tableBody += `<tr>
                            <td>
                                <div class="form-check form-check-md" data-id="${value.id}">
                                    <input class="form-check-input" type="checkbox">
                                </div>
                            </td>
                            <td>${value.pricing_type}</td>
                            <td>
                                <span class="badge ${(value.status == 1) ? 'badge-success-transparent' : 'badge-danger-transparent'} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${(value.status == 1) ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="editPricingType(${value.id});"><i class="ti ti-edit me-1"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="deletePricingType(${value.id});" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>`;
                });

            } else{
                tableBody += `
                        <tr>
                            <td colspan="4" class="text-center">No Data Available</td>
                        </tr>`;
                $('.table-footer').empty();
            }
            $("#pricingTypeTable tbody").html(tableBody);
            if ((response.data.length > 0)) {
                $('#pricingTypeTable').DataTable({
                    ordering: true,
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
                    }
                });
            }
        },
        error:function(error){
            if (error.responseJSON.code === 500) {
                toastr.error(error.responseJSON.message);
            } else {
                toastr.error("An error occurred while retrieving!");
            }
        }
    });
}

$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

function editPricingType(id){
    $.ajax({
       type:"GET",
       url:"/admin/pricing-type/edit/"+id,
       success: function(response) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            if(response.code === 200){
                let data = response.data;
                $("#pricing_type").val(data.pricing_type);
                $("#status").prop('checked', data.status == 1);
                $("#id").val(data.id);

                $("#pricing_type_modal .modal-title").text('Edit Pricing Type');
                $(".submitbtn").text('Save Changes');
                $('#statusDiv').show().parent().removeClass('justify-content-end').addClass('justify-content-between');
                $("#pricing_type_modal").modal('show');
            }
       }
    });
}

$("#add_pricing_type").on('click', function() {
    $(".modal-title").text('Create New Pricing Type');
    $(".submitbtn").text('Create New');
    $("#pricingTypeForm")[0].reset();
    $("#id").val('');
    $(".error-text").text("");
    $(".form-control").removeClass("is-invalid is-valid");
    $('#statusDiv').hide().parent().removeClass('justify-content-between').addClass('justify-content-end');
});

function deletePricingType(id){
    $("#delete_id").val(id);
}

$("#deletePricingType").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/pricing-type/delete",
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
                toastr.success(response.message);
                $("#delete-modal").modal('hide');
                initTable();
            }
        },
        error: function(res) {
            if(res.responseJSON.code === 500){
                toastr.success(res.responseJSON.message);
            } else {
                toastr.error('An error occurred while deleting!');
            }
        }
    });
});