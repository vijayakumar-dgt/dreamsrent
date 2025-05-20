(async () => {
    "use strict";
    
    // Load translations and permissions
    await loadTranslationFile('admin', 'common, manage');
    const permissions = await loadUserPermissions();
    let currentStatus;
    // DOM Elements
    const $userPhoneInput = $("#mobile");
    const $intlPhoneInput = $("#international_phone_number");
    const $userProfileForm = $("#locationForm");
    const $addLocationModal = $("#add_location");
    const $deleteModal = $("#delete-modal");
    const $locationTable = $("#locationTable");
    
    // Document ready
    $(document).ready(function() {
        initSelect2();
        handleImagePreview();
        setupFormValidation();
        initTable();
        fetchCountries();
        handleDeleteLocation();
        setupWorkingDaysValidation();
        setupEventHandlers();
    });

    // Initialize phone input
    const initPhoneInput = () => {
        if ($userPhoneInput.length && $userProfileForm.length) {
            const iti = window.intlTelInput($userPhoneInput[0], {
                initialCountry: "auto",
                nationalMode: false,
                utilsScript: `${window.location.origin}/frontend/assets/plugins/intltelinput/js/utils.js`,
                separateDialCode: true,
            });
            
            $userPhoneInput.addClass("iti").parent().addClass("intl-tel-input");
            
            const updatePhoneNumber = () => $intlPhoneInput.val(iti.getNumber());
            $userPhoneInput.on("keyup countrychange", updatePhoneNumber);
            
            return iti;
        }
        return null;
    };
    
    const iti = initPhoneInput();
    
    // Initialize select2 dropdowns
    const initSelect2 = () => {
        $("#country, #state, #city").select2({
            dropdownParent: $addLocationModal,
            placeholder: function() {
                const id = $(this).attr("id");
                return id === "country" ? _l('admin.manage.select_country') :
                       id === "state" ? _l('admin.manage.select_state') : 
                       _l('admin.manage.select_city');
            },
        });
    };
    
    // Image preview handler
    const handleImagePreview = () => {
        $(document).on("change", "#image", function() {
            const $preview = $("#image_preview");
            const $placeholder = $(".image_placeholder");
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => $preview.attr("src", e.target.result);
                reader.readAsDataURL(this.files[0]);
                $preview.removeClass('d-none');
                $placeholder.hide();
            } else {
                $preview.addClass('d-none');
                $placeholder.show();
            }
        });
    };
    
    // Form validation setup
    const setupFormValidation = () => {
        $.extend($.validator.messages, {
            required: _l('admin.common.this_field_is_required'),
        });

        $userProfileForm.validate({
            rules: {
                image: {
                    required: false,
                    filesize: 2048,
                    imageDimension: [180, 180],
                    extension: "jpeg|jpg|png|svg",
                },
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                },
                international_phone_number: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 15,
                },
                address: {
                    required: true,
                },
                country: {
                    required: true,
                },
                state: {
                    required: true,
                },
                city: {
                    required: true,
                },
                pincode: {
                    required: true,//max length: 6,
                    digits: true,
                    minlength: 6,
                    maxlength: 6,
                },
                "working_days[]": {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: _l('admin.manage.name_required'),
                    minlength: _l('admin.manage.name_minlength'),
                    maxlength: _l('admin.manage.name_maxlength'),
                },
                "working_days[]": {
                    required: _l('admin.manage.select_working_days'),
                },
                email: {
                    required: _l('admin.common.email_required'),
                    email: _l('admin.common.email_valid'),
                },
                international_phone_number: {
                    required: _l('admin.common.phone_number_required'),
                    pattern: _l('admin.manage.valid_phone_number'),
                },
                address: {
                    required: _l('admin.manage.address_required'),
                },
                country: {
                    required: _l('admin.manage.country_required'),
                },
                state: {
                    required: _l('admin.manage.state_required'),
                },
                city: {
                    required: _l('admin.manage.city_required'),
                },
                pincode: {
                    required: _l('admin.manage.pincode_required'),
                },
            },
            errorPlacement: (error, element) => {
                $(`#${element.attr("id")}_error`).text(error.text());
            },
            highlight: (element) => {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(`#${element.id}_error`).text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: (form) => submitLocationForm(form, iti),
        });

        // Custom validation methods
        $.validator.addMethod("filesize", function(value, element, param) {
            return element.files.length === 0 || element.files[0].size <= param * 2048;
        }, 'File size should be less than {0} bytes');

        $.validator.addMethod("imageDimension", function(value, element) {
            if (element.files.length === 0) return true;

            const file = element.files[0];
            const img = new Image();
            let valid = false;

            const reader = new FileReader();
            reader.onload = (e) => img.src = e.target.result;
            
            img.onload = function() {
                valid = img.width === 180 && img.height === 180;
                $(element).data("valid-dimension", valid);
                $(element).valid();
            };

            reader.readAsDataURL(file);
            return $(element).data("valid-dimension") !== false;
        }, "Image dimensions must be exactly 180x180 pixels.");

        $.validator.addMethod(
            "endTimeGreaterThanStartTime",
            function(value, element, params) {
                const startTime = $(`#${params[0]}`).val();
                if (!startTime || !value) return true;

                const timeToMinutes = time => {
                    const parts = time.split(":");
                    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                };

                return timeToMinutes(value) > timeToMinutes(startTime);
            },
            "End time must be greater than start time."
        );
    };
    
    // Submit location form
    const submitLocationForm = (form, iti) => {
        const $submitBtn = $addLocationModal.find(".submitbtn");
        const formData = new FormData(form);
        
        formData.set('international_phone_number', iti.getNumber());
        
        $submitBtn.html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}...`)
                 .prop("disabled", true);
        
        $.ajax({
            type: "POST",
            url: "/admin/store_location",
            data: formData,
            processData: false,
            contentType: false,
            success: (resp) => {
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $addLocationModal.modal("hide");
                    initTable();
                }
                resetSubmitButton($submitBtn);
            },
            error: (error) => {
                handleFormError(error);
                resetSubmitButton($submitBtn);
            },
        });
    };
    
    const resetSubmitButton = ($btn) => {
        const isEdit = $addLocationModal.find("#id").val() !== "";
        $btn.text(isEdit ? _l('admin.common.save_changes') : _l('admin.common.create_new'))
            .prop("disabled", false);
    };
    
    const handleFormError = (error) => {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        
        if (error.responseJSON.code === 422) {
            $.each(error.responseJSON.errors, (key, val) => {
                $(`#${key}`).addClass("is-invalid");
                $(`#${key}_error`).text(val[0]);
            });
        } else {
            showToast("error", error.responseJSON.message);
        }
    };
    
    // Initialize data table
    const initTable = (search = "", status = "") => {
        $(".table-loader").show();
        $(".input-loader").show();
        $(".real-table, .real-data").addClass("d-none");
        
        $.ajax({
            url: "/admin/get_locations",
            type: "GET",
            data: { search, status },
            success: (response) => {
                renderTable(response);
            },
            error: (error) => {
                showToast("error", error.responseJSON.message);
            },
            complete: () => {
                $(".table-loader").hide();
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-table, .real-data").removeClass("d-none");
            },
        });
    };
    
    // Render data table
    const renderTable = (response) => {
        let tableBody = "";
        
        if ($.fn.DataTable.isDataTable($locationTable)) {
            $locationTable.DataTable().destroy();
        }
        
        if (response.code === 200 && response.data.length > 0) {
            tableBody = generateTableRows(response.data);
        } else {
            tableBody = `<tr><td colspan="7" class="text-center">${_l('admin.common.empty_table')}</td></tr>`;
            $(".table-footer").empty();
        }
        
        $locationTable.find("tbody").html(tableBody);
        
        if (response.data.length > 0) {
            initDataTable();
        }
    };
    
    // Generate table rows
    const generateTableRows = (data) => {
        return data.map(location => {
            const workingDaysSet = new Set(
                location.working_days?.map(day => day.day.toLowerCase()) || []
            );
            
            const workingDaysHtml = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]
                .map(day => {
                    const className = workingDaysSet.has(day) ? "working" : "non-working";
                    return `<span class="${className}">${day.charAt(0).toUpperCase()}</span>`;
                }).join("");
            
            const statusClass = location.status == 1 ? "badge-success-transparent" : "badge-danger-transparent";
            const statusText = location.status == 1 ? _l('admin.common.active') : _l('admin.common.inactive');
            
            const actionButtons = [
                hasPermission(permissions, 'locations', 'edit') ? 
                `<li><button type="button" class="dropdown-item rounded-1 edit-location-btn" data-id="${location.id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</button></li>` : '',
                hasPermission(permissions, 'locations', 'delete') ? 
                `<li><button type="button" class="dropdown-item rounded-1 delete-location-btn" data-id="${location.id}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</button></li>` : ''
            ].filter(Boolean).join("");
            
            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center file-name-icon">
                            <a href="#" class="avatar avatar-lg border">
                                <img src="${location.image_url}" class="img-fluid" alt="brands">
                            </a>
                            <div class="ms-2">
                                <h6 class="fw-medium"><a href="#">${location.name}</a></h6>
                            </div>
                        </div>
                    </td>
                    <td><h6 class="fw-medium"><a href="#">${location.address}</a></h6></td>
                    <td><h6 class="fw-medium"><a href="#">${location.phone}</a></h6></td>
                    <td><div class="working-days">${workingDaysHtml}</div></td>
                    <td>
                        <span class="badge ${statusClass} d-inline-flex align-items-center badge-sm">
                            <i class="ti ti-point-filled me-1"></i>${statusText}
                        </span>
                    </td>
                    ${actionButtons ? `<td><div class="dropdown">
                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2">${actionButtons}</ul>
                    </div></td>` : ''}
                </tr>`;
        }).join("");
    };
    
    // Initialize DataTable
    const initDataTable = () => {
        $locationTable.DataTable({
            ordering: false,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            drawCallback: function() {
                $(".dataTables_info").addClass("d-none");
                $(".dataTables_wrapper .dataTables_paginate").addClass("d-none");
                
                const tableWrapper = $(this).closest(".dataTables_wrapper");
                $(".table-footer").empty().append(
                    $('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                        .append($('<div class="datatable-info"></div>').append(tableWrapper.find(".dataTables_info").clone(true)))
                        .append($('<div class="datatable-pagination"></div>').append(tableWrapper.find(".dataTables_paginate").clone(true)))
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
    };
    
    // Fetch countries, states, cities
    const fetchCountries = () => {
        $.ajax({
            type: "GET",
            url: "/api/countries",
            headers: { accept: "application/json" },
            success: (response) => {
                if (response.code === 200) {
                    populateDropdown("#country", response.data, _l('admin.common.select_country'));
                }
            },
        });
    };
    
    const fetchStatesByCountry = (countryId) => {
        $.ajax({
            type: "POST",
            url: "/api/states",
            data: { country_id: countryId },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                if (response.code === 200) {
                    populateDropdown("#state", response.data, _l('admin.common.select_state'));
                    $("#city").html(`<option value="">${_l('admin.common.select_city')}</option>`);
                }
            },
        });
    };
    
    const fetchCitiesByState = (stateId) => {
        $.ajax({
            type: "POST",
            url: "/api/cities",
            data: { state_id: stateId },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                if (response.code === 200) {
                    populateDropdown("#city", response.data, _l('admin.common.select_city'));
                }
            },
        });
    };
    
    const populateDropdown = (selector, data, placeholder, selectedId = null) => {
        const $dropdown = $(selector);
        $dropdown.empty().append(`<option value="">${placeholder}</option>`);
        
        data.forEach(item => {
            const selected = selectedId && item.id == selectedId ? "selected" : "";
            $dropdown.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
        });
    };
    
    // Edit location handler
    const handleEditLocation = (id) => {
        resetForm();
        
        $.ajax({
            type: "GET",
            url: "/admin/get_location/" + id,
            success: (response) => {
                if (response.code === 200) {
                    const data = response.data;
                    populateForm(data);
                    
                    $addLocationModal.find(".modal-title").text(_l('admin.manage.edit_location'));
                    $addLocationModal.find(".submitbtn").text(_l('admin.common.save_changes'));
                    $("#status_div").removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                    $addLocationModal.modal("show");
                }
            },
        });
    };
    
    const resetForm = () => {
        $userProfileForm[0].reset();
        $userProfileForm.find("#id").val("");
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    };
    
    const populateForm = (data) => {
        $("#id").val(data.id);
        $("#name").val(data.name);
        $("#language_id").val(data.language_id);
        $("#status").prop("checked", data.status === 1);
        $("#email").val(data.email);
        $("#address").val(data.address);
        $("#pincode").val(data.pincode);
        
        // Phone number
        if (data.phone && data.phone.length > 0) {
            const cleanPhone = data.phone.replace(/[^+\d]/g, '');
            iti.setNumber(cleanPhone);
            $("#international_phone_number").val(cleanPhone);
        }
        
        // Image
        if (data.image) {
            $("#image_preview").attr("src", data.image).removeClass('d-none');
            $(".image_placeholder").hide();
        }
        
        // Location dropdowns
        fetchCountryAjax(data.country)
            .then(() => fetchStateAjax(data.country, data.state))
            .then(() => fetchCityAjax(data.state, data.city));
        
        // Working days
        if (data.working_days?.length > 0) {
            data.working_days.forEach(day => {
                $(`#${day.day}`).prop("checked", true);
                $(`#${day.day}_start`).val(day.start_time);
                $(`#${day.day}_end`).val(day.end_time);
            });
        }
    };
    
    // Helper functions for AJAX calls with Promises
    const fetchCountryAjax = (id) => {
        return fetchDropdownData("/api/countries", "GET", null, "#country", id, _l('admin.common.select_country'));
    };
    
    const fetchStateAjax = (countryId, id) => {
        return fetchDropdownData("/api/states", "POST", { country_id: countryId }, "#state", id, _l('admin.common.select_state'));
    };
    
    const fetchCityAjax = (stateId, id) => {
        return fetchDropdownData("/api/cities", "POST", { state_id: stateId }, "#city", id, _l('admin.common.select_city'));
    };
    
    const fetchDropdownData = (url, method, data, selector, selectedId, placeholder) => {
        return new Promise((resolve) => {
            $.ajax({
                type: method,
                url: url,
                data: data,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    accept: "application/json",
                },
                success: (response) => {
                    if (response.code === 200) {
                        populateDropdown(selector, response.data, placeholder, selectedId);
                    }
                    resolve();
                },
                error: () => resolve(),
            });
        });
    };
    
    // Delete location handler
    const handleDeleteLocation = () => {
        $("#deleteLocation").on("submit", function(e) {
            e.preventDefault();
            const $submitBtn = $(this).find(".submitbtn");
            
            $submitBtn.html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..`)
                     .prop("disabled", true);
            
            $.ajax({
                type: "POST",
                url: "/admin/delete_location",
                data: $(this).serialize(),
                success: (response) => {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $deleteModal.modal("hide");
                        initTable();
                    } else {
                        showToast("error", response.message);
                    }
                    resetDeleteButton($submitBtn);
                },
                error: (error) => {
                    showToast("error", error.responseJSON.message);
                    $deleteModal.modal("hide");
                    resetDeleteButton($submitBtn);
                },
            });
        });
    };
    
    const resetDeleteButton = ($btn) => {
        $btn.text(_l('admin.common.yes_delete')).prop("disabled", false);
    };
    
    // Working days time validation
    const setupWorkingDaysValidation = () => {
        $(".working-day-checkbox").on("change", function() {
            const day = $(this).attr("id");
            const $start = $(`#${day}_start`);
            const $end = $(`#${day}_end`);
            
            $start.prop("required", $(this).prop("checked"));
            $end.prop("required", $(this).prop("checked"));
        });
    };
    
    // Event handlers
    const setupEventHandlers = () => {
        // Search
        $("#search").on("input", function() {
            initTable($(this).val().trim(), currentStatus);
        });
        
        // Status filter
        $(".dropdown-item").on("click", function() {
            $(".dropdown-item").removeClass("active");
            $(this).addClass("active");
            
            const statusText = $(this).text().trim();
            $(".statuslabel").text(statusText);
            
            currentStatus = statusText.toLowerCase() === "active" ? 1 : 0;
            initTable($("#search").val().trim(), currentStatus);
        });
        
        // Add new location
        $(document).on("click", "#add_new_location", function() {
            $addLocationModal.find(".modal-title").text(_l('admin.manage.add_location'));
            $addLocationModal.find(".submitbtn").text(_l('admin.common.create_new'));
            $("#status_div").addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
            resetForm();
        });
        
        // Edit location
        $(document).on('click', '.edit-location-btn', function() {
            handleEditLocation($(this).data('id'));
        });
        
        // Delete location
        $(document).on('click', '.delete-location-btn', function() {
            $("#delete_id").val($(this).data('id'));
        });
        
        // Country/state change
        $("#country").on("change", function() {
            const id = $(this).val();
            id ? fetchStatesByCountry(id) : resetLocationDropdowns();
        });
        
        $("#state").on("change", function() {
            const id = $(this).val();
            id && fetchCitiesByState(id);
        });
        
        // Numeric input
        $(".Number").on("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    };
    
    const resetLocationDropdowns = () => {
        $("#state").html(`<option value="">${_l('admin.common.select_state')}</option>`);
        $("#city").html(`<option value="">${_l('admin.common.select_city')}</option>`);
    };

})();

