(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    $(document).ready(function () {
        let table;
        initTable();
        $("#sitemapForm").validate({
            rules: {
                url: {
                    required: true,
                    pattern:
                        /^(https?:\/\/)?((localhost|(\d{1,3}\.){3}\d{1,3})|([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}))(:\d+)?(\/.*)?$/,
                    minlength: 3,
                    maxlength: 200,
                },
            },
            messages: {
                url: {
                    required: _l("admin.general_settings.enter_url"),
                    pattern: _l("admin.general_settings.enter_valid_url"),
                    minlength: _l("admin.general_settings.url_least_character"),
                    maxlength: _l("admin.general_settings.url_most_character"),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                let errorId = element.id + "_error";
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
                $("#sitemapForm .submitbtn").html(
                    '<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> Saving..'
                );

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/save-sitemap-url",
                    data: _formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $("#sitemapForm .submitbtn").attr("disabled", true)
                            .html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                            "admin.common.saving"
                        )}..
                    `);
                    },
                    complete: function () {
                        $("#sitemapForm .submitbtn")
                            .attr("disabled", false)
                            .html(_l("admin.common.save_changes"));
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_sitemap").modal("hide");
                        } else {
                            showToast("error", resp.message);
                        }
                        $("#sitemapForm")[0].reset();
                        $("#sitemapForm .submitbtn").text(
                            _l("admin.common.create_new")
                        );

                        table.ajax.reload();
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                        $("#sitemapForm .submitbtn").text(
                            _l("admin.common.create_new")
                        );
                    },
                });
            },
        });

        function initTable() {
            table = $("#sitemapTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/settings/get_sitemap_urls",
                    type: "POST",
                    data: function (d) {
                        d._token = $('meta[name="csrf-token"]').attr("content");
                        d.keyword = $("#keyword").val();
                    },
                    beforeSend: function () {
                        $(".table-loader").show();
                        $(".real-table, .table-footer").addClass("d-none");
                    },
                },
                order: [["1", "desc"]],
                ordering: false,
                searching: false,
                pageLength: 10,
                lengthChange: false,
                responsive: false,
                autoWidth: false,
                aoColumns: [
                    {
                        data: "url",
                        render: function (data, type, row) {
                            return `<p class="text-gray-9 fw-semibold fs-14">${row.url}</p>`;
                        },
                    },
                    {
                        data: "sitemap_path",
                        render: function (data, type, row) {
                            return `<p class="text-gray-9 fw-semibold fs-14"><a href="${row.filePath}" target="_blank" id="viewTemplate" data-id="${row.id}">${row.sitemap_path}</a></p>`;
                        },
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(${
                                                    row.id
                                                });" id="deleteSitemap" data-id="${
                                row.id
                            }" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l(
                                "admin.common.delete"
                            )}</a>
                                            </li>
                                        </ul>
                                    </div>`;
                        },
                    },
                ],
                initComplete: function () {
                    $(".table-loader").hide();
                    $(".real-table, .table-footer").removeClass("d-none");
                },
                drawCallback: function () {
                    customizeTableFooter($(this));
                },
                language: getDataTableLanguage(),
            });
        }

        $(document).on("click", "#deleteSitemap", function () {
            let delete_id = $(this).data("id");
            $("#deleteForm #id").val(delete_id);
        });

        $(document).on("keyup", "#keyword", function () {
            $("#sitemapTable").DataTable().ajax.reload();
        });

        $("#deleteForm").on("submit", function (e) {
            e.preventDefault();
            $("#deleteForm .submitbtn").prop("disabled", true);
            $("#deleteForm .submitbtn").html(
                '<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> Deleting..'
            );
            $(".table-loader").show();
            $(".real-table, .table-footer").addClass("d-none");
            $.ajax({
                type: "POST",
                url: "/admin/settings/delete-sitemapurl",
                data: $("#deleteForm").serialize(),
                success: function (response) {
                    $("#deleteForm .submitbtn").prop("disabled", false);
                    $("#deleteForm .submitbtn").text(
                        _l("admin.common.yes_delete")
                    );
                    $("#delete-modal").modal("hide");
                    table.ajax.reload();
                    showToast("success", response.message);
                },
                error: function (error) {
                    showToast("error", error.responseJSON.message);
                    $("#deleteForm .submitbtn").prop("disabled", false);
                    $("#deleteForm .submitbtn").text(
                        _l("admin.common.yes_delete")
                    );
                    $("#delete-modal").modal("hide");
                },
            });
        });
    });
})();
