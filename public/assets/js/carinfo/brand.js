(async () => {
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();

$(document).ready(function() {
    initTable();
    $("#brandForm").validate({
        rules: {
            brand_name: {
                required: true,
                minlength: 3,
                maxlength: 30
            },
            total_cars: {
                required: true,
            },
            brand_image: {
                required: function () {
                    return $("#id").val() === "";
                },
                extension: "jpeg|jpg|png|svg",
                filesize: 2048,
                imageDimension:[180,180],
            },
            brand_icon: {
                required: function () {
                    return $("#id").val() === "";
                },
                extension: "jpeg|jpg|png|svg",
                filesize: 2048,
                iconDimension:[25,25],
            },
        },
        messages:{
            brand_name: {
                required: _l('admin.rentals.brand_name_required'),
                minlength: _l('admin.rentals.brand_name_minlength'),
                maxlength: _l('admin.rentals.brand_name_maxlength'),
            },
            total_cars: {
                required: _l('admin.rentals.total_vehicles_required'),
            },
            brand_image: {
                required: _l('admin.rentals.brand_image_required'),
                extension: _l('admin.rentals.brand_image_format'),
                filesize: _l('admin.rentals.brand_image_size', {size: 2}),
            },
            brand_icon: {
                required: _l('admin.rentals.brand_icon_required'),
                extension: _l('admin.rentals.brand_icon_format'),
                filesize: _l('admin.rentals.brand_icon_size', {size: 2}),
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
            formData.append('brand_name', $('#brand_name').val());
            formData.append('total_cars', $('#total_cars').val());
            let fileInput = $('#brand_image')[0].files[0];
            let fileInput2 = $('#brand_icon')[0].files[0];

            if (fileInput) {
                formData.append('brand_image', fileInput);
            }
            if (fileInput2) {
                formData.append('brand_icon', fileInput2);
            }
            if ($('#id').val() != '') {
                formData.append('id', $("#id").val());
                formData.append('status', $("#status").is(":checked") ? 1 : 0);
            }

            $.ajax({
                type:"POST",
                url:"/admin/brand/save",
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
                        $("#brand_modal").modal('hide');
                        initTable();
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

    $.validator.addMethod("iconDimension", function (value, element) {
        if (element.files.length === 0) return true;
    
        let file = element.files[0];
        let img = new Image();
        let valid = false;
    
        let reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
        };
    
        img.onload = function () {
            valid = img.width >= 10 && img.width <= 25 && img.height >= 10 && img.height <= 25;
            $(element).data("valid-dimension", valid);
            $(element).valid();
        };
    
        reader.readAsDataURL(file);
    
        return $(element).data("valid-dimension") !== false;
    }, _l('admin.rentals.brand_icon_dimension'));

    $.validator.addMethod("imageDimension", function (value, element) {
        if (element.files.length === 0) return true;
    
        let file = element.files[0];
        let img = new Image();
        let valid = false;
    
        let reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
        };
    
        img.onload = function () {
            valid = img.width >= 180 || img.height >= 180;
            $(element).data("valid-dimension", valid);
            $(element).valid();
        };
    
        reader.readAsDataURL(file);
    
        return $(element).data("valid-dimension") !== false;
    }, _l('admin.common.image_pixel'));

});

$('#brand_image').on('change', function (event) {
    $(this).valid();
    if(this.files && this.files[0]){
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
        $("#imagePreview").removeClass('d-none');
        $('.upload_icon').addClass('d-none');
    }else{
        $("#imagePreview").addClass('d-none');
        $(".upload_icon").removeClass('d-none');
    }
});

$('#brand_icon').on('change', function (event) {
    $(this).valid();
    if (this.files && this.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#iconPreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
        $("#iconPreview").removeClass('d-none');
        $(".upload_icon_2").addClass('d-none');

    }else{
        $("#iconPreview").addClass('d-none');
        $(".upload_icon_2").removeClass('d-none');
    }
});

function initTable() {
    $("#brandTable").DataTable({
        serverSide: true,
        destroy: true,
        processing: false,
        ajax: {
            url: "/admin/brand/list",
            type: "GET",
            data: function (d) {
                d.search = $("#search").val();
                d.sort_by_status = $("#sort_by_status").val();
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#brandTable").DataTable().rows().count() === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
        },
        columns: [
            { data: "brand_name",
                render: function (data, type, row) {
                    return `<div class="d-flex align-items-center file-name-icon">
                                <a href="#" class="avatar avatar-lg border">
                                    <img src="${row.brand_image}" class="img-fluid" alt="${_l('admin.common.image')}">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fw-medium"><a href="#">${row.brand_name}</a></h6>
                                </div>
                            </div>`;
                } 
            },
            {
                data: "status",
                render: function (data, type, row) {
                    return `<span class="badge ${(row.status == 1) ? 'badge-success-transparent' : 'badge-danger-transparent'} d-inline-flex align-items-center badge-sm">
                                <i class="ti ti-point-filled me-1"></i>${(row.status == 1) ? _l('admin.common.active') : _l('admin.common.inactive')}
                            </span>`;
                },
            },
            { data: "id", orderable: false, searchable: false,
                render: function (data, type, row) {
                    return `<div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'edit') ? 
                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="editBrand(${row.id});"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</a>
                                        </li>` : ''
                                    }
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'delete') ? 
                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="deleteBrand(${row.id});" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</a>
                                        </li>` : ''
                                    }
                                </ul>
                            </div>`;
                },
                visible: hasPermission(permissions, 'vehicle_attributes', 'edit') || hasPermission(permissions, 'vehicle_attributes', 'delete')
            }
        ],
        ordering: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        responsive: false,
        autoWidth: false,
        language: {
            emptyTable: _l("admin.common.no_matching_records"),
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
    });
}

$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

$('#search').on('keyup', function() {
    $('#brandTable').DataTable().ajax.reload();
});

$(document).on('click', '#status_filter .dropdown-item', function () {
    let sortBy = $(this).data('status');
    $('#sort_by_status').val(sortBy);
    if (sortBy == 1) {
        $('#current_sort_status').text(_l('admin.common.active'));
    } else {
        $('#current_sort_status').text(_l('admin.common.inactive'));
    }
    $('#status_filter .dropdown-item').removeClass('active');
    $(this).addClass('active');
    $('#brandTable').DataTable().ajax.reload();
});

$("#add_brand").on('click', function() {
    $(".modal-title").text(_l('admin.rentals.create_brand'));
    $(".submitbtn").text(_l('admin.common.create_new'));
    $("#brandForm")[0].reset();
    $("#id").val('');
    $(".error-text").text("");
    $(".form-control").removeClass("is-invalid is-valid");
    $('#statusDiv').addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
    $(".upload_icon").removeClass('d-none');
    $(".upload_icon_2").removeClass('d-none');
    $('#imagePreview').addClass('d-none');
    $('#iconPreview').addClass('d-none');
});

$("#total_cars").on("input", function () {
    $(this).val($(this).val().replace(/[^0-9]/g, ""));
});

$("#brandDeleteForm").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/brand/delete",
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

function deleteBrand(id){
    $("#delete_id").val(id);
}

function editBrand(id){
    $.ajax({
       type:"GET",
       url:"/admin/brand/edit/"+id,
       success: function(response) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#brandForm")[0].reset();
            if(response.code === 200){
                let data = response.data;
                $("#brand_name").val(data.brand_name);
                $("#total_cars").val(data.total_cars);
                $("#status").prop('checked', data.status == 1);
                $("#id").val(data.id);
                $("#language_id").val(data.language_id);

                $("#brand_modal .modal-title").text(_l('admin.rentals.edit_brand'));
                $(".submitbtn").text(_l('admin.common.save_changes'));
                $('#statusDiv').removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                if (data.brand_image) {
                    $('#imagePreview').attr('src', data.brand_image).removeClass('d-none');
                    $(".upload_icon").addClass('d-none');
                } else {
                    $(".upload_icon").removeClass('d-none');
                    $('#imagePreview').addClass('d-none');
                }

                if (data.brand_icon) {
                    $('#iconPreview').attr('src', data.brand_icon).removeClass('d-none');
                    $(".upload_icon_2").addClass('d-none');
                } else {
                    $(".upload_icon_2").removeClass('d-none');
                    $('#iconPreview').addClass('d-none');
                }

                $("#brand_modal").modal('show');
            }
       }
    });
}