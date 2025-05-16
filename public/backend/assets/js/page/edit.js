(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, page");
    $(document).ready(function () {
        $("#editPageForm").validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                slug: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                keyword: {
                    required: false,
                },
                description: {
                    required: false,
                    minlength: 10,
                },
                mete_title: {
                    required: false,
                    minlength: 3,
                    maxlength: 255,
                },
                meta_key: {
                    required: false,
                },
                meta_description: {
                    required: false,
                    minlength: 10,
                },
                canonical_url: {
                    required: false,
                    url: true,
                },
                og_title: {
                    required: false,
                    minlength: 3,
                    maxlength: 255,
                },
                og_description: {
                    required: false,
                    minlength: 10,
                },
            },
            messages: {
                title: {
                    required: _l("admin.page.title_required"),
                    minlength: _l("admin.page.title_min"),
                    maxlength: _l("admin.page.title_max"),
                },
                slug: {
                    required: _l("admin.page.slug_required"),
                    minlength: _l("admin.page.slug_min"),
                    maxlength: _l("admin.page.slug_max"),
                },
                keyword: {
                    required: _l("admin.page.keyword_required"),
                },
                description: {
                    required: _l("admin.page.description_required"),
                    minlength: _l("admin.page.description_min"),
                },
                mete_title: {
                    required: _l("admin.page.meta_title_required"),
                    minlength: _l("admin.page.meta_title_min"),
                    maxlength: _l("admin.page.meta_title_max"),
                },
                meta_key: {
                    required: _l("admin.page.meta_keywords_required"),
                },
                meta_description: {
                    required: _l("admin.page.meta_description_required"),
                    minlength: _l("admin.page.meta_description_min"),
                },
                canonical_url: {
                    required: _l("admin.page.canonical_required"),
                    url: _l("admin.page.canonical_url"),
                },
                og_title: {
                    required: _l("admin.page.og_title_required"),
                    minlength: _l("admin.page.og_title_min"),
                    maxlength: _l("admin.page.og_title_max"),
                },
                og_description: {
                    required: _l("admin.page.og_description_required"),
                    minlength: _l("admin.page.og_description_min"),
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
                    $(element)
                        .next(".select2-container")
                        .addClass("is-invalid")
                        .removeClass("is-valid");
                }
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .removeClass("is-invalid")
                        .addClass("is-valid");
                }
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
                let formData = new FormData(form);
                formData.append("status", $("#status").is(":checked") ? 1 : 0);

                formData.set("language_id", $("#language_id").val());

                $("#edit-page")
                    .text(_l("admin.page.please_wait"))
                    .prop("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "/admin/page/update",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast("success", "Page Updated Successfully!");
                            setTimeout(() => {
                                window.location.href =
                                    window.location.origin + "/admin/pages";
                            }, 1500);
                        }
                        $("#edit-page")
                            .text(_l("admin.common.update"))
                            .prop("disabled", false);
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $("#edit-page")
                            .text(_l("admin.common.update"))
                            .prop("disabled", false);

                        if (error.responseJSON.code === 422) {
                            toastr.error(error.responseJSON.message); // Show error using Toastr
                        } else {
                            toastr.error("An unexpected error occurred!");
                        }
                    },
                });
            },
        });
    });

    $(document).ready(function () {
        fetchSection();
        let pageId = $("#page_id").val(); // Get the page_id from the hidden input

        if (pageId) {
            $.ajax({
                url: "/admin/get/page-content",
                type: "POST",
                data: {
                    page_id: pageId,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                success: function (response) {
                    if (response.success) {
                        var data = response.data;
                        if (
                            data.page_content &&
                            data.page_content.trim() !== ""
                        ) {
                            var pageContentArray = JSON.parse(
                                data.page_content
                            );

                            let count = 1;

                            let summernoteId = "";

                            pageContentArray.forEach(function (section) {
                                const uniqueId = Date.now();
                                const textareaTemplate = `
                            <div class="textarea-item border p-3 mb-3 mt-3 bg-light">
                                <div class="d-flex align-items-center justify-content-end mt-1">
                                    <label for="${uniqueId}" class="me-2 fw-bold">${_l(
                                    "admin.common.status"
                                )}</label>
                                    <div class="status-toggle modal-status">
                                        <input type="checkbox" name="page_status[]" id="${uniqueId}" value="1" class="check user8" checked>
                                        <label for="${uniqueId}" class="checktoggle"></label>
                                    </div>
                                    <a class="removeTextarea ms-3">
                                        <i class="ti ti-trash fs-20 fw-bold"></i>
                                    </a>
                                </div>
                
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">${_l(
                                            "admin.page.section_title"
                                        )} <span class="text-danger">*</span></label>
                                        <input type="text" name="section_title[]"  value="${
                                            section.section_title
                                        }" placeholder="${_l(
                                    "admin.page.enter_title"
                                )}" class="form-control">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">${_l(
                                            "admin.page.section_label"
                                        )} <span class="text-danger">*</span></label>
                                        <input type="text" name="section_label[]" value="${
                                            section.section_label
                                        }" placeholder="${_l(
                                    "admin.page.enter_label"
                                )}" class="form-control">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">${_l(
                                            "admin.page.section_des"
                                        )} </label>
                                        <textarea name="page_content[]" placeholder="${_l(
                                            "admin.page.enter_content"
                                        )}" cols="10" rows="3" class="form-control summer" id="summernote_${count}"></textarea>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                            </div>
                        `;

                                $(".textareasContainer").append(
                                    textareaTemplate
                                );
                                initializeSummernote();
                                summernoteId = `#summernote_${count++}`;
                                $(summernoteId).summernote(
                                    "code",
                                    section.section_content
                                );
                            });
                        }
                    }
                    $(".table-loader").hide();
                    $(".label-loader, .input-loader").hide();
                    $(
                        ".real-label, .real-table, .real-data, .real-input"
                    ).removeClass("d-none");
                },
                error: function () {
                    $("#page-content").html("<p>Error fetching content.</p>");
                },
            });
        }
    });

    function initializeSummernote() {
        $(".summer").summernote({
            height: 100,
            width: "100%",
            toolbar: [
                ["style", ["style"]],
                [
                    "font",
                    [
                        "bold",
                        "italic",
                        "underline",
                        "strikethrough",
                        "superscript",
                        "subscript",
                        "clear",
                    ],
                ],
                ["fontname", ["fontname"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["height", ["height"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
            callbacks: {
                onDrop: function (event) {
                    event.preventDefault();
                    var data =
                        event.originalEvent.dataTransfer.getData("text/plain");
                    if (data) {
                        $(this).summernote("pasteHTML", data);
                    }
                },
            },
        });
    }

    let themeId = $("#theme_id").val();

    function updateThemeSelection(selectedButton, newThemeId) {
        themeId = newThemeId;

        $(".setSection button").removeClass("btn-primary").addClass("btn-dark"); // Reset all
        $(selectedButton).removeClass("btn-dark").addClass("btn-primary"); // Highlight selected

        fetchSection(); // Fetch data with updated theme_id
    }
    $(document).on('click', '.setSection button', function () {
        let selectedText = $(this).text().trim();
        let newThemeId = selectedText === "Screen One" ? 1 : 2;
        updateThemeSelection(this, newThemeId);
    });

    function fetchSection() {
        $(".table-loader").show();
        $(".real-table, .real-data").addClass("d-none");
        $.ajax({
            url: "/api/page-builder/section-list",
            type: "POST",
            dataType: "json",
            data: {
                order_by: "asc",
                sort_by: "id",
                theme_id: themeId, // Include the selected theme_id
            },
            headers: {
                Authorization: "Bearer " + localStorage.getItem("admin_token"),
                Accept: "application/json",
            },
            success: function (response) {
                if (response.code === 200) {
                    var sectionHtml = '<div class="row p-1">';

                    // Get theme_id from the response
                    let themeId = response.theme?.theme_id || 1; // Default to 1 if not provided

                    // Set banner text based on theme_id
                    let bannerText =
                        themeId === 2 ? "Banner Two" : "Banner One";
                    let bannerValue =
                        themeId === 2 ? "banner_two" : "banner_one";

                    sectionHtml += `
                    <div class="col-md-6">
                        <div class="card mb-3 draggable-card shadow-sm border-0 rounded-0" draggable="true" data-value="[${bannerValue}]">
                            <div class="py-2 text-center">
                                <p class="fs-14 fw-bold mb-0">${bannerText}</p>
                            </div>
                        </div>
                    </div>
                `;

                    $.each(response.data, function (index, section) {
                        if (section.name === bannerText) return;

                        $.each(section, function (key, value) {
                            if (
                                key !== "id" &&
                                key !== "name" &&
                                key !== "status"
                            ) {
                                sectionHtml += `
                                <div class="col-md-6">
                                    <div class="card mb-3 draggable-card shadow-sm border-0 rounded-0" draggable="true" data-value="${value}">
                                        <div class="py-2 text-center">
                                            <p class="fs-14 fw-bold mb-0">${section.name}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            }
                        });
                    });

                    sectionHtml += "</div>"; // Close row

                    $("#cardContainer").html(sectionHtml);
                }
            },
            error: function (error) {
                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else {
                        toastr.error("An error occurred while fetching.");
                    }
                } else {
                    toastr.error("An error occurred while fetching.");
                }
            },
            complete: function () {
                $(".table-loader").hide();
                $(".label-loader, .input-loader").hide();
                $(
                    ".real-label, .real-table, .real-data, .real-input"
                ).removeClass("d-none");
            },
        });
    }

    $(document).ready(function () {
        initializeSummernote();

        $("#addTextarea").on("click", function () {
            const uniqueId = `status_${Date.now()}`;

            const textareaTemplate = `
            <div class="textarea-item border p-3 mb-3 mt-3 bg-light">
                <div class="d-flex align-items-center justify-content-end mt-1">
                    <label for="${uniqueId}" class="me-2 fw-bold">${_l(
                "admin.common.status"
            )}</label>
                    <div class="status-toggle modal-status">
                        <input type="checkbox" name="page_status[]" id="${uniqueId}" value="1" class="check user8" checked>
                        <label for="${uniqueId}" class="checktoggle"></label>
                    </div>
                    <a class="removeTextarea ms-3">
                        <i class="ti ti-trash fs-20 fw-bold"></i>
                    </a>
                </div>

                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">${_l(
                            "admin.page.section_title"
                        )} <span class="text-danger">*</span></label>
                        <input type="text" name="section_title[]" placeholder="${_l(
                            "admin.page.enter_title"
                        )}" class="form-control">
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">${_l(
                            "admin.page.section_label"
                        )} <span class="text-danger">*</span></label>
                        <input type="text" name="section_label[]" placeholder="${_l(
                            "admin.page.enter_label"
                        )}" class="form-control">
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">${_l(
                            "admin.page.section_des"
                        )} </label>
                        <textarea name="page_content[]" placeholder="${_l(
                            "admin.page.enter_content"
                        )}" cols="10" rows="3" class="form-control summer"></textarea>
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
            </div>
        `;

            $(".textareasContainer").append(textareaTemplate);
            initializeSummernote();
        });

        $(".textareasContainer").on("click", ".removeTextarea", function () {
            $(this).closest(".textarea-item").remove();
        });
    });

    $(document).on("dragstart", ".draggable-card", function (event) {
        event.originalEvent.dataTransfer.setData(
            "text/plain",
            $(this).data("value")
        );
    });

    $(document).ready(function () {
        $("#language_id").on("change", function () {
            var langId = $(this).val();

            // Extract slug from current URL
            var pathSegments = window.location.pathname.split("/");
            var slug = pathSegments[pathSegments.length - 1]; // Get the last part (slug)

            if (langId && slug) {
                window.location.href =
                    "/admin/edit-pages/" + slug + "?language_id=" + langId;
            }
        });
    });
})();
