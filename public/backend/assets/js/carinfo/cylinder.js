(function($) {
    "use strict";

(async () => {
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();

$(document).ready(function() {
    var table;
    let statusFilter;
    cylinderTableServerside();
    $("#cylinderForm").validate({
        rules: {
            cylinder_type: {
                required: true,
                minlength:3,
                maxlength:30
            },
        },
        messages:{
            cylinder_type: {
                required: _l('admin.rentals.cylinder_type_required'),
                minlength: _l('admin.rentals.cylinder_type_minlength'),
                maxlength: _l('admin.rentals.cylinder_type_maxlength'),
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
           let cylinderFormData = new FormData(form);
           $.ajax({
               type:"POST",
               url:"/admin/store_cylinder_type",
               data:cylinderFormData,
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
                    if (resp.code === 200) {
                        showToast("success", resp.message);
                        $("#add_cylinder").modal('hide');
                        table.ajax.reload();
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
                        showToast("error", error.responseJSON.message);
                    }
                }
           });
        }
    });

    $("#search").on("keyup", function () {
        let search = $(this).val().trim();
        setTimeout(function () {
            table.search(search).draw();
        }, 300);
    });
    $(document).on("click",".statusfilter", function(){
        statusFilter = $(this).data('status');
        if(statusFilter == 1){
            $("#status_text").text(_l('admin.common.active'));
        }else if(statusFilter == 0){
            $("#status_text").text(_l('admin.common.inactive'));
        }else{
            $("#status_text").text(_l('admin.common.status'));
        }
        table.ajax.reload();
    });

    function cylinderTableServerside(){
        table =  $("#cylinderTable").DataTable({
           processing: false,
           serverSide: true,
           ajax:{
               url:"/admin/get_cylinder_serverside",
               type:"POST",
               beforeSend: function () {
                   $(".table-loader").removeClass('d-none');
                   $(".real-table").addClass('d-none');
               },
               data:function(d){
                   d._token = $('meta[name="csrf-token"]').attr('content');
                   d.search = $('#search').val();
                   d.status = statusFilter;
               },
               beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete: function () {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass("d-none");
                    if ($("#cylinderTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
           },
           order:[['1','desc']],
           ordering: false,
           searching: false,
           pageLength: 10,
           lengthChange: false,
           responsive: false,
           autoWidth: false,
           columns:[
            {
                data: "cylinder_type",
                render:function(data,type,row){
                    return `<h6 class="fw-medium"><a href="#">${row.cylinder_type}</a></h6>`;
                }
            },
            {
                data: "status",
                render:function(data,type,row){
                    return `<span class="badge ${row.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${row.status == 1 ? `${_l('admin.common.active')}` : `${_l('admin.common.inactive')}` }
                            </span>`;
                 }
            },
            {
                data: "id",
                render: function(data, type, row){
                    return `<div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                ${ hasPermission(permissions, 'vehicle_attributes', 'edit') ? 

                                    `<li>
                                        <a class="dropdown-item rounded-1 editCylinder" href="javascript:void(${row.id});" data-id="${row.id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</a>
                                    </li>`:''}
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'delete') ? 

                                    `<li>
                                        <a class="dropdown-item rounded-1 deleteCylinder" href="javascript:void(${row.id});" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</a>
                                    </li>`:''}
                                </ul>
                            </div>`;
                },
                 visible: hasPermission(permissions, 'vehicle_attributes', 'edit') || hasPermission(permissions, 'vehicle_attributes', 'delete'),
                
                }
            ],
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

    $(document).on('click', '.dataTables_paginate a', function() {
        $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
    });

    $("#deleteCylinder").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type:"POST",
            url:"/admin/delete_cylinder",
            data:$("#deleteCylinder").serialize(),
            success:function(response){
                if(response.code === 200){
                    showToast("success", response.message);
                    $("#delete-modal").modal('hide');
                    table.ajax.reload();
                }else{
                    showToast("error", response.message);
                    $("#delete-modal").modal('hide');
                }
            },
            error:function(error){
              showToast("error", error.responseJSON.message);
              $("#delete-modal").modal('hide');
            }
        });
    });
});

})();

$(document).on("click", "#add_new_cylinder", function () {
    $("#add_cylinder .modal-title").text(_l('admin.rentals.create_cylinder_type'));
    $("#add_cylinder .submitbtn").text(_l('admin.common.create_new'));
    $("#cylinderForm")[0].reset();
    $("#cylinderForm #id").val('');
    $(".error-text").text("");
    $(".form-control").removeClass("is-invalid is-valid");
    $('#statusDiv').addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
});

$(document).on("click", ".editCylinder", function () {
    let id = $(this).attr("data-id");
    $.ajax({
        type:"GET",
        url:"/admin/get_cylinder/"+id,
        success:function(response){
            if(response.code === 200){
                let data = response.data;
                $("#add_cylinder #cylinder_type").val(data.cylinder_type);
                $("#add_cylinder #id").val(data.id);
                if(data.status === 1){
                    $("#add_cylinder #status").prop('checked', true);
                }else{
                    $("#add_cylinder #status").prop('checked', false);
                }
                $("#add_cylinder .modal-title").text(_l('admin.rentals.edit_cylinder_type'));
                $("#add_cylinder .submitbtn").text(_l('admin.common.save_changes'));
                $("#status_div").show();
                $("#add_cylinder").modal('show');
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                $('#statusDiv').removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
            }else{
                showToast("error", response.message);
            }
        },
        error:function(error){
            showToast("error", error.responseJSON.message);
        }
    });
});

$(document).on("click", ".deleteCylinder", function () {
    let id = $(this).attr("data-id");
    $("#delete_id").val(id);
});


})(jQuery);