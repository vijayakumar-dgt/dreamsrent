(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');
    const permissions = await loadUserPermissions();


    $(document).ready(function() {
        signatureTable();
        $("#addSignatureForm").validate({
            rules: {
                signature_image: {
                    required: true,
                    accept: "image/*"
                },
                signature_name: {
                    required: true,
                    minlength: 3
                },
                is_default: {
                    required: false
                }
            },
            messages: {
                signature_image: {
                    required: _l('admin.general_settings.upload_signature_image'),
                    accept:_l('admin.general_settings.image_only_allowed')
                },
                signature_name: {
                    required: _l('admin.general_settings.enter_signature'),
                    minlength:_l('admin.general_settings.enter_signature_characters')
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
                let signatureData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/signatures/store",
                    data: signatureData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.btn-primary').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.btn-primary').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $('#addSignatureForm')[0].reset();

                            $('#add_signatures').modal('hide');
                            signatureTable();
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

        $("#editSignatureForm").validate({
            rules: {
                signature_image: {
                    accept: "image/*"
                },
                signature_name: {
                    required: true,
                    minlength: 3
                },
                is_default: {
                    required: false
                },
                status: {
                    required: false
                }
            },
            messages: {
                signature_image: {
                    accept: _l('admin.general_settings.favicon_image_resolution')
                },
                signature_name: {
                    required: _l('admin.general_settings.signature_name'),
                    minlength:_l('admin.general_settings.signature_characters'),
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
                let editData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/signatures/update",
                    data: editData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.btn-primary').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.btn-primary').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $('#editSignatureForm')[0].reset();
                            $('#edit_signature').modal('hide');
                            signatureTable();
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

    });

    function signatureTable(){
        $.ajax({
            url: "/admin/settings/signatures/list",
            type: "GET",
            success: function(response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#signatureTable")) {
                    $("#signatureTable").DataTable().destroy();
                }

                if (response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function(index, value) {
                        tableBody += `
                        <tr>
                            <td>
                                <h6 class="fw-medium fs-14">
                                    ${value.signature_name}
                                    ${value.is_default === 1 ? '<span class="ms-2 badge badge-soft-purple">Default</span>' : ''}
                                </h6>
                            </td>
                            <td>
                                <img src="${value.signature_image}" alt="Signature">
                            </td>
                            <td>
                               <span class="badge badge-${value.status === 1 ? 'success' : 'danger'}-transparent d-inline-flex align-items-center badge-sm">
                           <i class="ti ti-point-filled me-1 ${value.status === 1 ? 'text-success' : 'text-danger'}"></i>
                                 ${value.status === 1 ? _l('admin.common.active') : _l('admin.common.inactive')}
                         </span>

                            </td>
                             ${hasPermission(permissions, 'app_settings', 'edit') || hasPermission(permissions, 'app_settings', 'delete') ?

                            `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                 ${ hasPermission(permissions, 'app_settings', 'edit') ?

                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                            onclick="editSignature(${value.id}, '${value.signature_name}', '${value.signature_image}', ${value.status}, ${value.is_default})">
                                                <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                            </a>
                                        </li>`:''}
                                          ${ hasPermission(permissions, 'app_settings', 'delete') ?

                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" onclick="deleteSignature(${value.id})" data-bs-target="#delete_signature">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </a>
                                        </li>`:''}
                                    </ul>
                                </div>
                            </td>`:''}
                        </tr>`;
                    });
                } else {
                    tableBody += `
                    <tr>
                        <td colspan="4" class="text-center">${_l('admin.general_settings.no_data')}</td>
                    </tr>`;
                    $('.table-footer').empty();
                }

                $("#signatureTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $('#signatureTable').DataTable({
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
                        }
                    });
                }
            },
            error: function(error) {
                if (error.responseJSON && error.responseJSON.error) {
                    showToast('error', error.responseJSON.error);
                } else {
                    showToast('error',_l('admin.general_settings.retrive_error'));
                }
            },
            complete: function() {
                $(".table-loader, .input-loader, .label-loader").hide();
                $('.real-table, .real-label, .real-input').removeClass('d-none');

            },
        });

    }

})();


function editSignature(id, name, image, status, isDefault) {

    $('#edit_signature_id').val(id);
    $('#edit_signature_name').val(name);
    $('#edit_signature_preview').attr('src', `${image}`);
    $('#edit_signature_status').prop('checked', status === 1);
    $('#edit_signature_default').prop('checked', isDefault === 1);


    $('#edit_signature').modal('show');
}

function deleteSignature(id){
    $("#delete_id").val(id);
}

$("#deleteSignature").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/settings/signatures/delete",
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
                signatureTable();
            }
        },
        error: function(res) {
            if(res.responseJSON.code === 500){
                showToast('error', res.responseJSON.message);
            } else {
                showToast('error', _l('admin.general_settings.retrive_error'));
            }
        }
    });
});

function editpreviewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('edit_signature_preview');

    reader.onload = function () {
        preview.src = reader.result;
    }

    if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function previewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('profile_photo_preview');

    reader.onload = function () {
        preview.src = reader.result;
    }

    if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function removeImage() {
    const preview = document.getElementById('profile_photo_preview');
    const fileInput = document.getElementById('profile_photo');

    preview.src = '/assets/img/settings/company-logo-01.jpg';
    fileInput.value = '';
}
