$(document).ready(function() {
    "use strict";
    var table;
    $("#typeForm").validate({
        rules: {
            name: {
                required: true,
            },
        },
        messages:{
            name: {
                required: 'Please enter name',
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
           let typeFormData = new FormData(form);
           $("#add_type .submitbtn").text('Please Wait...');
           $("#add_type .submitbtn").prop('disabled', true);
           $.ajax({
               type:"POST",
               url:"/admin/storetype",
               data:typeFormData,
               processData: false,
               contentType: false,
               success:function(resp){
                   if (resp.code === 200) {
                       toastr.success(resp.message);
                       $("#add_type").modal('hide');
                    //    initTable();
                    table.ajax.reload();
                   }
                   $("#add_type .submitbtn").text('Create New');
                   $("#add_type .submitbtn").prop('disabled', false);
                   
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
                $("#add_type .submitbtn").text('Create New');
                $("#add_type .submitbtn").prop('disabled', false);
               }
           });
        }
    });

    function initTableServerSide(){
        //serverside ajax datatable
        table =  $("#carTypeTable").DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                url:"/admin/getvehiclelist",
                type:"POST",
                data:function(d){
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.search = $('input[type="search"]').val();
                }
            },
            order:[['1','desc']],
            ordering: false,
            searching: false, // Hides the search box
            pageLength: 10, // default page length
            lengthChange: false, // Hides the length menu
            aoColumns:[
                 {
                     data: "id",
                     render:function(data,type,row){
                         return `<div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox">
                                 </div>`;
                     }
                 },
                 {
                     data: "name",
                     render:function(data,type,row){
                        return `<h6 class="fw-medium"><a href="#">${row.name}</a></h6>`;
                     }
                 },
                 {
                     data: "status",
                     render:function(data,type,row){
                        return `<span class="badge ${row.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>${row.status == 1 ? `Active` : `Inactive` }
                                </span>`;
                     }
                 },
                 {
                     data: null,
                     render: function(data, type, row){
                         return `<div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(${row.id});" onclick="editType(${row.id});"><i class="ti ti-edit me-1"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(${row.id});" onclick="deleteType(${row.id});" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>Delete</a>
                                            </li>
                                        </ul>
                                    </div>`;
                     }
                 }
            ],
            "drawCallback": function() {
                $(".dataTables_info").addClass('d-none');
                // Only hide pagination inside the table (within the .dataTables_wrapper)
                $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');
                // Move the info and pagination to the card-footer
                var tableWrapper = $(this).closest('.dataTables_wrapper');
                var info = tableWrapper.find('.dataTables_info');
                var pagination = tableWrapper.find('.dataTables_paginate');
                
                // Clear the card-footer and append info and pagination
                $('.table-footer').empty()
                    .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                        .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                        .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                );
                $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
            }
         });
    }

    $("#deleteType").on('submit', function(e){
        e.preventDefault();
        $("#deleteType .submitbtn").text('Please Wait...');
        $("#deleteType .submitbtn").prop('disabled', true);
        $.ajax({
            type:"POST",
            url:"/admin/deletetype",
            data:$("#deleteType").serialize(),
            success:function(response){
                if(response.code === 200){
                    toastr.success(response.message);
                    $("#delete-modal").modal('hide');
                    // initTable();
                    table.ajax.reload();
                }else{
                    toastr.error(response.message);
                    $("#delete-modal").modal('hide');
                }
                $("#deleteType .submitbtn").text('Yes,Delete');
                $("#deleteType .submitbtn").prop('disabled',false);
            },
            error:function(error){
                toastr.error(error.responseJSON.message);
                $("#delete-modal").modal('hide');
                $("#deleteType .submitbtn").text('Yes,Delete');
                $("#deleteType .submitbtn").prop('disabled',false);
            }
        });
    });

    initTableServerSide();
});

function initTable(){
    $.ajax({
        url:"/admin/getvehiclelist",
        type:"GET",
        success:function(response){
            let tableBody = "";
            if($.fn.DataTable.isDataTable('#carTypeTable')){
                $("#carTypeTable").DataTable().destroy();
            }
            if (response.code === 200 && response.data.length > 0) {
                let data = response.data;
                // console.log(data);
                
                $.each(data, function(index, value) {
                    tableBody += `<tr>
                            <td>
                                <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                </div>
                            </td>
                            <td><h6 class="fw-medium"><a href="#">${value.name}</a></h6></td>
                            <td>
                                <span class="badge ${value.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${value.status == 1 ? `Active` : `Inactive` }
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(${value.id});" onclick="editType(${value.id});"><i class="ti ti-edit me-1"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(${value.id});" onclick="deleteType(${value.id});" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>`;
                });

            }else{
                //destroy datatable
                $("#carTypeTable").DataTable().destroy();
                tableBody += `
                            <tr>
                                <td colspan="4" class="text-center">No Data Available</td>
                            </tr>`;
                $('.table-footer').empty();
            }
           
            $("#carTypeTable tbody").html(tableBody);
            if ((response.data.length != 0)) {
                
                $('#carTypeTable').DataTable({
                    ordering: false,
                    searching: false, // Hides the search box
                    pageLength: 10, // default page length
                    lengthChange: false, // Hides the length menu
                    "drawCallback": function() {
                        $(".dataTables_info").addClass('d-none');
                        // Only hide pagination inside the table (within the .dataTables_wrapper)
                        $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');
                        // Move the info and pagination to the card-footer
                        var tableWrapper = $(this).closest('.dataTables_wrapper');
                        var info = tableWrapper.find('.dataTables_info');
                        var pagination = tableWrapper.find('.dataTables_paginate');
                     
                        // Clear the card-footer and append info and pagination
                        $('.table-footer').empty()
                            .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                                .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                                .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                            );
                    }
                    
                });
            }
        },
        error:function(error){
            console.log('error');
        }
    });
}


$(document).on('click', '.dataTables_paginate a', function() {
    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
});

function editType(id){
    $.ajax({
       type:"GET",
       url:"/admin/getcartype/"+id,
       success:function(response){
        if(response.code === 200){
            let data = response.data;
            $("#add_type #name").val(data.name);
            $("#add_type #id").val(data.id);
            if(data.status === 1){
                $("#add_type #status").prop('checked', true);
            }else{
                $("#add_type #status").prop('checked', false);
            }
            //change modal title 
            $("#add_type .modal-title").text('Edit Type');
            $("#add_type .submitbtn").text('Save Changes');
            $("#status_div").show();
            $("#add_type").modal('show');
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
        }else{
            toastr.error(response.message);
        }
        
       },
       error:function(error){
         toastr.error(error.responseJSON.message);
       }
    });
}
$(document).on("click", "#add_new_type", function(){
    $("#add_type .modal-title").text('Create Type');
    $("#add_type .submitbtn").text('Create New');
    $("#status_div").hide();
    $("#typeForm")[0].reset();
    $("#typeForm #id").val('');
    $(".error-text").text("");
    $(".form-control").removeClass("is-invalid is-valid");
});

function deleteType(id){
    console.log('delete id '+ id);
    $("#delete_id").val(id);
}

