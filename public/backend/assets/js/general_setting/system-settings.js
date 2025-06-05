(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    const permissions = await loadUserPermissions();
    $(document).on("click", ".delete-backup-btn", function () {
        const id = $(this).data("id");
        deleteSystemBackup(id);
    });
    DbBackUpTable();

    function DbBackUpTable() {
        $.ajax({
            url: "/admin/settings/system-backup/list",
            method: "GET",
            dataType: "json",
            success: function (response) {
                const tableBody = $("#system-backup-list");
                tableBody.empty();

                if (!response.data || response.data.length === 0) {
                    tableBody.append(
                        $("<tr>").append(
                            $("<td>")
                                .attr("colspan", 4)
                                .append(
                                    $("<p>")
                                        .addClass("text-gray-9 text-center")
                                        .text(_l("admin.common.empty_table"))
                                )
                        )
                    );
                    return;
                }

                response.data.forEach((backup) => {
                    const downloadUrl = DOMPurify.sanitize(
                        backup.download_url || ""
                    );
                    const backupName = DOMPurify.sanitize(backup.name || "");
                    const createdOn = DOMPurify.sanitize(
                        backup.created_on || ""
                    );

                    const row = $("<tr>");

                    const nameCell = $("<td>").append(
                        $("<h6>")
                            .addClass("fw-semibold fs-14")
                            .append(
                                $("<a>")
                                    .attr({ href: downloadUrl, download: "" })
                                    .text(backupName)
                            )
                    );

                    const createdCell = $("<td>").append(
                        $("<p>").addClass("text-gray-9").text(createdOn)
                    );

                    const dropdownBtn = $("<button>")
                        .addClass("btn btn-icon btn-sm")
                        .attr({
                            type: "button",
                            "data-bs-toggle": "dropdown",
                            "aria-expanded": "false",
                        })
                        .append($("<i>").addClass("ti ti-dots-vertical"));

                    const downloadItem = $("<a>")
                        .addClass("dropdown-item rounded-1")
                        .attr({ href: downloadUrl, download: "" })
                        .append($("<i>").addClass("ti ti-download me-1"))
                        .append(
                            document.createTextNode(_l("admin.common.download"))
                        );

                    const dropdownList = $("<ul>")
                        .addClass("dropdown-menu dropdown-menu-end p-2")
                        .append($("<li>").append(downloadItem));

                    if (
                        hasPermission(permissions, "other_settings", "delete")
                    ) {
                        const deleteBtn = $("<button>")
                            .addClass(
                                "dropdown-item rounded-1 delete-backup-btn"
                            )
                            .attr({
                                type: "button",
                                "data-id": backup.id,
                                "data-bs-toggle": "modal",
                                "data-bs-target": "#delete_backup",
                            })
                            .append($("<i>").addClass("ti ti-trash me-1"))
                            .append(
                                document.createTextNode(
                                    _l("admin.general_settings.delete")
                                )
                            );
                        const cleandeleteBtn = DOMPurify.sanitize(deleteBtn);
                        dropdownList.append($("<li>").append(cleandeleteBtn));
                    }

                    const actionsCell = $("<td>").append(
                        $("<div>")
                            .addClass("dropdown")
                            .append(dropdownBtn, dropdownList)
                    );

                    row.append(nameCell, createdCell, actionsCell);
                    tableBody.append(row);
                });
            },
            complete: function () {
                $(".table-loader").hide();
                $(".label-loader, .input-loader").hide();
                $(
                    ".real-label, .real-table, .real-data, .table-footer"
                ).removeClass("d-none");
            },
            error: function (error) {
                showToast("error", _l("admin.general_settings.retrive_error"));
            },
        });
    }

    $("#deleteSystemDbBackup").on("submit", function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/settings/backups/delete",
            type: "POST",
            data: {
                id: $("#delete_id").val(),
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    showToast("success", response.message);
                    $("#delete_backup").modal("hide");
                    DbBackUpTable();
                }
            },
            error: function (res) {
                if (res.responseJSON.code === 500) {
                    showToast("error", res.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.general_settings.retrive_error")
                    );
                }
            },
        });
    });
    function deleteSystemBackup(id) {
        $("#delete_id").val(id);
    }
})();
