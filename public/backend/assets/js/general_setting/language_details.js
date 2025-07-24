(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        loadLanguageModules();
        $("#languageDetailsTable").DataTable({
            bFilter: false,
            bLengthChange: false,
            bSort: false,
            bInfo: false,
            bPaginate: false,
        });
        function loadLanguageModules() {
            let code = $("#language").data("id");
            let tab = $("#language").data("tab");
            let search = $("#search").val();
            $.ajax({
                type: "GET",
                url:
                    "/admin/settings/get-language-modules?code=" +
                    code +
                    "&tab=" +
                    tab +
                    "&search=" +
                    search,
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table").addClass("d-none");
                },
                success: function (response) {
                    if (response.code === 200) {
                        let html = "";
                        if (
                            response.data &&
                            Object.keys(response.data).length > 0
                        ) {
                            let data = response.data;
                            let barColor = "success";

                            $.each(data, function (index, module) {
                                switch (true) {
                                    case module.progress >= 100:
                                        barColor = "success";
                                        break;
                                    case module.progress >= 75:
                                        barColor = "pink";
                                        break;
                                    case module.progress >= 50:
                                        barColor = "warning";
                                        break;
                                    case module.progress >= 25:
                                        barColor = "danger";
                                        break;
                                    default:
                                        barColor = "danger";
                                        break;
                                }
                                html += `<tr>
                                        <td>
                                            <div class="fw-semibold text-black">${module.module_name}</div>
                                        </td>
                                        <td>
                                            ${module.total_keys}
                                        </td>
                                        <td>
                                            ${module.translated_keys}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress progress-xs" style="width: 120px;">
                                                    <div class="progress-bar bg-${barColor} rounded" role="progressbar" style="width: ${module.progress}%;" aria-valuenow="${module.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="d-inline-flex fs-12 ms-2">${module.progress}%</span>
                                            </div>
                                        </td>
                                        ${ hasPermission(permissions, 'website_settings', 'edit') ?
                                        `<td>
                                            <button type="button" class="btn btn-icon" id="editModuleLanguage" data-code="${code}" data-tab="${tab}" data-module="${module.module_key}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </td>` : ''}
                                    </tr>`;
                            });
                        } else {
                            html += `<tr>
                                    <td colspan="6" class="text-center">${_l(
                                        "admin.common.empty_table"
                                    )}</td>
                                </tr>`;
                        }
                        $("#languageDetailsTable tbody").html(html);
                    }
                },
                complete: function () {
                    $(".table-loader").hide();
                    $(".real-table").removeClass("d-none");
                },
            });
        }

        $(document).on("keyup", "#search", function (e) {
            if ($(this).val().length >= 3 || $(this).val().length == 0) {
                loadLanguageModules();
            }
        });

        function editModuleLanguage(code, tab, module, _keyword) {
            $.ajax({
                type: "POST",
                url: "/admin/settings/edit-module-language",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    code: code,
                    tab: tab,
                    module: module,
                    keyword: _keyword,
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#lngicon").attr("src", response.icon);
                        $(".lngTitile").text(response.language.trans_lang.name);
                        $("#modalProgressBar")
                            .css("width", response.progress + "%")
                            .removeClass()
                            .addClass("progress-bar rounded " + response.color);
                        $(".modalProgress").text(response.progress + "%");
                        $(".langTitle").text(response.uppercaseName);

                        let html = "";
                        if (
                            response.data &&
                            Object.keys(response.data).length > 0
                        ) {
                            let data = response.data;
                            $.each(data, function (index, language) {
                                html += `<tr>
                                        <td class="lang-label">${
                                            language.default
                                        }</td>
                                        <td class="lang-input">
                                            <input type="text" dir="${
                                                code === "ar" ? "rtl" : "ltr"
                                            }"
                                                   data-tab="${tab}" data-code="${code}" data-module="${module}"
                                                   class="form-control text-end translate" data-key="${
                                                       language.key
                                                   }"
                                                   value="${language.value}">
                                        </td>
                                    </tr>`;
                            });
                        } else {
                            html = `<tr><td colspan="3" class="text-center">${_l(
                                "admin.common.no_data_found"
                            )}</td><td></td></tr>`;
                        }

                        const $table = $("#languageSetupTable");
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }
                        $table.find("tbody").html(html);
                        $table.DataTable({
                            destroy: true,
                            bFilter: false,
                            bLengthChange: false,
                            bSort: false,
                            bInfo: false,
                            bPaginate: true,
                            pageLength: 10,
                        });

                        $("#language_setup").modal("show");
                    } else {
                        showToast("error", response.message);
                    }
                },
                error: function (error) {
                    if (error.responseJSON && error.responseJSON.code === 500) {
                        showToast("error", error.responseJSON.message);
                    } else {
                        showToast(
                            "error",
                            _l("admin.general_settings.retrive_error")
                        );
                    }
                },
            });
        }

        $(document).on("click", "#editModuleLanguage", function () {
            let code = $(this).data("code");
            let tab = $(this).data("tab");
            let module = $(this).data("module");
            let keyword = $("#keyword").val();
            $("#keyword").val("");
            $("#keyword").data("code", code);
            $("#keyword").data("tab", tab);
            $("#keyword").data("module", module);
            editModuleLanguage(code, tab, module, keyword);
        });

        $(document).on("keyup", "#keyword", function (e) {
            let code = $(this).data("code");
            let tab = $(this).data("tab");
            let module = $(this).data("module");
            let keyword = $(this).val();
            if (keyword.length >= 3 || keyword.length == 0) {
                editModuleLanguage(code, tab, module, keyword);
            }
        });
        $(document).on("blur", ".translate", function () {
            let key = $(this).data("key");
            let value = $(this).val();
            let tab = $(this).data("tab");
            let code = $(this).data("code");
            let module = $(this).data("module");
            $.ajax({
                type: "POST",
                url: "/admin/settings/update-module-language",
                data: {
                    key: key,
                    value: value,
                    tab: tab,
                    code: code,
                    module: module,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.code === 200) {
                        $("#modalProgressBar").css(
                            "width",
                            response.progress + "%"
                        );
                        $("#modalProgressBar").removeClass();
                        $("#modalProgressBar").addClass(
                            "progress-bar  rounded " + response.color
                        );
                        $(".modalProgress").text(response.progress + "%");
                        showToast("success", response.message);
                    } else {
                        showToast("error", response.message);
                    }
                },
                error: function (error) {
                    if (error.responseJSON.code === 500) {
                        showToast("error", error.responseJSON.message);
                    } else {
                        showToast(
                            "error",
                            _l("admin.general_settings.retrive_error")
                        );
                    }
                },
                complete: function () {
                    loadLanguageModules();
                },
            });
        });
    });
})();
