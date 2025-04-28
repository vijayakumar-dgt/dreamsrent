(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, manage');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        fetchCountries();
        $("#country, #state, #city").select2({
            dropdownParent: $("#add_location"),
            placeholder: function () {
                return $(this).attr("id") === "country"
                    ? `${_l('admin.manage.select_country')}`
                    : $(this).attr("id") === "state"
                    ? `${_l('admin.manage.select_state')}`
                    : `${_l('admin.manage.select_city')}`;
            },
        });

        $(document).on("change", "#image", function () {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $("#image_preview").attr("src", e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
                $("#image_preview").removeClass('d-none');
                $(".image_placeholder").hide();
            } else {
                $("#image_preview").addClass('d-none');
                $(".image_placeholder").show();
            } 
        });

        $.extend($.validator.messages, {
            required: _l('admin.common.this_field_is_required'),
        });

        $("#locationForm").validate({
            rules: {
                image: {
                    required: false,
                    filesize: 2048,
                    imageDimension:[180,180],
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
                phone: {
                    required: true,
                    pattern: /^\+?[1-9][0-9]{7,14}$/,
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
                    required: true,
                    pattern: /^[0-9a-zA-Z]+$/,
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
                    required:_l('admin.common.email_required'),
                    email: _l('admin.common.email_valid'),
                },
                phone: {
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
                
                let locationFormData = new FormData(form);
                $("#add_location .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}...`);
                $("#add_location .submitbtn").prop("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "/admin/store_location",
                    data: locationFormData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_location").modal("hide");
                            initTable();
                        }
                        $("#add_location .submitbtn").text(_l('admin.common.create_new'));
                        $("#add_location .submitbtn").prop("disabled", false);
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
                            showToast("error", error.responseJSON.message);
                        }
                        $("#add_location .submitbtn").text(_l('admin.common.create_new'));
                        $("#add_location .submitbtn").prop("disabled", false);
                    },
                });
            },
        }); 
    

    function initTable(search = "", status = "") {
        $(".table-loader").show();
        $(".input-loader").show();
        $(".real-table, .real-data").addClass("d-none");
        $.ajax({
            url: "/admin/get_locations",
            type: "GET",
            data: { search: search, status: status },
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#locationTable")) {
                    $("#locationTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;
    
                    $.each(data, function (index, value) {
                        let workingDaysSet = new Set();
    
                        if (value.working_days && value.working_days.length > 0) {
                            workingDaysSet = new Set(value.working_days.map((_day) => _day.day.toLowerCase())); // Normalize to lowercase
                        }
                        
                        const daysOfWeek = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
                        
                        let workingDaysHtml = daysOfWeek.map(day => {
                            const className = workingDaysSet.has(day) ? "working" : "non-working";
                            return `<span class="${className}">${day.charAt(0).toUpperCase()}</span>`;
                        }).join("");
    
                        tableBody += `<tr>
                                <td>
                                    <div class="d-flex align-items-center file-name-icon">
                                        <a href="#" class="avatar avatar-lg border">
                                            <img src="${
                                                value.image_url
                                            }" class="img-fluid" alt="brands">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fw-medium"><a href="#">${
                                                value.name
                                            }</a></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><h6 class="fw-medium"><a href="#">${
                                    value.address
                                }</a></h6></td>
                                <td><h6 class="fw-medium"><a href="#">${
                                    value.phone
                                }</a></h6></td>
                                <td>
                                <div class="working-days">${workingDaysHtml}</div>
                                </td>
                                <td><span class="badge ${
                                    value.status == 1
                                        ? `badge-success-transparent`
                                        : `badge-danger-transparent`
                                }  d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>${
                                                value.status == 1
                                                    ? `${_l('admin.common.active')}`
                                                    : `${_l('admin.common.inactive')}`
                                            }
                                    </span>
                                </td>
                                ${ hasPermission(permissions, 'locations', 'edit') || hasPermission(permissions, 'locations', 'delete') ? 
    
                                `<td>
                                   <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${ hasPermission(permissions, 'locations', 'edit') ? 
                                            `<li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(${
                                                    value.id
                                                });" id="edit-location-btn" data-id="${value.id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</a>
                                            </li>`:''}
                                            ${ hasPermission(permissions, 'locations', 'delete') ? 
                                            `<li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(${
                                                    value.id
                                                });" id="delete-location-btn" data-id="${value.id}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</a>
                                            </li>`:''}
                                        </ul>
                                    </div>
                                </td>`:''}
                            </tr>`;
                    });
                } else {
                    tableBody += `
                            <tr>
                                <td colspan="7" class="text-center">${_l('admin.common.empty_table')}</td>
                            </tr>`;
                    $(".table-footer").empty();
                }
                $("#locationTable tbody").html(tableBody);
                if (response.data.length > 0) {
                    $("#locationTable").DataTable({
                        ordering: false,
                        searching: false, 
                        pageLength: 10, 
                        lengthChange: false,
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(".dataTables_wrapper .dataTables_paginate").addClass(
                                "d-none"
                            );
                            var tableWrapper = $(this).closest(
                                ".dataTables_wrapper"
                            );
                            var info = tableWrapper.find(".dataTables_info");
                            var pagination = tableWrapper.find(
                                ".dataTables_paginate"
                            );
    
                            $(".table-footer")
                                .empty()
                                .append(
                                    $(
                                        '<div class="d-flex justify-content-between align-items-center w-100"></div>'
                                    )
                                        .append(
                                            $(
                                                '<div class="datatable-info"></div>'
                                            ).append(info.clone(true))
                                        )
                                        .append(
                                            $(
                                                '<div class="datatable-pagination"></div>'
                                            ).append(pagination.clone(true))
                                        )
                                );
                            $(".table-footer")
                                .find(".dataTables_paginate")
                                .removeClass("d-none");
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
            error: function (error) {
                showToast("error", error.responseJSON.message);
            },
            complete: function () {
                $(".table-loader").hide();
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-table, .real-data").removeClass("d-none");
            },
        });
    }

    $("#pincode").on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-6]/g, "")
        );
    });
    
    
    
    let currentStatus = "";  
    let currentNameType = "";
    
    $("#search").on("input", function () {
        let searchQuery = $(this).val().trim();
        initTable(searchQuery, currentStatus);
    });
    
     
     $(".dropdown-item").on("click", function () {
        let statuslabel = _l('admin.common.status');
        $(".dropdown-item").removeClass("active");
        $(this).addClass("active"); 
        statuslabel = $(this).text().trim();
        $(".statuslabel").text(statuslabel);
        let selectedStatus = $(this).text().trim().toLowerCase() === "active" ? 1 : 0; 
    
        currentStatus = selectedStatus; 
    
        initTable($("#search").val().trim(), currentStatus);
    
    });
    
    
    $(document).on("click", "#add_new_location", function () {
        $("#add_location .modal-title").text(_l('admin.manage.add_location'));
        $("#add_location .submitbtn").text(_l('admin.common.create_new'));
        $("#status_div").addClass('d-none').parent().removeClass('justify-content-between').addClass('justify-content-end');
        $("#locationForm")[0].reset();
        $("#locationForm #id").val("");
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    });
    
    $(document).on('click','#edit-location-btn', function(){
        $("#locationForm")[0].reset();
        $("#locationForm #id").val("");
        let id = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "/admin/get_location/" + id,
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#add_location #name").val(data.name);
                    $("#add_location #id").val(data.id);
                    $("#add_location #language_id").val(data.language_id);
                    if (data.status === 1) {
                        $("#add_location #status").prop("checked", true);
                    } else {
                        $("#add_location #status").prop("checked", false);
                    }
                    if(data.image){
                        $("#image_preview").attr("src", data.image);
                        $("#image_preview").removeClass('d-none');
                        $(".image_placeholder").hide();
                    }else{
                        $("#image_preview").addClass('d-none');
                        $(".image_placeholder").show();
                    }
                    $("#email").val(data.email);
                    $("#phone").val(data.phone);
                    $("#address").val(data.address);
                    $("#pincode").val(data.pincode);
                    fetchCountryAjax(data.country)
                        .then(() => fetchStateAjax(data.country, data.state))
                        .then(() => fetchCityAjax(data.state, data.city))
                        .then(() => {
                            if (data.working_days && data.working_days.length > 0) {
                                $.each(data.working_days, function (index, value) {
                                    $("#" + value.day).prop("checked", true);
                                    $("#" + value.day + "_start").val(
                                        value.start_time
                                    );
                                    $("#" + value.day + "_end").val(value.end_time);
                                });
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
    
                    $("#add_location .modal-title").text(_l('admin.manage.edit_location'));
                    $("#add_location .submitbtn").text(_l('admin.common.save_changes'));
                    $("#status_div").removeClass('d-none').parent().removeClass('justify-content-end').addClass('justify-content-between');
                    $("#add_location").modal("show");
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
                }
            },
        });
    });
   
    function fetchCountryAjax(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                url: "/api/countries",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#country").empty();
                        $("#country").append(
                            `<option value="">${_l('admin.common.select_country')}</option>`
                        );
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#country").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#country").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    console.log(error);
                    reject({
                        message: _l('admin.common.something_went_wrong'),
                    });
                },
            });
        });
    }
    
    function fetchStateAjax(country_id, id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "/api/states",
                data: { country_id: country_id },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    accept: "application/json",
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#state").empty();
                        $("#state").append(
                            `<option value="">${_l('admin.common.select_state')}</option>`
                        );
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#state").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#state").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    console.log(error);
                    reject({
                        message: _l('admin.common.something_went_wrong'),
                    });
                },
            });
        });
    }
    
    function fetchCityAjax(state_id, id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "/api/cities",
                data: { state_id: state_id },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    accept: "application/json",
                },
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#city").empty();
                        $("#city").append(`<option value="">${_l('admin.common.select_city')}</option>`);
                        $.each(data, function (key, value) {
                            if (value.id === id) {
                                $("#city").append(
                                    '<option value="' +
                                        value.id +
                                        '" selected>' +
                                        value.name +
                                        "</option>"
                                );
                            } else {
                                $("#city").append(
                                    '<option value="' +
                                        value.id +
                                        '">' +
                                        value.name +
                                        "</option>"
                                );
                            }
                        });
                        resolve();
                    }
                },
                error: function (error) {
                    console.log(error);
                    reject({
                        message: _l('admin.common.something_went_wrong'),
                    });
                },
            });
        });
    }   
    $(document).on('click','#delete-location-btn', function(){
        let id = $(this).data('id');
        $("#delete_id").val(id);
    });
    
    $("#deleteLocation").on("submit", function (e) {
        e.preventDefault();
        $("#deleteLocation .submitbtn").html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..`);
        $("#deleteLocation .submitbtn").prop("disabled", true);
        $.ajax({
            type: "POST",
            url: "/admin/delete_location",
            data: $("#deleteLocation").serialize(),
            success: function (response) {
                if (response.code === 200) {
                    showToast("success", response.message);
                    $("#delete-modal").modal("hide");
                    initTable();
                } else {
                    showToast("error", response.message);
                    $("#delete-modal").modal("hide");
                }
                $("#deleteLocation .submitbtn").text(_l('admin.common.yes_delete'));
                $("#deleteLocation .submitbtn").prop("disabled", false);
            },
            error: function (error) {
                showToast("error", error.responseJSON.message);
                $("#delete-modal").modal("hide");
                $("#deleteLocation .submitbtn").text(_l('admin.common.yes_delete'));
                $("#deleteLocation .submitbtn").prop("disabled", false);
            },
        });
    });
    
    $(".working-day-checkbox").on("change", function () {
        var day = $(this).attr("id");
        toggleTimeRequired(day);
    });
    
    function toggleTimeRequired(day) {
        var checkbox = $("#" + day);
        var startInput = $("#" + day + "_start");
        var endInput = $("#" + day + "_end");
    
        if (checkbox.prop("checked")) {
            startInput.prop("required", true);
            endInput.prop("required", true);
        } else {
            startInput.prop("required", false);
            endInput.prop("required", false);
        }
    }
    
    $("#country").on("change", function () {
        let id = $(this).val();
        if (id) {
            fetchStatesByCountry(id);
        } else {
            $("#state").empty();
            $("#state").append(`<option value="">${_l('admin.common.select_state')}</option>`);
            $("#city").empty();
            $("#city").append(`<option value="">${_l('admin.common.select_city')}</option>`);
        }
    });
    
    $("#state").on("change", function () {
        let id = $(this).val();
        if (id) {
            fetchCitiesByState(id);
        } else {
            $("#city").empty();
            $("#city").append(`<option value="">${_l('admin.common.select_city')}</option>`);
        }
    });
    function fetchCountries() {
        $.ajax({
            type: "GET",
            url: "/api/countries",
            headers: {
                accept: "application/json",
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#country").empty();
                    $("#country").append(
                        `<option value="">${_l('admin.common.select_country')}</option>`
                    );
                    $.each(data, function (key, value) {
                        $("#country").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                }
            },
        });
    }
    
    function fetchStatesByCountry(country_id) {
        $.ajax({
            type: "POST",
            url: "/api/states",
            data: { country_id: country_id },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#state").empty();
                    $("#state").append(`<option value="">${_l('admin.common.select_state')}</option>`);
                    $.each(data, function (key, value) {
                        $("#state").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                    $("#city").empty();
                    $("#city").append(`<option value="">${_l('admin.common.select_city')}</option>`);
                }
            },
        });
    }
    
    function fetchCitiesByState(state_id) {
        $.ajax({
            type: "POST",
            url: "/api/cities",
            data: { state_id: state_id },
            headers: {
                accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#city").empty();
                    $("#city").append(`<option value="">${_l('admin.common.select_city')}</option>`);
                    $.each(data, function (key, value) {
                        $("#city").append(
                            '<option value="' +
                                value.id +
                                '">' +
                                value.name +
                                "</option>"
                        );
                    });
                }
            },
        });
    }
    
    
        $(".Number").on("input", function () {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    });
})();
$.validator.addMethod("filesize", function (value, element, param) {
    if (element.files.length === 0) return true;
    return element.files[0].size <= param * 2048;

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
        valid = img.width === 180 && img.height === 180;
        $(element).data("valid-dimension", valid);
        $(element).valid();
    };

    reader.readAsDataURL(file);

    return $(element).data("valid-dimension") !== false;
}, "Image dimensions must be exactly 180x180 pixels.");


$.validator.addMethod(
    "endTimeGreaterThanStartTime",
    function (value, element, params) {
        var startTimeId = params[0]; 
        var startTime = $("#" + startTimeId).val();

        if (!startTime || !value) {
            return true; 
        }

        function timeToMinutes(time) {
            var parts = time.split(":");
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10); 
        }

        var startMinutes = timeToMinutes(startTime);
        var endMinutes = timeToMinutes(value);

        return endMinutes > startMinutes; 
    },
    "End time must be greater than start time."
);

