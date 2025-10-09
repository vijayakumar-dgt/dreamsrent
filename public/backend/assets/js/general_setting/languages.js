(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        loadLanguages();
        $(document).on(
            "click",
            "#langDropdownMenu li .dropdown-item",
            function () {
                let lang_title = $(this).data("lang_title") ?? "Language";
                $("#langDropdownMenu li .dropdown-item").removeClass(
                    "selected"
                );
                $(this).addClass("selected");
                $("#langText").text(lang_title);
            }
        );

        $(document).on("click", "#addNewLanguage", function () {
            let lang_id = $("#langDropdownMenu li .selected").data("lang");
            let lang_title = $("#langDropdownMenu li .selected").data(
                "lang_title"
            );
            if (lang_id) {
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/add_language",
                    data: {
                        lang_id: lang_id,
                        lang_title: lang_title,
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: function () {
                        $("#addNewLanguage").html(
                            '<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> Adding..'
                        );
                        $("#addNewLanguage").attr("disabled", true);
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            showToast("success", response.message);
                        } else {
                            showToast("error", response.message);
                        }
                    },
                    complete: function () {
                        $("#addNewLanguage").html(
                            `<i class="ti ti-plus me-1"></i> ${_l(
                                "admin.general_settings.add_new_language"
                            )}`
                        );
                        $("#addNewLanguage").attr("disabled", false);
                        $("#langDropdownMenu li .selected").removeClass(
                            "selected"
                        );
                        $("#langText").text("Language");
                        loadLanguages();
                    },
                });
            } else {
                showToast(
                    "error",
                    _l("admin.general_settings.select_language")
                );
            }
        });

        $(document).on("keyup", "#search", function () {
            loadLanguages();
        });
        function loadLanguages() {
            $(".table-loader").show();
            $(".real-table").addClass("d-none");
            let search = $("#search").val();
            $.ajax({
                type: "GET",
                url: "/admin/settings/get_languages",
                data: { search: search },
                success: function (response) {
                    if (response.code === 200) {
                        if (response.data && Object.keys(response.data).length > 0) {
                            let response_data = response.data;
                            let html = "";

                            $.each(response_data, function (key, language) {
                                // Permissions and switches
                                const canEdit = hasPermission(permissions, "website_settings", "edit");
                                const canDelete = hasPermission(permissions, "website_settings", "delete");

                                const rtlSwitch = canEdit
                                    ? `<td>
                                        <div class="form-check form-check-md form-switch">
                                            <input class="form-check-input form-label" data-field="rtl" data-id="${language.id}" type="checkbox" role="switch" ${language.lang_rtl == 1 ? "checked" : ""}>
                                        </div>
                                    </td>`
                                    : "";

                                const defaultSwitch = canEdit
                                    ? `<td>
                                        <div class="form-check form-check-md form-switch">
                                            <input class="form-check-input form-label" data-field="default" data-id="${language.id}" type="checkbox" role="switch" ${language.default == 1 ? "checked" : ""} ${language.default == 1 ? "disabled" : ""}>
                                        </div>
                                    </td>`
                                    : "";

                                const statusSwitch = canEdit
                                    ? `<td>
                                        <div class="form-check form-check-md form-switch">
                                            <input class="form-check-input form-label" data-field="status" data-id="${language.id}" type="checkbox" role="switch" ${language.status == 1 ? "checked" : ""} ${language.lang_code == "en" ? "disabled" : ""}>
                                        </div>
                                    </td>`
                                    : "";

                                const deleteButton = canDelete
                                    ? `<td>
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end p-2">
                                                <li>
                                                    <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal" id="deleteLanguage" data-id="${language.id}">
                                                        <i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>`
                                    : "";

                                html += `<tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="${language.lang_img}" alt="img" class="avatar avatar-sm rounded-circle">&nbsp;
                                                    <p class="fw-semibold">${language.language_name}</p>
                                                </div>
                                            </td>
                                            <td>${language.lang_code}</td>
                                            ${rtlSwitch}${defaultSwitch}
                                            <td>${language.total_keys}</td>
                                            <td>${language.translated_keys}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="circle-progress" data-value="${language.progress}" data-thickness="2">
                                                        <span class="progress-left">
                                                            <span class="progress-bar border-warning"></span>
                                                        </span>
                                                        <span class="progress-right">
                                                            <span class="progress-bar border-warning"></span>
                                                        </span>
                                                    </div>
                                                    <div class="progress-value ms-2">${language.progress}%</div>
                                                </div>
                                            </td>
                                            ${statusSwitch}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="/admin/settings/language?code=${language.lang_code}&type=web" class="btn btn-white me-1">Web</a>
                                                    <a href="/admin/settings/language?code=${language.lang_code}&type=admin" class="btn btn-white">Admin</a>
                                                </div>
                                            </td>
                                            ${deleteButton}
                                        </tr>`;
                            });

                            $("#languageTable tbody").html(html);
                        } else {
                            $("#languageTable tbody").html(
                                `<tr><td colspan="10" class="text-center">${_l(
                                    "admin.common.empty_table"
                                )}</td></tr>`
                            );
                        }
                    } else {
                        $("#languageTable tbody").html(
                            `<tr><td colspan="10" class="text-center">${_l(
                                "admin.common.empty_table"
                            )}</td></tr>`
                        );
                    }
                },
                complete: function () {
                    $(".table-loader").hide();
                    $(".real-table").removeClass("d-none");
                },
            });
        }

        $(document).on("change", ".form-check-input", function () {
            if ($(this).is(":checked")) {
                updateLanguageSettings(
                    $(this).data("id"),
                    $(this).data("field"),
                    1
                );
            } else {
                updateLanguageSettings(
                    $(this).data("id"),
                    $(this).data("field"),
                    0
                );
            }
        });
        function updateLanguageSettings(id, field, value) {
            $.ajax({
                type: "POST",
                url: "/admin/settings/update_language_settings",
                data: {
                    id: id,
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.code === 200) {
                        showToast("success", response.message);
                    } else {
                        showToast("error", response.message);
                    }
                },
                error: function (response) {
                    showToast("error", response.message);
                },
                complete: function () {
                    loadLanguages();
                    location.reload();
                },
            });
        }

        $(document).on("click", "#deleteLanguage", function () {
            let id = $(this).data("id");
            $("#deleteForm #id").val(id);
        });

        $("#deleteForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/admin/settings/delete-language",
                data: $("#deleteForm").serialize(),
                success: function (response) {
                    $("#delete-modal").modal("hide");
                    showToast("success", response.message);
                },
                error: function (error) {
                    showToast("error", error.responseJSON.message);
                    $("#delete-modal").modal("hide");
                },
                complete: function () {
                    loadLanguages();
                },
            });
        });

        const tableWrapper = '.table-responsive';

        $(document).on('show.bs.dropdown', tableWrapper, function () {
            $(this).css('overflow', 'hidden');
        });

        $(document).on('hide.bs.dropdown', tableWrapper, function () {
            $(this).css('overflow', 'auto');
        });
    });
})();
