(async () => {
    "use strict";
    
    await loadTranslationFile('admin', 'common, manage');
    const permissions = await loadUserPermissions();
    let international_phone_number;
    $(document).ready(function() {
        initTable();
        initInternationalPhoneInput();
        initDatePicker();
        initValidation();
        initEvents();
        ucfirst();
        function initInternationalPhoneInput() {
            const userPhoneInput = document.querySelector(".driver_phone_number");
            const intlPhoneInput = document.querySelector("#international_phone_number");

            if (userPhoneInput) {
                const iti = intlTelInput(userPhoneInput, {
                    utilsScript: window.location.origin + "/backend/assets/plugins/intltelinput/js/utils.js",
                    separateDialCode: true,
                });

                userPhoneInput.classList.add("iti");
                userPhoneInput.parentElement.classList.add("intl-tel-input");

                document.querySelector("#driverForm").addEventListener("submit", function (event) {
                    event.preventDefault();

                    const intlNumber = iti.getNumber();
                    if (intlNumber) {
                        intlPhoneInput.value = intlNumber;
                        international_phone_number = intlNumber;
                    } else {
                        intlPhoneInput.value = userPhoneInput.value.trim();
                        international_phone_number = intlPhoneInput.value;
                    }
                });
            }
        }

        function initDatePicker() {
            $('.date_of_issue').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: 'fas fa-angle-right',
                    previous: 'fas fa-angle-left'
                },
                maxDate: moment().subtract(1, 'days').endOf('day')
            });

            $('.valid_date').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: 'fas fa-angle-right',
                    previous: 'fas fa-angle-left'
                },
                minDate: moment().startOf('day')
            });

            $("#sort_by_date").val('');
        }
       
        function initValidation(){
            $("#driverForm").validate({
                rules: {
                    driver_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        pattern: /^[A-Za-z\s]+$/,
                    },
                    gender: {
                        required: true,
                    },
                    image: {
                        required: true,
                        extension: "jpeg|jpg|png",
                        filesize: 2048,
                    },
                    phone_number: {
                        required: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    address: {
                        required: true,
                        maxlength: 150
                    },
                    "assigned_cars[]": {
                        required: true,
                    },
                    card_number: {
                        required: true,
                        minlength: 12,
                        maxlength: 25,
                    },
                    date_of_issue: {
                        required: true,
                    },
                    valid_date: {
                        required: true,
                    },
                    "documents[]": {
                        required: true,
                        extension: "jpeg|jpg|png|pdf|doc|docx",
                        filesize: 5120,
                    },
                },
                messages:{
                    driver_name: {
                        required: _l('admin.manage.driver_name_required'),
                        minlength: _l('admin.manage.driver_name_minlength'),
                        maxlength: _l('admin.manage.driver_name_maxlength'),
                        pattern: _l('admin.manage.driver_name_alpha_space'),
                    },
                    gender: {
                        required: _l('admin.manage.gender_required'),
                    },
                    image: {
                        required: _l('admin.common.image_required'),
                        extension: _l('admin.common.image_format'),
                        filesize: _l('admin.common.image_size', {size: 2}),
                    },
                    phone_number: {
                        required: _l('admin.common.phone_number_required'),
                        minlength: _l('admin.common.phone_number_minlength'),
                        maxlength: _l('admin.common.phone_number_maxlength'),
                    },
                    email: {
                        required: _l('admin.common.email_required'),
                        email: _l('admin.common.email_valid'),
                    },
                    address: {
                        required: _l('admin.manage.address_required'),
                        maxlength: _l('admin.manage.address_maxlength'),
                    },
                    "assigned_cars[]": {
                        required: _l('admin.manage.assigned_vehicles_required'),
                    },
                    card_number: {
                        required: _l('admin.manage.card_number_required'),
                        minlength: _l('admin.manage.card_number_minlength'),
                        maxlength: _l('admin.manage.card_number_maxlength'),
                    },
                    date_of_issue: {
                        required: _l('admin.manage.date_of_issue_required'),
                    },
                    valid_date: {
                        required: _l('admin.manage.valid_date_required'),
                    },
                    "documents[]": {
                        required: _l('admin.manage.documents_required'),
                        extension: _l('admin.manage.documents_format'),
                        filesize: _l('admin.manage.documents_size', {size: 5}),
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
                    let formData = new FormData(form);
                    formData.set('phone_number', international_phone_number);

                    $.ajax({
                        type:"POST",
                        url:"/admin/driver/save",
                        data: formData,
                        enctype: "multipart/form-data",
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
                            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                            $(".submitbtn").removeAttr("disabled").html(_l('admin.common.create_new'));
                            if (resp.code === 200) {
                                showToast('success', resp.message);
                                $("#add_driver_modal").modal('hide');
                                $("#driverTable").DataTable().ajax.reload();
                            }
                        },
                        error:function(error){
                            $(".error-text").text("");
                            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                            $(".submitbtn").removeAttr("disabled").html(_l('admin.common.create_new'));
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

            $("#editDriverForm").validate({
                rules: {
                    driver_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        pattern: /^[A-Za-z\s]+$/,
                    },
                    gender: {
                        required: true,
                    },
                    image: {
                        extension: "jpeg|jpg|png",
                        filesize: 2048,
                    },
                    phone_number: {
                        required: true,
                        minlength: 10,
                        maxlength: 15,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    address: {
                        required: true,
                        maxlength: 150
                    },
                    "assigned_cars[]": {
                        required: true,
                    },
                    card_number: {
                        required: true,
                        minlength: 12,
                        maxlength: 25,
                    },
                    date_of_issue: {
                        required: true,
                    },
                    valid_date: {
                        required: true,
                    },
                    "documents[]": {
                        extension: "jpeg|jpg|png|pdf|doc|docx",
                        filesize: 5120,
                    },
                },
                messages:{
                    driver_name: {
                        required: _l('admin.manage.driver_name_required'),
                        minlength: _l('admin.manage.driver_name_minlength'),
                        maxlength: _l('admin.manage.driver_name_maxlength'),
                        pattern: _l('admin.manage.driver_name_alpha_space'),
                    },
                    gender: {
                        required: _l('admin.manage.gender_required'),
                    },
                    image: {
                        required: _l('admin.common.image_required'),
                        extension: _l('admin.common.image_format'),
                        filesize: _l('admin.common.image_size', {size: 2}),
                    },
                    phone_number: {
                        required: _l('admin.common.phone_number_required'),
                        minlength: _l('admin.common.phone_number_minlength'),
                        maxlength: _l('admin.common.phone_number_maxlength'),
                    },
                    email: {
                        required: _l('admin.common.email_required'),
                        email: _l('admin.common.email_valid'),
                    },
                    address: {
                        required: _l('admin.manage.address_required'),
                        maxlength: _l('admin.manage.address_maxlength'),
                    },
                    "assigned_cars[]": {
                        required: _l('admin.manage.assigned_vehicles_required'),
                    },
                    card_number: {
                        required: _l('admin.manage.card_number_required'),
                        minlength: _l('admin.manage.card_number_minlength'),
                        maxlength: _l('admin.manage.card_number_maxlength'),
                    },
                    date_of_issue: {
                        required: _l('admin.manage.date_of_issue_required'),
                    },
                    valid_date: {
                        required: _l('admin.manage.valid_date_required'),
                    },
                    "documents[]": {
                        required: _l('admin.manage.documents_required'),
                        extension: _l('admin.manage.documents_format'),
                        filesize: _l('admin.manage.documents_size', {size: 5}),
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
                    let formData = new FormData(form);
                    formData.set('status', $("#status").is(":checked") ? 1 : 0);
                    formData.set('phone_number', $("#edit_international_phone_number").val());

                    $.ajax({
                        type:"POST",
                        url:"/admin/driver/save",
                        data: formData,
                        enctype: "multipart/form-data",
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
                            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                            $(".submitbtn").removeAttr("disabled").html(_l('admin.common.save_changes'));
                            if (resp.code === 200) {
                                showToast('success', resp.message);
                                $("#edit_driver_modal").modal('hide');
                                $("#driverTable").DataTable().ajax.reload();
                            }
                        },
                        error:function(error){
                            $(".error-text").text("");
                            $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                            $(".submitbtn").removeAttr("disabled").html(_l('admin.common.save_changes'));

                            if (error.responseJSON.code === 422) {
                                $.each(error.responseJSON.errors, function(key, val) {
                                    $("#edit_" + key).addClass("is-invalid");
                                    $("#edit_" + key + "_error").text(val[0]);
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
        }

        function initEvents() {
            $('#image').on('change', function (event) {
                if ($(this).val() !== '') {
                    $(this).valid();
                }
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                    $('.upload_icon').addClass('d-none');
                };
                reader.readAsDataURL(event.target.files[0]);
                var file = this.files[0];
                if (file) {
                    var img = new Image();
                    var objectURL = URL.createObjectURL(file);

                    img.onload = function () {
                        if (this.width < 180 || this.height < 180) {
                            $("#image_error").text("Image must be at least 180x180 pixels.");
                            $("#image").addClass("is-invalid").removeClass("is-valid");
                        }
                        URL.revokeObjectURL(objectURL);
                    };
                    img.src = objectURL;
                }
            });

            $('#edit_image').on('change', function (event) {
                if ($(this).val() !== '') {
                    $(this).valid();
                }
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#editImagePreview').attr('src', e.target.result).removeClass('d-none');
                    $('.upload_icon').addClass('d-none');
                };
                reader.readAsDataURL(event.target.files[0]);
                var file = this.files[0];
                if (file) {
                    var img = new Image();
                    var objectURL = URL.createObjectURL(file);
                    img.onload = function () {
                        if (this.width < 180 || this.height < 180) {
                            $("#edit_image_error").text("Image must be at least 180x180 pixels.");
                            $("#edit_image").addClass("is-invalid").removeClass("is-valid");
                        }
                        URL.revokeObjectURL(objectURL);
                    };
                    img.src = objectURL;
                }
            });
            $(document).on('click', '.dataTables_paginate a', function() {
                $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
            });

            $(document).on('keyup', '#search', function() {
                $('#driverTable').DataTable().ajax.reload();
            });

            $(document).on('click', '.sort_by_list .dropdown-item', function () {
                let sortBy = $(this).data('sort');
                $('#sort_by_input').val(sortBy);
                $('#current_sort').text(sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase());
                $('.sort_by_list .dropdown-item').removeClass('active');
                $(this).addClass('active');
                $('#driverTable').DataTable().ajax.reload();
            });

            $(document).on('click', '#statusList .dropdown-item', function () {
                let sortBy = $(this).data('sort');
                $('#sort_by_status').val(sortBy);
                if (sortBy == 1) {
                    $('#current_sort_status').text('Active');
                } else {
                    $('#current_sort_status').text('Inactive');
                }
                $('#statusList .dropdown-item').removeClass('active');
                $(this).addClass('active');
                $('#driverTable').DataTable().ajax.reload();
            });

            $('#sort_by_date').on('change', function() {
                var sort_by_date = $(this).val();
                initTable(sort_by_date);
            });

            $("#add_driver").on('click', function() {
                $("#driverForm")[0].reset();
                $("#id").val('');
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                $('#assigned_cars').val('').trigger('change');
                $(".upload_icon").removeClass('d-none');
                $('#imagePreview').addClass('d-none');
                $('.submitbtn').text(_l('admin.common.create_new'));
            });

            $('#gender').on('change', function () {
                $(this).valid();
            });
            $('#assigned_cars').on('change', function () {
                $(this).valid();
            });
            $("#phone_number").on("input", function () {
                $(this).val($(this).val().replace(/[^0-9]/g, ""));
            });
            $("#card_number").on("input", function () {
                $(this).val($(this).val().replace(/[^a-zA-Z0-9]/g, ""));
            });

            $('#edit_gender').on('change', function () {
                $(this).valid();
            });
            $('#edit_assigned_cars').on('change', function () {
                $(this).valid();
            });
            $("#edit_phone_number").on("input", function () {
                $(this).val($(this).val().replace(/[^0-9]/g, ""));
            });
            $("#edit_card_number").on("input", function () {
                $(this).val($(this).val().replace(/[^a-zA-Z0-9]/g, ""));
            });

            let removedDocuments = [];
            $(document).on('click', '.remove-document', function() {
                let documentId = $(this).data('id');
                removedDocuments.push(documentId);
                $("#removed_documents").val(removedDocuments.join(","));
                $(this).closest('.document-preview').remove();
            });

            $("#driverDeleteForm").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url:"/admin/driver/delete",
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
                            $("#driverTable").DataTable().ajax.reload();
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

            $('#select-all').on('change', function () {
                $('.select-multiple').prop('checked', $(this).prop('checked'));
            });

            $('.bulk_status_change').on('click', function () {
                let selectedIds = [];
                let status = $(this).data('status');

                $('.select-multiple:checked').each(function () {
                    var id = $(this).val();
                    if (id) {
                        selectedIds.push(id);
                    }
                });

                if (selectedIds.length === 0) {
                    showToast('error', _l('admin.common.select_atleast_one_item_change_status'));
                    return;
                }

                $.ajax({
                    url: '/admin/driver/status-change',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ids: selectedIds,
                        status: status
                    },
                    success: function (response) {
                        if(response.code === 200){
                            showToast('success', response.message);
                            $("#driverTable").DataTable().ajax.reload();
                            $('#select-all').prop('checked', false);
                        }
                    },
                    error: function () {
                        if(res.responseJSON.code === 500){
                            showToast('error', res.responseJSON.message);
                        } else {
                            showToast('error', _l('admin.common.default_status_error'));
                        }
                    },
                });
            });

            let initialPhoneNumber = null;

            $(document).on('click', '.edit-driver', function() {
                let id = $(this).data('id');
                $('#editDriverForm').trigger('reset');
                $('.submitbtn').text(_l('admin.common.save_changes'));

                removedDocuments = [];
                $.ajax({
                type:"GET",
                url:"/admin/driver/edit/"+id,
                success: function(response) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                        if(response.code === 200){
                            let data = response.data;

                            $("#id").val(data.id);
                            $("#edit_driver_name").val(data.driver_name);
                            $("#edit_gender").val(data.gender).trigger('change');
                            $("#edit_email").val(data.email);
                            $("#edit_address").val(data.address);
                            $("#edit_date_of_issue").val(data.date_of_issue);
                            $("#edit_valid_date").val(data.valid_date);
                            $("#edit_card_number").val(data.card_number);
                            $("#status").prop('checked', data.status == 1);

                            if (data.assigned_cars) {
                                let assignedCars = data.assigned_cars.split(",");
                                $("#edit_assigned_cars").val(assignedCars).trigger('change');
                            }
                            if (data.image) {
                                $('#editImagePreview').attr('src', data.image).removeClass('d-none');
                                $(".upload_icon").addClass('d-none');
                            } else {
                                $(".upload_icon").removeClass('d-none');
                                $('#editImagePreview').addClass('d-none');
                            }
                            $('.document-preview-container').empty();
                            if (data.documents && data.documents.length > 0) {
                                $.each(response.data.documents, function(index, value) {
                                    $('.document-preview-container').append(
                                        `<div class="document-preview me-2">
                                            <a href="/storage/${value.document}" target="_blank" class="btn btn-sm btn-light me-0" ><i class="ti ti-file-text fs-40"></i></a>
                                            <button type="button" class="btn btn-sm btn-light remove-document" data-id="${value.id}"><i class="ti ti-trash"></i></button>
                                        </div>`
                                    );
                                });
                            }

                            const phoneNumber = data.phone_number ? data.phone_number.trim() : data.phone_number;
                            const phoneInput = document.querySelector(".edit_driver_phone_number");
                            const hiddenInput = document.querySelector("#edit_international_phone_number");

                            if ($(phoneInput).data('itiInstance')) {
                                $(phoneInput).data('itiInstance').destroy();
                            }
                            const iti = intlTelInput(phoneInput, {
                                utilsScript: window.location.origin + "/backend/assets/plugins/intltelinput/js/utils.js",
                                separateDialCode: true,
                            });
                            $(phoneInput).data('itiInstance', iti);

                            if (phoneNumber) {
                                iti.setNumber(phoneNumber);
                                hiddenInput.value = iti.getNumber();
                                initialPhoneNumber = phoneNumber;
                            }
                            const updateHiddenPhoneNumber = () => {
                                const currentPhoneNumber = iti.getNumber();
                                if (currentPhoneNumber !== initialPhoneNumber) {
                                    hiddenInput.value = currentPhoneNumber.trim();
                                }
                            };

                            phoneInput.addEventListener("input", updateHiddenPhoneNumber);
                            phoneInput.addEventListener("countrychange", updateHiddenPhoneNumber);

                            if (!hiddenInput.value) {
                                hiddenInput.value = initialPhoneNumber;
                            }

                            $("#edit_driver_modal").modal('show');

                        }
                }
                });
            });

            $(document).on('click', '.delete-driver', function() {
                let id = $(this).data('id');
                $("#delete_id").val(id);
            });
        }
        
        function ucfirst(str) {
            if (typeof str !== 'string' || !str.length) return '';
            return str[0].toUpperCase() + str.slice(1);
        }

        function initTable(sortByDate = '') {
            $("#driverTable").DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                processing: false,
                ajax: {
                    url: '/admin/driver/list',
                    type: "POST",
                    data: function (d) {
                        d.search = $("#search").val();
                        d.sort_by_status = $("#sort_by_status").val();
                        d.sort_by_date = sortByDate;
                        d.sort_by = $("#sort_by_input").val();
                    },
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    error: function (error) {
                        if (error.responseJSON && error.responseJSON.code === 500) {
                            showToast("error", error.responseJSON.message);
                        } else {
                            showToast("error", _l("admin.common.default_retrieve_error"));
                        }
                    },
                    beforeSend: function () {
                        $(".table-loader").show();
                        $(".real-table, .table-footer").addClass("d-none");
                    },
                    complete: function () {
                        $(".table-loader, .input-loader, .label-loader").hide();
                        $(".real-table, .real-label, .real-input").removeClass("d-none");

                        if ($("#driverTable").DataTable().rows().count() === 0) {
                            $(".table-footer").addClass("d-none");
                        } else {
                            $(".table-footer").removeClass("d-none");
                        }
                    },
                },
                columns: [
                    { data : "id", orderable: false, searchable: false,
                        render: function (data, type, row) {
                        return `
                            <div class="form-check form-check-md">
                                <input class="form-check-input select-multiple" type="checkbox" value="${row.id}">
                            </div>`;
                    }

                    },
                    { data: "driver_name",
                        render: function (data, type, row) {
                        return `
                            <div class="d-flex align-items-center">
                                <div class="avatar rounded-circle me-2 flex-shrink-0">
                                    <img src="${row.image}" class="rounded-circle" alt="Image Preview">
                                </div>
                                <div>
                                    <h6 class="fs-14 fw-semibold text-black">${ucfirst(row.driver_name)}</h6>
                                    <p>${row.phone_number}</p>
                                </div>
                            </div>`;
                    }},
                    { data: "email" },
                    { data: "card_number" },
                    { data: "valid_date" },
                    { data: "status",
                        render: function (data, type, row) {
                            return `
                                <span class="badge ${(row.status == 1) ? 'badge-success-transparent' : 'badge-danger-transparent'} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${(row.status == 1) ? _l('admin.common.active') : _l('admin.common.inactive')}
                                </span>`;
                        },
                    },
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                    ${ hasPermission(permissions, 'drivers', 'edit') ?
                                        `<li>
                                            <button type="button" class="dropdown-item rounded-1 edit-driver" data-id="${row.id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</button>
                                        </li>`:''}
                                        ${ hasPermission(permissions, 'drivers', 'delete') ?
                                        `<li>
                                            <button type="button" class="dropdown-item rounded-1 delete-driver" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</button>
                                        </li>`:''}
                                    </ul>
                                </div>`;
                        },
                        visible: hasPermission(permissions, 'drivers', 'edit') || hasPermission(permissions, 'drivers', 'delete')

                    },
                ],
                order: [[1, "asc"]],
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
                drawCallback: function () {
                    $(".dataTables_info").addClass("d-none");
                    $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");

                    var tableWrapper = $(this).closest(".dataTables_wrapper");
                    var info = tableWrapper.find(".dataTables_info");
                    var pagination = tableWrapper.find(".dataTables_paginate");

                    $(".table-footer")
                        .empty()
                        .append(
                            $('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                                .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                                .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                        );
                    $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
                },
            });
        }
    });

})();
