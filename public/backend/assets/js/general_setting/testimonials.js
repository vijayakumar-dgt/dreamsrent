(async () => {
    "use strict";

    await loadTranslationFile("admin", "cms,common");
    const permissions = await loadUserPermissions();

    /**
     * Initialize DOM Events
     */
    $(document).ready(function () {
        bindFormValidations();
        bindImagePreviewHandlers();
        bindSortAndSearchHandlers();
        bindDeleteHandler();
        bindEditHandler();
        $(".dropdown-toggle .sort").text("Sort By : Latest");
        applyFilters();
    });

    /**
     * Edit testimonial - fills the modal with the testimonial's data
     */
    function editTestimonial(id, customerName, image, review, ratings, status) {
        $("#edit_testimonial_id").val(id);
        $("#edit_testimonial_name").val(customerName);
        $("#edit_testimonial_review").val(review);
        $("#edit_testimonial_status").prop("checked", status === 1);
        $("#edit_testimonial_preview").attr("src", image);
        $("#edit_testimonial_ratings").val(ratings).trigger("change");
        $("#edit_testimonial").modal("show");
    }

    /**
     * File input preview and validation
     */
    function bindImagePreviewHandlers() {
        const previewImage = (inputSelector, previewSelector) => {
            $(inputSelector).on("change", async (event) => {
                await handleFileChange(event, inputSelector, previewSelector);
            });
        };

        const handleFileChange = async (event, inputSelector, previewSelector) => {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                showToast("error", _l("admin.common.image_size"));
                $(inputSelector).val("");
                return;
            }

            try {
                const dataUrl = await readFileAsync(file);
                const isValid = await validateImageDimensionsAsync(dataUrl, 180, 180);

                if (isValid) {
                    $(previewSelector).attr("src", dataUrl).show();
                } else {
                    showToast("error", _l("admin.cms.testimonial_image_size"));
                    $(inputSelector).val("");
                }
            } catch (err) {
                console.error(err);
                showToast("error", _l("admin.common.image_error"));
                $(inputSelector).val("");
            }
        };

        const readFileAsync = (file) => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = (e) => reject(e);
                reader.readAsDataURL(file);
            });
        };

        const validateImageDimensionsAsync = (dataUrl, width, height) => {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(img.width === width && img.height === height);
                img.onerror = () => resolve(false);
                img.src = dataUrl;
            });
        };

        previewImage("#testimonial_image", "#testimonial_image_preview");
        previewImage("#edit_testimonial_image", "#edit_testimonial_preview");
    }

    /**
     * Validation rules for add and edit testimonial forms
     */
    function bindFormValidations() {
        $.validator.addMethod(
            "filesize",
            function (value, element, param) {
                return (
                    this.optional(element) ||
                    (element.files[0] && element.files[0].size <= param)
                );
            },
            _l("admin.common.image_size")
        );

        const commonRules = {
            customer_name: { required: true, minlength: 3 },
            customer_rating: { required: true },
            customer_review: { required: true, minlength: 10 },
        };

        const commonMessages = {
            customer_name: {
                required: _l("admin.common.customer_required"),
                minlength: _l("admin.cms.customer_minlength"),
            },
            customer_rating: { required: _l("admin.cms.rating_required") },
            customer_review: {
                required: _l("admin.cms.review_required"),
                minlength: _l("admin.cms.review_minlength"),
            },
        };

        // Add Testimonial
        $("#addTestimonials").validate({
            rules: {
                testimonial_image: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2 * 1024 * 1024,
                },
                ...commonRules,
            },
            messages: {
                testimonial_image: {
                    required: _l("admin.common.image_required"),
                    extension: _l("admin.common.image_format"),
                    filesize: _l("admin.common.image_size"),
                },
                ...commonMessages,
            },
            errorPlacement: (error, element) =>
                $(`#${element.attr("id")}_error`).text(error.text()),
            highlight: (element) =>
                $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(`#${element.id}_error`).text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: (form) =>
                submitTestimonial(form, "/admin/testimonials/store", "add"),
        });

        // Edit Testimonial
        $("#editTestimonialForm").validate({
            rules: {
                edit_testimonial_image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2 * 1024 * 1024,
                },
                edit_testimonial_name: { required: true, minlength: 3 },
                edit_testimonial_ratings: { required: true },
                edit_testimonial_review: { required: true, minlength: 10 },
            },
            messages: {
                edit_testimonial_image: {
                    extension: _l("admin.common.image_format"),
                    filesize: _l("admin.common.image_size"),
                },
                edit_testimonial_name: {
                    required: _l("admin.common.customer_required"),
                    minlength: _l("admin.cms.customer_minlength"),
                },
                edit_testimonial_ratings: {
                    required: _l("admin.cms.rating_required"),
                },
                edit_testimonial_review: {
                    required: _l("admin.cms.review_required"),
                    minlength: _l("admin.cms.review_minlength"),
                },
            },
            errorPlacement: (error, element) =>
                $(`#${element.attr("id")}_error`).text(error.text()),
            highlight: (element) =>
                $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(`#${element.id}_error`).text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: (form) =>
                submitTestimonial(form, "/admin/testimonials/update", "edit"),
        });

        // Delete Testimonial
        $("#deleteTestimonialForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/testimonials/delete",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $("#delete_testimonial").modal("hide");
                        loadTestimonialsSettings();
                    }
                },
                error: function (res) {
                    showToast(
                        "error",
                        res.responseJSON?.message ||
                            _l("admin.common.default_delete_error")
                    );
                },
            });
        });
    }

    /**
     * Submit form for add/edit testimonial
     */
    function submitTestimonial(form, url, type) {
        let formData = new FormData(form);

        if (type === "edit") {
            formData.append("id", $("#edit_testimonial_id").val());
            formData.append("customer_name", $("#edit_testimonial_name").val());
            formData.append(
                "customer_rating",
                $("#edit_testimonial_ratings").val()
            );
            formData.append(
                "customer_review",
                $("#edit_testimonial_review").val()
            );
            formData.append(
                "status",
                $("#edit_testimonial_status").prop("checked") ? 1 : 0
            );

            let imageFile = $("#edit_testimonial_image")[0].files[0];
            if (imageFile) formData.append("testimonial_image", imageFile);
        }

        $.ajax({
            type: "POST",
            url,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                const btn = type === "edit" ? ".savebtn" : ".submitbtn";
                $(btn).attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                        "admin.common.saving"
                    )}..
                `);
            },
            complete: function () {
                const btn = type === "edit" ? ".savebtn" : ".submitbtn";
                $(btn)
                    .attr("disabled", false)
                    .html(
                        type === "edit"
                            ? _l("admin.common.save_changes")
                            : _l("admin.common.create_new")
                    );
            },
            success: function (response) {
                if (response.code === 200) {
                    loadTestimonialsSettings();
                    $(form)[0].reset();
                    showToast("success", response.message);
                    $(form)
                        .find(".form-control")
                        .removeClass("is-valid is-invalid");
                    $(
                        type === "edit"
                            ? "#edit_testimonial"
                            : "#add_testimonial"
                    ).modal("hide");
                }
            },
            error: function (error) {
                $(form).find(".error-text").text("");
                $(form)
                    .find(".form-control")
                    .removeClass("is-invalid is-valid");

                if (error.responseJSON?.code === 422) {
                    $.each(error.responseJSON.errors, function (key, val) {
                        $(`#${key}`).addClass("is-invalid");
                        $(`#${key}_error`).text(val[0]);
                    });
                } else {
                    showToast("error", error.responseJSON.message);
                }
            },
        });
    }

    /**
     * Bind search, sort, and filter button actions
     */
    function bindSortAndSearchHandlers() {
        $(document).on("click", ".sort-option", function () {
            const selectedSort = $(this).text().trim();
            $(".dropdown-toggle .sort").text(`Sort By : ${selectedSort}`);
            applyFilters();
        });

        $(".filterbox .text-purple").on("click", applyFilters);
        $(".filterbox .text-danger").on("click", function () {
            $('.filterbox input[type="checkbox"]').prop("checked", false);
            applyFilters();
        });

        $("#search").on("keyup", function () {
            clearTimeout(window.searchTimer);
            window.searchTimer = setTimeout(applyFilters, 500);
        });
    }

    /**
     * Apply filters and refresh testimonial listing
     */
    function applyFilters() {
        const sortText = $(".dropdown-toggle .sort")
            .text()
            .replace("Sort By : ", "")
            .trim();
        const searchText = $("#search").val().trim();
        const selectedRatings = [];

        $('.filterbox input[type="checkbox"]:checked').each(function () {
            selectedRatings.push($(this).parent().text().trim()[0]);
        });

        const filters = {
            sort: sortText,
            search: searchText,
            ratings: selectedRatings,
        };

        loadTestimonialsSettings(filters);
    }

    /**
     * Dummy AJAX load function (replaced in real setup)
     */
    function loadTestimonialsSettings(filters = {}) {
        $.ajax({
            url: "/admin/testimonials/list",
            type: "GET",
            data: filters,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass(
                    "d-none"
                );
                if ($("#testimonialsTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function (response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#testimonialsTable")) {
                    $("#testimonialsTable").DataTable().destroy();
                }

                if (response.success && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, testimonial) {
                        let imageUrl = testimonial.image
                            ? "/storage/" + testimonial.image
                            : "/backend/assets/img/default-profile.png";

                        let stars = "";
                        for (let i = 1; i <= 5; i++) {
                            stars += i <= testimonial.ratings
                                ? `<span class="me-1"><i class="ti ti-star-filled text-warning"></i></span>`
                                : `<span class="me-1"><i class="ti ti-star text-gray-400"></i></span>`;
                        }

                        let reviewText = testimonial.review.length > 50
                            ? testimonial.review.substring(0, 50) + "..."
                            : testimonial.review;

                        // Extract action buttons into a separate variable
                        let actionButtons = "";
                        const canEdit = hasPermission(permissions, "testimonials", "edit");
                        const canDelete = hasPermission(permissions, "testimonials", "delete");

                        if (canEdit || canDelete) {
                            actionButtons = `
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${canEdit ? `
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item rounded-1 edit-testimonial-btn"
                                                        data-id="${testimonial.id}"
                                                        data-name="${testimonial.customer_name}"
                                                        data-image="${imageUrl}"
                                                        data-review="${testimonial.review}"
                                                        data-ratings="${testimonial.ratings}"
                                                        data-status="${testimonial.status}"
                                                    >
                                                        <i class="ti ti-edit me-1"></i>${_l("admin.common.edit")}
                                                    </button>
                                                </li>` : ""}
                                            ${canDelete ? `
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item rounded-1 delete-testimonial-btn"
                                                        data-id="${testimonial.id}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#delete_testimonial"
                                                    >
                                                        <i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}
                                                    </button>
                                                </li>` : ""}
                                        </ul>
                                    </div>
                                </td>
                            `;
                        }

                        tableBody += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-rounded me-2 flex-shrink-0">
                                            <img src="${imageUrl}" alt="">
                                        </a>
                                        <div>
                                            <a href="javascript:void(0);" class="fw-semibold">${testimonial.customer_name}</a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        ${stars}
                                        <span>(${testimonial.ratings}.0)</span>
                                    </div>
                                </td>
                                <td><a href="javascript:void(0);" title="${testimonial.review}">${reviewText}</a></td>
                                <td>${testimonial.created_date}</td>
                                ${actionButtons}
                            </tr>
                        `;
                    });
                } else {
                    tableBody += `
                    <tr>
                        <td colspan="7" class="text-center">${_l(
                            "admin.common.empty_table"
                        )}</td></td>
                    </tr>`;
                    $(".table-footer").empty();
                }

                $("#testimonialsTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $("#testimonialsTable").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function () {
                            customizeTableFooter($(this));
                        },
                        language: getDataTableLanguage(),
                    });
                }
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    }

    /**
     * Dummy handler placeholder if needed for future use
     */
    function bindDeleteHandler() {
        $(document).on("click", ".delete-testimonial-btn", function () {
            const id = $(this).data("id");
            $("#deleteTestimonialForm #id").val(id);
        });
    }

    function bindEditHandler() {
        $(document).on("click", ".edit-testimonial-btn", function () {
            const id = $(this).data("id");
            const name = $(this).data("name");
            const image = $(this).data("image");
            const review = $(this).data("review");
            const ratings = $(this).data("ratings");
            const status = $(this).data("status");

            editTestimonial(id, name, image, review, ratings, status);
        });
    }
})();
