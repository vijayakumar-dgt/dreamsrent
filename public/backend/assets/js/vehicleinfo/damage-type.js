(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();
    let statusFilter;

    $(document).ready(function() {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initTable(){
        $.ajax({
            url:"/admin/get_damage_types",
            type:"GET",
            data:{
                keyword : $("#keyword").val(),
                status : statusFilter
            },
            beforeSend: function() {
               $(".table-loader").show();
               $(".real-table").addClass('d-none');  
            },
            success:function(response){
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#damageTypeTable")) {
                    $("#damageTypeTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;
                    
                    $.each(data, function(index, value) {
                        tableBody += `
                                <tr>
                                    <td class="text-start"><h6 class="fw-medium"><a href="#">${value.damage_type}</a></h6></td>
                                    <td class="text-start">
                                        <span class="badge ${value.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>${value.status == 1 ? `${_l('admin.common.active')}` : `${_l('admin.common.inactive')}` }
                                        </span>
                                    </td>
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'edit') || hasPermission(permissions, 'vehicle_attributes', 'delete') ? 
                                    `<td class="text-start">
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${ hasPermission(permissions, 'vehicle_attributes', 'edit') ? 
                                                `<li>
                                                    <button 
                                                        class="dropdown-item rounded-1 edit-damage-type" 
                                                        data-id="${value.id}">
                                                        <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                                    </button>
                                                </li>`:''
                                            }
                                            ${ hasPermission(permissions, 'vehicle_attributes', 'delete') ? 
                                                `<li>
                                                    <button 
                                                        class="dropdown-item rounded-1 delete-damage-type" 
                                                        data-id="${value.id}" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#delete-modal">
                                                        <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                                    </button>
                                                </li>` : ''
                                            }
                                            </ul>
                                        </div>
                                    </td>` : ''}
                                </tr>`;
                    });
                    
                }else{
                    tableBody += `
                            <tr>
                                <td colspan="4" class="text-center">${_l('admin.common.empty_table')}</td>
                            </tr>`;
                    $('.table-footer').empty();
                }
                $("#damageTypeTable tbody").html(tableBody);
                if ((response.data.length > 0)) {
                    $('#damageTypeTable').DataTable({
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
            complete: function() {
                $(".table-loader").hide();
                $(".real-table").removeClass('d-none');
            },
            error:function(error){
                showToast('error', _l('admin.common.default_retrieve_error'));
            }
        });
    }
    
    function initFormValidation() {
        $("#damageTypeForm").validate({
            rules: {
                damage_type: {
                    required: true,
                    minlength:3,
                    maxlength:30
                },
            },
            messages:{
                damage_type: {
                    required: _l('admin.rentals.damage_type_required'),
                    minlength: _l('admin.common.minlength', {min: 3}),
                    maxlength: _l('admin.common.maxlength', {max: 30}),
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
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
                let damageTypeFormData = new FormData(form);
                $("#add_damage_type .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..`);
                $("#add_damage_type .submitbtn").attr('disabled', true);
                $.ajax({
                    type:"POST",
                    url:"/admin/store_damage_type",
                    data:damageTypeFormData,
                    processData: false,
                    contentType: false,
                    success:function(resp){
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_damage_type").modal('hide');
                            initTable();
                        }
                        $("#add_damage_type .submitbtn").text(_l('admin.common.create_new'));
                        $("#add_damage_type .submitbtn").attr('disabled', false);
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
                            showToast("error", error.responseJSON.message);
                        }
                        $("#add_damage_type .submitbtn").text(_l('admin.common.create_new'));
                        $("#add_damage_type .submitbtn").attr('disabled', false);
                    }
               });
            }
        });
    }

    function initEvents() {
        $(document).on('click', '#add_new_damage_type', function () {
            $("#add_damage_type .modal-title").text(_l('admin.rentals.create_damage_type'));
            $("#add_damage_type .submitbtn").text(_l('admin.common.create_new'));
            $("#status_div").addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');;
            $("#damageTypeForm")[0].reset();
            $("#damageTypeForm #id").val('');
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
        });

        $(document).on('click', '.status_filter', function(){
            statusFilter = $(this).data('status');
            if(statusFilter == 1){
                $("#status_text").text(_l('admin.common.active'));
            }else if(statusFilter == 0){
                $("#status_text").text(_l('admin.common.inactive'));
            }else{
                $("#status_text").text(_l('admin.common.status'));
            }
            initTable();
        });

        $(document).on('keyup','#keyword', function(){
            let keyword = $(this).val();
            setTimeout(function () {
                initTable();
            }, 300);
        });

        $("#deleteDamageType").on("submit", function (e) {
            e.preventDefault();
        
            const $submitBtn = $("#deleteDamageType .submitbtn");
            const deleteId = $("#delete_id").val();

            $submitBtn.html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> 
                ${_l('admin.common.deleting')}...`)
                .prop("disabled", true);
        
            $.ajax({
                type: "POST",
                url: "/admin/delete_damage_type",
                data: { delete_id: deleteId },
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (response) {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $("#delete-modal").modal("hide");
                        initTable();
                    } else {
                        showToast("error", response.message || _l('admin.common.default_delete_error'));
                        $("#delete-modal").modal("hide");
                    }
                },
                error: function (error) {
                    const msg = error.responseJSON?.message || _l('admin.common.default_delete_error');
                    showToast("error", msg);
                    $("#delete-modal").modal("hide");
                },
                complete: function () {
                    $submitBtn.text(_l('admin.common.yes_delete')).prop("disabled", false);
                }
            });
        }); 

        $(document).on("click", ".edit-damage-type", function () {
            const id = $(this).data("id");
            editDamageType(id);
        });

        $(document).on("click", ".delete-damage-type", function () {
            const id = $(this).data("id");
            $("#delete_id").val(id);
        });
    }

    function editDamageType(id) {
        $.ajax({
            type: "GET",
            url: "/admin/get_damage_type/" + id,
            success: function (response) {
                if (response.code === 200) {
                    const data = response.data;

                    $("#add_damage_type #damage_type").val(data.damage_type);
                    $("#add_damage_type #id").val(data.id);
                    $("#add_damage_type #language_id").val(data.language_id);

                    $("#add_damage_type #status").prop("checked", data.status === 1);

                    $("#add_damage_type .modal-title").text(_l('admin.rentals.edit_damage_type'));
                    $("#add_damage_type .submitbtn").text(_l('admin.common.save_changes'));

                    $("#status_div")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");

                    $("#add_damage_type").modal("show");

                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                } else {
                    showToast("error", response.message || _l("admin.common.fetch_error"));
                }
            },
            error: function () {
                showToast("error", _l("admin.common.fetch_error"));
            }
        });
    }
})();


