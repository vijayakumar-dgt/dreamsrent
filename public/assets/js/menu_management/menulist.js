(async () => {
    await loadTranslationFile('admin', 'common, cms');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        menuTable();
        $("#addMenu").validate({
            rules: {
                language: {
                    required: true,
                },
                menu_type: {
                    required: true,
                },
                menu_name: {
                    required: true,
                    minlength: 3
                },
                menu_permalink: {
                    required: true,
                    url: true
                }
            },
            messages: {
                menu_name: {
                    required: _l('admin.cms.menu_name_required'),
                    minlength: _l('admin.cms.menu_name_minlength')
                },
                menu_type: {
                    required: _l('admin.cms.menu_type_required')
                },
                menu_permalink: {
                    required: _l('admin.cms.permalink_required'),
                    url: _l('admin.cms.valid_url')
                }
            },
            errorPlacement: function (error, element) {
                var errorId = element.attr("id") + "Error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = element.id + "Error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
    
                $.ajax({
                    type: "POST",
                    url: "/admin/menus/store", // Change this to your actual backend route
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('#createMenuBtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('#createMenuBtn').attr('disabled', false).html(_l('admin.common.create_new'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            $("#add_menu").modal('hide');
                            showToast('success', resp.message);
                            $("#addMenu")[0].reset();
                            $(".form-control").removeClass("is-invalid is-valid");
                            menuTable();
                        } else {
                            showToast('error', resp.message);
                        }
                    },
                    error: function (error) {
                        $(".error-message").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
    
                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").text(val[0]);
                            });
                        } else {
                            showToast('error', error.responseJSON.message);
                        }
                    }
                });
            }
        });
    
        $("#editMenuForm").validate({
            rules: {
                editMenuName: {
                    required: true,
                    minlength: 3
                },
                editMenuPermalink: {
                    required: true,
                    url: true
                }
            },
            messages: {
                editMenuName: {
                    required: _l('admin.cms.menu_name_required'),
                    minlength: _l('admin.cms.menu_name_minlength')
                },
                editMenuPermalink: {
                    required: _l('admin.cms.permalink_required'),
                    url: _l('admin.cms.valid_url')
                }
            },
            errorPlacement: function (error, element) {
                var errorId = element.attr("id") + "Error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = element.id + "Error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
    
                $.ajax({
                    type: "POST",
                    url: "/admin/menus/update",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.savebtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.savebtn').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            $("#edit_menu").modal('hide');
                            showToast('success', resp.message);
                            $("#editMenuForm")[0].reset();
                            $(".form-control").removeClass("is-invalid is-valid");
                            menuTable();
                        } else {
                            showToast('error', resp.message);
                        }
                    },
                    error: function (error) {
                        $(".error-message").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
    
                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").text(val[0]);
                            });
                        } else {
                            showToast('error', error.responseJSON.message);
                        }
                    }
                });
            }
        });
    
    
    });
    function menuTable() {
        $.ajax({
            url: "/admin/menus/list",
            type: "GET",
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#menuTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function(response) {
                let tableBody = "";
    
                if ($.fn.DataTable.isDataTable("#menuTable")) {
                    $("#menuTable").DataTable().clear().destroy(); // Properly clear and destroy DataTable
                }
    
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, value) {
                        let createdDate = new Date(value.created_at).toLocaleDateString("en-US", {
                            year: "numeric",
                            month: "short",
                            day: "2-digit",
                        });
    
                        tableBody += `
                        <tr>
                            <td>
                                <p class="text-gray-9">${value.name}</p>
                            </td>
                            <td>
                                <p class="text-gray-9">${value.menu_type.charAt(0).toUpperCase() + value.menu_type.slice(1)}</p>
                            </td>
                            <td>
                                <p class="text-gray-9">${createdDate}</p>
                            </td>
                            <td>
                                <span class="badge badge-${value.status === 1 ? 'soft-success' : 'soft-danger'} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1 ${value.status === 1 ? 'text-success' : 'text-danger'}"></i>
                                    ${value.status === 1 ? `${_l('admin.cms.published')}` : `${_l('admin.cms.unpublished')}`}
                                </span>
                            </td>
                                ${hasPermission(permissions, 'menu_management', 'edit') || hasPermission(permissions, 'menu_management', 'delete') ?
    
                            `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                         ${hasPermission(permissions, 'menu_management', 'edit')  ?
                                       `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                            onclick="editMenu(${value.id}, '${value.name}', '${value.permenantlink}', ${value.status}, ${value.language_id}, '${value.menu_type}')">
                                                <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                            </a>
                                        </li>`:''}
                                             ${hasPermission(permissions, 'menu_management', 'delete')  ?
    
                                       ` <li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                            onclick="deleteMenu(${value.id})" data-bs-toggle="modal" data-bs-target="#delete_menu">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </a>
                                        </li>`:''}
                                             ${hasPermission(permissions, 'menu_management', 'edit')  ?
    
                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                                onclick="menuManagement(${value.id}, '${value.name}', '${value.permenantlink}', ${value.status}, ${value.language_id})">
                                                <i class="ti ti-menu-2 me-1"></i>${_l('admin.cms.menu_management')}
                                            </a>
                                        </li>`:''}
                                    </ul>
                                </div>
                            </td>`:''}
                        </tr>`;
                    });
                } else {
                    tableBody = `<tr><td colspan="6" class="text-center">${_l('admin.common.empty_table')}</td></td></tr>`;
                    $('.table-footer').empty();
                }
    
                $("#menuTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $('#menuTable').DataTable({
                        ordering: true,
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
            error: function(error) {
                showToast('error', error.responseJSON?.error || "An error occurred while retrieving the menu list!");
            },
        });
    }
    
})();


function editMenu(id, name, permalink, status, languageId, menu_type) {
    $(".form-control").removeClass("is-invalid is-valid");
    $('#menuId').val(id);
    $('#editMenuName').val(name);
    $('#editMenuPermalink').val(permalink);
    $('#editMenuStatus').prop('checked', status == 1);
    $('#editMenuLanguage').val(languageId).trigger('change');
    $('#editMenuType').val(menu_type).trigger('change');

    $('#edit_menu').modal('show');
}
function menuManagement(id, name, permalink, status, languageId) {
    localStorage.setItem('menu_id', id);
    localStorage.setItem('menu_name', name);

    window.location.href = "/admin/menu-management";
}


function deleteMenu(id){
    $("#delete_id").val(id);
}

$("#deleteMenu").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/menus/delete",
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
                $("#delete_menu").modal('hide');
                menuTable();
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


