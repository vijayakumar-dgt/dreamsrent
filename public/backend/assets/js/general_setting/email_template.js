(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');
    const permissions = await loadUserPermissions();


    $(document).ready(function(){
        let table;
        initTable();
         $("#description").summernote({
            height: 250,
            minHeight: 150,
            maxHeight: 500,
            focus: true,
            placeholder: _l('admin.general_settings.message_here'),
            callbacks: {
                onChange: function(contents) {
                    $('#description').val(contents);
                    $('#description').valid();
                }
            }
         });

         $(document).on('change','.select',function(){
             $(this).valid();
         });
         $(document).on('click','.var_placeholder', function(){
             let placeholder = $(this).data('placeholder');
             placeholder = '{' + placeholder + '}';

             $("#description").summernote('editor.saveRange');
             $("#description").summernote('editor.restoreRange');
             $("#description").summernote('editor.insertText', placeholder);
         });

         $("#mailTemplateForm").validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 150,
                },
                notification_type: {
                    required: true,
                },
                subject: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                sms_content: {
                    required: true,
                    minlength: 3,
                    maxlength: 500,
                },
                notification_content: {
                    required: true,
                    minlength: 3,
                    maxlength: 150,
                },
                description: {
                    customRequired: true,
                }
            },
            messages: {
                title: {
                    required:_l('admin.general_settings.enter_title'),
                    minlength: _l('admin.general_settings.enter_atleast_3character'),
                    maxlength: _l('admin.general_settings.enter_atleast_120character'),
                },
                notification_type: {
                    required: _l('admin.general_settings.select_notification_type'),
                },
                subject: {
                    required: _l('admin.general_settings.subject_required'),
                    minlength:_l('admin.general_settings.enter_atleast_3character'),
                    maxlength:  _l('admin.general_settings.enter_atleast_255character'),
                },
                sms_content: {
                    required: _l('admin.general_settings.enter_sms_content'),
                    minlength: _l('admin.general_settings.enter_atleast_3character'),
                    maxlength: _l('admin.general_settings.enter_atleast_300character'),
                },
                notification_content: {
                    required: _l('admin.general_settings.enter_notification_content'),
                    minlength: _l('admin.general_settings.enter_atleast_3character'),
                    maxlength: _l('admin.general_settings.enter_atleast_120character'),
                },
                description: {
                    customRequired:  _l('admin.general_settings.enter_description'),
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
                let _formData = new FormData(form);
                $("#mailTemplateForm .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..`);
                $("#mailTemplateForm .submitbtn").attr("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/save_email_template",
                    data: _formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $("#add_email").modal('hide');
                        } else {
                            showToast('error', resp.message);
                        }
                        $("#mailTemplateForm")[0].reset();
                        $("#mailTemplateForm #id").val('');
                        $("#mailTemplateForm .submitbtn").text( $('#id').val() != '' ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
                        $("#mailTemplateForm .submitbtn").prop('disabled', false);
                        table.ajax.reload();
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
                        $("#mailTemplateForm .submitbtn").text( $('#id').val() ? _l('admin.common.save_changes') : _l('admin.common.create_new'));
                        $("#mailTemplateForm .submitbtn").prop('disabled', false);
                    }
                });
            }
        });


        jQuery.validator.addMethod("customRequired", function(value, element) {

            let content = $(element).summernote('isEmpty') ? '' : $(element).summernote('code');
            return content.trim().length > 0;
        }, "Please enter description");

        function initTable(){
            table =  $("#emailTemplateTable").DataTable({
                processing: false,
                serverSide: true,
                ajax:{
                    url:"/admin/settings/get_emailtemplates",
                    type:"POST",
                    data:function(d){
                        d._token = $('meta[name="csrf-token"]').attr('content');
                    },
                    complete:function(){
                        $(".table-loader").hide();
                        $(".real-table").removeClass('d-none');
                    }
                },
                order:[['1','desc']],
                ordering: false,
                searching: false,
                pageLength: 10,
                lengthChange: false,
                responsive:false,
                autoWidth:false,
                aoColumns:[
                     {
                         data: "title",
                         render:function(data,type,row){
                             return `<p class="text-gray-9 fw-semibold fs-14"><a href="javascript(0)" id="viewTemplate" data-id="${row.id}">${row.title}</a></p>`;
                         }
                     },
                     {
                         data: "created_at",
                         render:function(data,type,row){
                             let parsedDate = moment(row.created_at,'YYYY-MM-DD').format('DD MMM YYYY');
                             return `<p class="text-gray-9">${parsedDate}</p>`;
                         }
                     },
                     {
                         data: "status",
                         render:function(data,type,row){
                             return `<span class="badge ${row.status == 1 ? `badge-success-transparent` : `badge-danger-transparent`}  d-inline-flex align-items-center badge-sm">
                                             <i class="ti ti-point-filled me-1"></i>${row.status == 1 ? _l('admin.common.active') : _l('admin.common.inactive') }
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
                                                 ${ hasPermission(permissions, 'system_settings', 'edit') ?

                                                `<li>
                                                    <a class="dropdown-item rounded-1" href="javascript:void(${row.id});" id="editTemplate" data-id="${row.id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</a>
                                                </li>`:''}
                                                    ${ hasPermission(permissions, 'system_settings', 'delete') ?

                                                `<li>
                                                    <a class="dropdown-item rounded-1" href="javascript:void(${row.id});" id="deleteTemplate" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</a>
                                                </li>`:''}
                                            </ul>
                                        </div>`;
                         },
                         visible: hasPermission(permissions, 'system_settings', 'edit') || hasPermission(permissions, 'system_settings', 'delete')

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

        $(document).on('click','#editTemplate', function(){
            let id = $(this).data('id');
            $.ajax({
               type:"GET",
               url:"/admin/settings/get_email_template/"+id,
               success:function(response){
                  $("#title").val(response.data.title);
                  $("#notification_type").val(response.data.notification_type).trigger('change');
                  $("#subject").val(response.data.subject);
                  $("#sms_content").val(response.data.sms_content);
                  $("#description").summernote('code',response.data.description);
                  if(response.data.status === 1){
                    $("#status").prop('checked',true);
                  }else{
                    $("status").prop('checked',false);
                  }
                  $("#add_email input#id").val(response.data.id);
                  $("#modalfootdiv").removeClass('justify-content-end');
                  $("#modalfootdiv").addClass("justify-content-between");
                  $("#status_div").removeClass('d-none');
                  $("#description_error").text('');
                  $('.modal_title').text(_l('admin.general_settings.edit_template'));
                  $('#notification_content').val(response.data.notification_content);
                  $('.savebtn').text(_l('admin.common.save_changes'));
                  $("#add_email").modal('show');
               }
            });
        });

        $(document).on('click','#add_new_template', function(){
            $("#mailTemplateForm")[0].reset();
            $("#modalfootdiv").removeClass('justify-content-between');
            $("#modalfootdiv").addClass('justify-content-end');
            $("#status_div").addClass('d-none');
            $("#description_error").text('');
            $("#description").summernote('code','');
            $("#notification_type").val('').trigger('change');
            $('.modal_title').text(_l('admin.general_settings.create_template'));
            $('.savebtn').text(_l('admin.common.create_new'));
        });

        $(document).on('click','#deleteTemplate', function(){
            let delete_id = $(this).data('id');
            $("#deleteForm #delete_id").val(delete_id);
        });

        $("#deleteForm").on("submit", function(e){
            e.preventDefault();
            $("#deleteForm .submitbtn").prop('disabled',true);
            $("#deleteForm .submitbtn").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> Deleting..');
            $.ajax({
                type:"POST",
                url:"/admin/settings/delete-emailtemplate",
                data:$("#deleteForm").serialize(),
                success:function(response){
                    $("#deleteForm .submitbtn").prop('disabled',false);
                    $("#deleteForm .submitbtn").text( _l('admin.general_settings.yes_delete'),);
                    $("#delete-modal").modal("hide");
                    table.ajax.reload();
                    showToast('success',response.message);
                },
                error:function(error){
                    showToast('error',error.responseJSON.message);
                    $("#deleteForm .submitbtn").prop('disabled',false);
                    $("#deleteForm .submitbtn").text( _l('admin.general_settings.yes_delete'),);
                    $("#delete-modal").modal("hide");
                }
            });
        });

        $(document).on('click','#viewTemplate', function(e){
              e.preventDefault();
              let id = $(this).data('id');
              $.ajax({
                  type:"GET",
                  url:"/admin/settings/get_email_template/"+id,
                  success:function(response){

                    let description = response.data.description;
                    let regex = /{([a-zA-Z0-9_]+)}/g;


                    description = description.replace(regex, (match, placeholder) => {
                        return `<span class="text-info var_placeholder" data-placeholder="${placeholder}">${match}</span>`;
                    });


                    $("#view_template_title").text(response.data.title);
                    $("#preview_box").html(description);


                  }
              });
              $("#view_template").modal('show');
        });
    });
})();

$(document).on("change", "#notification_type", function () {
    let value = $(this).val();
    if(value){
        getTags(value);
    }
});

function getTags(id){
    $.ajax({
        type: "GET",
        url: "/admin/settings/get_tags/" + id,
        success: function (response) {
            if (response.status === 'success') {
                let tags = response.tags;
                let placeholders = '';
                if (tags && tags.length > 0) {
                    placeholders = tags.map(tag =>
                        `<span class="var_placeholder btn btn-light text-info btn-sm" data-placeholder="${tag}">{${tag}}</span>`
                    ).join('');
                }
                $("#placeholders").html(placeholders);
            }

        }
    });
}
