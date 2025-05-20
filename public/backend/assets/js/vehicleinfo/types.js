(async () => {
    "use strict";

    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();
    let table;
    let statusFilter;
    
    $(document).ready(function() {
        initTable();
        initValidation();
        initEvents();
    });

    function initValidation() {
        $("#typeForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                icon: {
                    required: {
                        depends: function(element) {
                            return $('#icon_preview').attr('src') == "";
                        }
                    },
                    extension: "jpeg|jpg|png|svg",
                    filesize: 10,
                    imageDimension:[50,150],
                }
            },
            messages:{
                name: {
                    required: _l('admin.rentals.name_required'),
                },
                icon: {
                    required: _l('admin.rentals.icon_required'),
                    extension: _l('admin.rentals.icon_extension'),
                    filesize: _l('admin.rentals.brand_image_size', {size: 2}),
                    imageDimension: _l('admin.rentals.icon_dimension')
                }
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
                $("#add_type .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..`);
                $("#add_type .submitbtn").prop('disabled', true);
            
                $.ajax({
                    type:"POST",
                    url:"/admin/storetype",
                    data:typeFormData,
                    processData: false,
                    contentType: false,
                    success:function(resp){
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_type").modal('hide');
                            table.ajax.reload();
                        }
                        $("#add_type .submitbtn").text(_l('admin.common.create_new'));
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
                            showToast("error", error.responseJSON.message);
                        }
                        $("#add_type .submitbtn").text(_l('admin.common.create_new'));
                        $("#add_type .submitbtn").prop('disabled', false);
                    }
                });
            }
        });

        $.validator.addMethod("filesize", function (value, element, param) {
            if (element.files.length === 0) return true;
            return element.files[0].size <= param * 1024;
        },'file size should be less than {0} bytes');

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
                valid = img.width >= 40 && img.width <= 150 && img.height >= 40 && img.height <= 150;
                $(element).data("valid-dimension", valid);
                $(element).valid();
            };
        
            reader.readAsDataURL(file);
        
            return $(element).data("valid-dimension") !== false;
        }, _l('admin.rentals.icon_dimension'));
    }

    function initEvents() {
        $(document).on("click", "#add_new_type", function(){
            $("#add_type .modal-title").text(_l('admin.rentals.create_type'));
            $("#add_type .submitbtn").text(_l('admin.common.create_new'));
            $("#status_div").addClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
            $("#typeForm")[0].reset();
            $("#icon_preview").addClass('d-none');
            $(".icon_placeholder").removeClass('d-none');
            $("#typeForm #id").val('');
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            if($("#submit_div").hasClass("justify-content-between")){
                $("#submit_div").removeClass("justify-content-between").addClass("justify-content-end");
            }
        });

        $(document).on("click", ".delete-type", function () {
            const id = $(this).data("id");
            $("#delete_id").val(id);
        });

        $(document).on('keyup', 'input[name=search]',function(){
            table.ajax.reload();
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
            table.ajax.reload();
        });

        $("#icon").change(function(){
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#icon_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
                $("#icon_preview").removeClass('d-none');
                $(".icon_placeholder").addClass('d-none');
        
            }else{
                $("#icon_preview").addClass('d-none');
                $(".icon_placeholder").removeClass('d-none');
            }
        });

        $("#deleteType").on('submit', function(e){
            e.preventDefault();
            $("#deleteType .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.deleting')}..`);
            $("#deleteType .submitbtn").prop('disabled', true);
            $.ajax({
                type:"POST",
                url:"/admin/deletetype",
                data:$("#deleteType").serialize(),
                success:function(response){
                    if(response.code === 200){
                        showToast("success", response.message);
                        $("#delete-modal").modal('hide');
                        table.ajax.reload();
                    }else{
                        showToast("error", response.message);
                        $("#delete-modal").modal('hide');
                    }
                    $("#deleteType .submitbtn").text(_l('admin.common.yes_delete'));
                    $("#deleteType .submitbtn").prop('disabled',false);
                },
                error:function(error){
                    showToast("error", error.responseJSON.message);
                    $("#delete-modal").modal('hide');
                    $("#deleteType .submitbtn").text(_l('admin.common.yes_delete'));
                    $("#deleteType .submitbtn").prop('disabled',false);
                }
            });
        });

        $(document).on("click", ".edit-type", function () {
            const id = $(this).data("id");
            editType(id);
        });

        $(document).on('click', '.dataTables_paginate a', function() {
            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
        });
    }

    function initTable(){
        table =  $("#carTypeTable").DataTable({
            processing: false,
            serverSide: true,
            ajax:{
                url:"/admin/get_cartype_serverside",
                type:"POST",
                data:function(d){
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.search = $('input[name="search"]').val();
                    d.status = statusFilter;
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete:function(response){
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass("d-none");
                    if ($("#carTypeTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                }
            },
            order:[['1','desc']],
            ordering: false,
            searching: false, 
            pageLength: 10,
            lengthChange: false,
            autoWidth: false,
            responsive: false,
            aoColumns:[
                {
                    data: "name",
                    render:function(data,type,row){
                    return `<h6 class="fw-medium">${row.name}</h6>`;
                    },
                    className: 'text-start'
                },
                {
                    data: "icon",
                    render:function(data,type,row){
                    return `<div class="d-flex align-items-center file-name-icon">
                                <div  class="avatar avatar-lg border">
                                    <img src="${row.icon}" class="img-fluid" width="40" height="40">
                                </div>
                            </div>`;
                    },
                    className: 'text-start'
                },
                {
                    data: "status",
                    render:function(data,type,row){
                    return `<span class="badge ${row.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                <i class="ti ti-point-filled me-1"></i>${row.status == 1 ? `${_l('admin.common.active')}` : `${_l('admin.common.inactive')}` }
                            </span>`;
                    },
                    className: 'text-start'
                },
                {
                    data: null,
                    render: function(data, type, row){
                        return `<div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'edit') ? 
                                        `<li>
                                            <button type="button"
                                                class="dropdown-item rounded-1 edit-type" 
                                                data-id="${row.id}">
                                                <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                            </button>
                                        </li>` : ''}
                                    ${ hasPermission(permissions, 'vehicle_attributes', 'delete') ? 
                                        `<li>
                                            <button type="button"
                                                class="dropdown-item rounded-1 delete-type" 
                                                data-id="${row.id}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#delete-modal">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                            </button>
                                        </li>` : ''}
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

    function editType(id){
        $("#icon").val('');
        $.ajax({
        type:"GET",
        url:"/admin/getcartype/"+id,
        success:function(response){
            if(response.code === 200){
                let data = response.data;
                $("#add_type #name").val(data.name);
                $("#add_type #id").val(data.id);
                $("#add_type #language_id").val(data.language_id);
                if(data.status === 1){
                    $("#add_type #status").prop('checked', true);
                }else{
                    $("#add_type #status").prop('checked', false);
                }
                if(data.icon && data.icon != null){
                    $("#icon_preview").attr('src', data.icon).removeClass('d-none');
                    $(".icon_placeholder").addClass('d-none');
                }else{
                    $("#icon_preview").addClass('d-none');
                    $(".icon_placeholder").removeClass('d-none');
                }
                $("#add_type .modal-title").text(_l('admin.rentals.edit_type'));
                $("#add_type .submitbtn").text(_l('admin.common.save_changes'));
                $("#status_div").removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                if($("#submit_div").hasClass("justify-content-end")){
                    $("#submit_div").removeClass("justify-content-end").addClass("justify-content-between");
                }
                $("#add_type").modal('show');
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
            }else{
                showToast("error", response.message);
            }
            
        },
        error:function(error){
            showToast("error", error.responseJSON.message);
        }
        });
    }
}) ();