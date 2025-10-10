/* global $, loadTranslationFile, loadUserPermissions, intlTelInput, document, showToast, _l, window, FormData, Image, URL, hasPermission, FileReader  */
(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, user_management");
    const permissions = await loadUserPermissions();
    let international_phone_number = "";
    let initialPhoneNumber = null;
    $(document).ready(function () {
        initTable();
        initInternationalPhoneInput();
        initSelect();
        initValidation();
        initEvents();
        function initInternationalPhoneInput() {
            const userPhoneInput = document.querySelector(".user_phone_number");
            const intlPhoneInput = document.querySelector("#international_phone_number");

            if (userPhoneInput) {
                const iti = intlTelInput(userPhoneInput, {
                    utilsScript: window.location.origin + "/backend/assets/plugins/intltelinput/js/utils.js",
                    separateDialCode: true,
                    placeholderNumberType: "",
                    autoPlaceholder: "off",
                    formatOnDisplay: false,
                });

                userPhoneInput.classList.add("iti");
                userPhoneInput.parentElement.classList.add("intl-tel-input");

                document.querySelector("#userForm").addEventListener("submit", function (event) {
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

        function initSelect() {
            $(".role").select2({
                dropdownParent: $("#add_user_modal"),
            });
            $(".edit_role").select2({
                dropdownParent: $("#edit_user_modal"),
            });
        }

        function initValidation(){
            $("#userForm").validate({
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 30,
                        pattern: /^[A-Za-z\s]+$/,
                    },
                    last_name: {
                        required: true,
                        maxlength: 30,
                        pattern: /^[A-Za-z\s]+$/,
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
                    role_id: {
                        required: true,
                    },
                    password: {
                        required: true,
                        minlength: 8,
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages:{
                    first_name: {
                        required: _l("admin.common.first_name_required"),
                        minlength: _l("admin.common.first_name_minlength", {min: 3}),
                        maxlength: _l("admin.common.first_name_maxlength", {max: 30}),
                        pattern: _l("admin.common.alpha_space_allowed"),
                    },
                    last_name: {
                        required: _l("admin.common.last_name_required"),
                        minlength: _l("admin.common.last_name_minlength", {min: 3}),
                        maxlength: _l("admin.common.last_name_maxlength", {max: 30}),
                        pattern: _l("admin.common.alpha_space_allowed"),
                    },
                    image: {
                        required: _l("admin.common.image_required"),
                        extension: _l("admin.common.image_format"),
                        filesize: _l("admin.common.image_size", {size: 2}),
                    },
                    phone_number: {
                        required: _l("admin.common.phone_number_required"),
                        minlength: _l("admin.common.phone_number_minlength"),
                        maxlength: _l("admin.common.phone_number_maxlength"),
                    },
                    email: {
                        required: _l("admin.common.email_required"),
                        email: _l("admin.common.email_valid"),
                    },
                    role_id: {
                        required: _l("admin.user_management.role_required"),
                    },
                    password: {
                        required: _l("admin.common.password_required"),
                        minlength: _l("admin.common.password_minlength"),
                    },
                    confirm_password: {
                        required: _l("admin.common.confirm_password_required"),
                        equalTo: _l("admin.common.confirm_password_equal_to"),
                    }
                },
                errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    if (element.hasClass("select2-hidden-accessible")) {
                        $("#" + errorId).text(error.text());
                    } else {
                        $("#" + errorId).text(error.text());
                    }
                },
                highlight: function (element) {
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).next(".select2-container").addClass("is-invalid").removeClass("is-valid");
                    }
                    $(element).addClass("is-invalid").removeClass("is-valid");
                    $("#" + element.id).siblings("span").addClass("me-3");
                },
                unhighlight: function (element) {
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).next(".select2-container").removeClass("is-invalid").addClass("is-valid");
                    }
                    $(element).removeClass("is-invalid").addClass("is-valid");
                    $("#" + element.id).siblings("span").addClass("me-3");
                    const errorId = element.id + "_error";
                    $("#" + errorId).text("");
                },
                onkeyup: function(element) {
                    $(element).valid();
                    $("#" + element.id).siblings("span").removeClass("me-3");
                },
                onchange: function(element) {
                    $(element).valid();
                    $("#" + element.id).siblings("span").removeClass("me-3");
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    formData.set("phone_number", international_phone_number);

                    $.ajax({
                        type: "POST",
                        url: "/admin/user/save",
                        data: formData,
                        enctype: "multipart/form-data",
                        processData: false,
                        contentType: false,
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                        },
                        beforeSend: handleBeforeSend,
                        success: handleSuccess,
                        error: handleError
                    });
                }
            });

            function handleBeforeSend() {
                $(".submitbtn").attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
                `);
            }

            function handleSuccess(resp) {
                resetFormState();

                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $("#add_user_modal").modal("hide");
                    $("#edit_user_modal").modal("hide");
                    $("#userTable").DataTable().ajax.reload();
                }
            }

            function handleError(error) {
                resetFormState();

                if (error.responseJSON.code === 422) {
                    $.each(error.responseJSON.errors, function (key, val) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(val[0]);
                    });
                } else {
                    showToast("error", error.responseJSON.message);
                }
            }

            function resetFormState() {
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                $(".submitbtn").removeAttr("disabled").html(_l("admin.common.create_new"));
            }

            $("#editUserForm").validate({
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 30,
                        pattern: /^[A-Za-z\s]+$/,
                    },
                    last_name: {
                        required: true,
                        maxlength: 30,
                        pattern: /^[A-Za-z\s]+$/,
                    },
                    image: {
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
                    role_id: {
                        required: true,
                    },
                },
                messages:{
                    first_name: {
                        required: _l("admin.common.first_name_required"),
                        minlength: _l("admin.common.first_name_minlength", {min: 3}),
                        maxlength: _l("admin.common.first_name_maxlength", {max: 30}),
                        pattern: _l("admin.common.alpha_space_allowed"),
                    },
                    last_name: {
                        required: _l("admin.common.last_name_required"),
                        minlength: _l("admin.common.last_name_minlength", {min: 3}),
                        maxlength: _l("admin.common.last_name_maxlength", {max: 30}),
                        pattern: _l("admin.common.alpha_space_allowed"),
                    },
                    image: {
                        required: _l("admin.common.image_required"),
                        extension: _l("admin.common.image_format"),
                        filesize: _l("admin.common.image_size", {size: 2}),
                    },
                    phone_number: {
                        required: _l("admin.common.phone_number_required"),
                        minlength: _l("admin.common.phone_number_minlength"),
                        maxlength: _l("admin.common.phone_number_maxlength"),
                    },
                    email: {
                        required: _l("admin.common.email_required"),
                        email: _l("admin.common.email_valid"),
                    },
                    role_id: {
                        required: _l("admin.user_management.role_required"),
                    },
                },
                errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    if (element.hasClass("select2-hidden-accessible")) {
                        $("#" + errorId).text(error.text());
                    }
                },
                highlight: function (element) {
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).next(".select2-container").addClass("is-invalid").removeClass("is-valid");
                    }
                    $(element).addClass("is-invalid").removeClass("is-valid");
                    $("#" + element.id).siblings("span").addClass("me-3");
                },
                unhighlight: function (element) {
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).next(".select2-container").removeClass("is-invalid").addClass("is-valid");
                    }
                    $(element).removeClass("is-invalid").addClass("is-valid");
                    $("#" + element.id).siblings("span").addClass("me-3");
                    const errorId = element.id + "_error";
                    $("#" + errorId).text("");
                },
                onkeyup: function(element) {
                    $(element).valid();
                    $("#" + element.id).siblings("span").removeClass("me-3");
                },
                onchange: function(element) {
                    $(element).valid();
                    $("#" + element.id).siblings("span").removeClass("me-3");
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    formData.set("phone_number", $("#edit_international_phone_number").val());
                    formData.set("status", $("#status").is(":checked") ? 1 : 0);

                    $.ajax({
                        type: "POST",
                        url: "/admin/user/save",
                        data: formData,
                        enctype: "multipart/form-data",
                        processData: false,
                        contentType: false,
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                        },
                        beforeSend: handleBeforeSend,
                        success: handleSuccess,
                        error: handleError
                    });
                }
            });

            $.validator.addMethod("filesize", function (value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param * 1024;
            }, "File size must be less than {0} KB.");
        }

        function initEvents() {
            $("#add_user").on("click", function() {
                $("#userForm")[0].reset();
                $("#id").val("");
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                $("#role_id").val("").trigger("change");
                $(".upload_icon").removeClass("d-none");
                $("#imagePreview").addClass("d-none");
                $(".submitbtn").text(_l("admin.common.create_new"));
            });

            $("#image").on("change", handleImageChange);

            $("#edit_image").on("change", handleEditImageChange);

            $("#phone_number, #edit_phone_number").on("input", function () {
                $(this).val($(this).val().replace(/\D/g, ""));
            });

            $(document).on("click", ".dataTables_paginate a", function() {
                $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
            });

            $(document).on("keyup", "#search", function() {
                $("#userTable").DataTable().ajax.reload();
            });

            $(document).on("click", ".sort_by_list .dropdown-item", function () {
                let sortBy = $(this).data("sort");
                $("#sort_by_input").val(sortBy);
                $("#current_sort").text(sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase());
                $(".sort_by_list .dropdown-item").removeClass("active");
                $(this).addClass("active");
                $("#userTable").DataTable().ajax.reload();
            });

            $(document).on("click", "#apply_filter", function () {
                $("#userTable").DataTable().ajax.reload();
            });

            $(document).on("click", "#reset_filter", function () {
                $("#role_list input:checkbox").prop("checked", false);
                $("#sort_by_input").val("");
                $("#userTable").DataTable().ajax.reload();
            });

            $("#deleteUserForm").on("submit", function(e){
                e.preventDefault();
                $.ajax({
                    url:"/admin/user/delete",
                    type:"POST",
                    data: {
                        id: $("#delete_id").val()
                    },
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                    },
                    success: handleDeleteSuccess,
                    error: handleDeleteError
                });
            });

            $(document).on("click", ".editUser", function() {
                let id = $(this).data("id");
                $("#editUserForm").trigger("reset");
                $(".submitbtn").text(_l("admin.common.save_changes"));

                $.ajax({
                type:"GET",
                url:"/admin/user/edit/"+id,
                success: function(response) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                        if(response.code === 200){
                            let data = response.data;

                            $("#id").val(data.id);
                            $("#edit_first_name").val(data.first_name);
                            $("#edit_last_name").val(data.last_name);
                            $("#edit_email").val(data.email);
                            $("#edit_role_id").val(data.role_id).trigger("change");
                            $("#status").prop("checked", data.status == 1);


                            if (data.profile_image) {
                                $("#editImagePreview").attr("src", data.profile_image).removeClass("d-none");
                                $(".upload_icon").addClass("d-none");
                            } else {
                                $(".upload_icon").removeClass("d-none");
                                $("#editImagePreview").addClass("d-none");
                            }

                            const phoneNumber = data.phone_number ? data.phone_number.trim() : data.phone_number;
                            const phoneInput = document.querySelector(".edit_user_phone_number");
                            const hiddenInput = document.querySelector("#edit_international_phone_number");

                            if ($(phoneInput).data("itiInstance")) {
                                $(phoneInput).data("itiInstance").destroy();
                            }
                            const iti = intlTelInput(phoneInput, {
                                utilsScript: window.location.origin + "/backend/assets/plugins/intltelinput/js/utils.js",
                                separateDialCode: true,
                                placeholderNumberType: "",
                                autoPlaceholder: "off",
                                formatOnDisplay: false,
                            });
                            $(phoneInput).data("itiInstance", iti);

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
                            $("#edit_user_modal").modal("show");
                        }
                }
                });
            });

            $(document).on("click", ".deleteUser", function() {
                let id = $(this).data("id");
                $("#delete_id").val(id);
            });
        }

        function handleImageChange(event) {
            const input = event.target;

            if ($(input).val() !== "") {
                $(input).valid();
            }

            readAndPreviewImage(input);
            validateImageDimensions(input);
        }

        // --- Read file and preview ---
        function readAndPreviewImage(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                $("#imagePreview")
                    .attr("src", e.target.result)
                    .removeClass("d-none");
                $(".upload_icon").addClass("d-none");
            };
            reader.readAsDataURL(file);
        }

        // --- Validate image dimensions ---
        function validateImageDimensions(input) {
            const file = input.files[0];
            if (!file) return;

            const img = new Image();
            const objectURL = URL.createObjectURL(file);

            img.onload = function () {
                if (this.width < 180 || this.height < 180) {
                    $("#image_error").text(
                        _l("admin.common.image_pixel", { width: 180, height: 180 })
                    );
                    $("#image").addClass("is-invalid").removeClass("is-valid");
                }
                URL.revokeObjectURL(objectURL);
            };
            img.src = objectURL;
        }

        function handleEditImageChange(event) {
            const input = event.target;

            if ($(input).val() !== "") {
                $(input).valid();
            }

            previewEditImage(input);
            validateEditImageDimensions(input);
        }

        // --- Preview the selected image ---
        function previewEditImage(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                $("#editImagePreview")
                    .attr("src", e.target.result)
                    .removeClass("d-none");
                $(".upload_icon").addClass("d-none");
            };
            reader.readAsDataURL(file);
        }

        // --- Validate image dimensions ---
        function validateEditImageDimensions(input) {
            const file = input.files[0];
            if (!file) return;

            const img = new Image();
            const objectURL = URL.createObjectURL(file);

            img.onload = function () {
                if (this.width < 180 || this.height < 180) {
                    $("#edit_image_error").text(
                        _l("admin.common.image_pixel", { width: 180, height: 180 })
                    );
                    $("#edit_image").addClass("is-invalid").removeClass("is-valid");
                }
                URL.revokeObjectURL(objectURL);
            };

            img.src = objectURL;
        }

        function handleDeleteSuccess(response) {
            if (response.code === 200) {
                showToast("success", response.message);
                $("#delete_modal").modal("hide");
                $("#userTable").DataTable().ajax.reload();
            }
        }

        // --- Error callback ---
        function handleDeleteError(res) {
            if (res.responseJSON?.code === 500) {
                showToast("error", res.responseJSON.message);
            } else {
                showToast("error", _l("admin.common.default_delete_error"));
            }
        }

        function getUserTableData(d) {
            d.search = $("#search").val();
            d.sort_by = $("#sort_by_input").val();
            d.role_ids = $(".role_checkbox:checked").map(function () {
                return $(this).val();
            }).get();
        }

        // --- AJAX headers ---
        function getAjaxHeaders() {
            return {
                "Accept": "application/json",
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
            };
        }

        // --- Error handler ---
        function handleUserTableError(error) {
            if (error.responseJSON?.code === 500) {
                showToast("error", error.responseJSON.message);
            } else {
                showToast("error", _l("admin.common.default_retrieve_error"));
            }
        }

        // --- Before sending AJAX ---
        function showTableLoader() {
            $(".table-loader").show();
            $(".real-table, .table-footer").addClass("d-none");
        }

        // --- After AJAX completes ---
        function hideTableLoader() {
            $(".table-loader, .input-loader, .label-loader").hide();
            $(".real-table, .real-label, .real-input").removeClass("d-none");

            if ($("#userTable").DataTable().rows().count() === 0) {
                $(".table-footer").addClass("d-none");
            } else {
                $(".table-footer").removeClass("d-none");
            }
        }
        function initTable() {
            $("#userTable").DataTable({
                serverSide: true,
                destroy: true,
                processing: false,
                ajax: {
                    url: "/admin/user/list",
                    type: "POST",
                    data: getUserTableData,
                    headers: getAjaxHeaders(),
                    error: handleUserTableError,
                    beforeSend: showTableLoader,
                    complete: hideTableLoader
                },
                columns: [
                    { data: "full_name",
                        render: function (data, type, row) {
                        return `
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                    <img src="${row.profile_image}" class="rounded-circle" alt="Profile Image">
                                </a>
                                <h6><a href="javascript:void(0);" class="fs-14 fw-semibold">${row.full_name ? row.full_name : ""}</a></h6>
                            </div>`;
                    }},
                    { data: "phone_number" },
                    { data: "email" },
                    { data: "role_name" },
                    { data: "status",
                        render: function (data, type, row) {
                            return `
                                <span class="badge ${(row.status == 1) ? "badge-success-transparent" : "badge-danger-transparent"} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${(row.status == 1) ? _l("admin.common.active") : _l("admin.common.inactive")}
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
                                            ${ hasPermission(permissions, "users", "edit") ?
                                            `<li>
                                                <a class="dropdown-item rounded-1 editUser" href="javascript:void(0);" data-id="${row.id}"><i class="ti ti-edit me-1"></i>${_l("admin.common.edit")}</a>
                                            </li>` : ""}
                                            ${ hasPermission(permissions, "users", "delete") ?
                                            `<li>
                                                <a class="dropdown-item rounded-1 deleteUser" href="javascript:void(0);" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}</a>
                                            </li>` : ""}
                                        </ul>
                                </div>`;
                        },
                        visible: hasPermission(permissions, "users", "edit") || hasPermission(permissions, "users", "delete"),
                    },
                ],
                order: [[0, "asc"]],
                ordering: true,
                searching: false,
                pageLength: 10,
                lengthChange: false,
                responsive: false,
                autoWidth: false,
                drawCallback: function () {
                    customizeTableFooter($(this));
                },
                language: getDataTableLanguage(),
            });
        }
    });

}) ();