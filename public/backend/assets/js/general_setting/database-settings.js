(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    const permissions = await loadUserPermissions();

    DbBackUpTable();

    function DbBackUpTable() {
        $.ajax({
            url: "/admin/settings/dbbackups",
            method: "GET",
            dataType: "json",
            success: function (response) {
                let tableBody = $("#backup-list");
                tableBody.empty();

                if (response.data.length === 0) {
                    const emptyRow = $("<tr>").append(
                        $("<td>")
                            .attr("colspan", 3)
                            .append(
                                $("<p>")
                                    .addClass("text-gray-9 text-center m-0")
                                    .text(_l("admin.common.empty_table"))
                            )
                    );
                    tableBody.append(emptyRow);
                    return;
                }

                response.data.forEach((backup) => {
                   
                    const safeName = DOMPurify.sanitize(backup.name || "");
                    const safeCreatedOn = DOMPurify.sanitize(
                        backup.created_on || ""
                    );
                    const safeDownloadUrl = DOMPurify.sanitize(
                        backup.download_url || ""
                    );
                    const safeId = DOMPurify.sanitize(
                        backup.id?.toString() || ""
                    );

                    const row = $("<tr>");

                    
                    const nameTd = $("<td>").append(
                        $("<h6>")
                            .addClass("fw-semibold fs-14")
                            .append(
                                $("<a>")
                                    .attr("href", safeDownloadUrl)
                                    .attr("download", "")
                                    .text(safeName)
                            )
                    );

                   
                    const createdTd = $("<td>").append(
                        $("<p>").addClass("text-gray-9").text(safeCreatedOn)
                    );

                   
                    const actionTd = $("<td>").append(
                        $("<div>")
                            .addClass("dropdown")
                            .append(
                                $("<button>")
                                    .addClass("btn btn-icon btn-sm")
                                    .attr({
                                        type: "button",
                                        "data-bs-toggle": "dropdown",
                                        "aria-expanded": "false",
                                    })
                                    .append(
                                        $("<i>").addClass("ti ti-dots-vertical")
                                    ),
                                $("<ul>")
                                    .addClass(
                                        "dropdown-menu dropdown-menu-end p-2"
                                    )
                                    .append(
                                       
                                        $("<li>").append(
                                            $("<a>")
                                                .addClass(
                                                    "dropdown-item rounded-1"
                                                )
                                                .attr({
                                                    href: safeDownloadUrl,
                                                    download: "",
                                                })
                                                .append(
                                                    $("<i>").addClass(
                                                        "ti ti-download me-1"
                                                    ),
                                                    document.createTextNode(
                                                        _l(
                                                            "admin.common.download"
                                                        )
                                                    )
                                                )
                                        ),
                                        
                                        hasPermission(
                                            permissions,
                                            "other_settings",
                                            "delete"
                                        )
                                            ? $("<li>").append(
                                                  $("<button>")
                                                      .addClass(
                                                          "dropdown-item rounded-1"
                                                      )
                                                      .attr({
                                                          type: "button",
                                                          "data-bs-toggle":
                                                              "modal",
                                                          "data-bs-target":
                                                              "#delete_backup",
                                                          "data-id": safeId,
                                                          id: "delete-backup",
                                                      })
                                                      .append(
                                                          $("<i>").addClass(
                                                              "ti ti-trash me-1"
                                                          ),
                                                          document.createTextNode(
                                                              _l(
                                                                  "admin.general_settings.delete"
                                                              )
                                                          )
                                                      )
                                              )
                                            : null
                                    )
                            )
                    );

                    row.append(nameTd, createdTd, actionTd);
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
                console.error("Error fetching backups:", error);
            },
        });
    }
    $(document).on("click", "#delete-backup", function () {
        let id = $(this).data("id");
        deleteBackup(id);
    });
    function deleteBackup(id) {
        $("#delete_id").val(id);
    }
    $("#deleteDbBackup").on("submit", function (e) {
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
                    showToast("error", _l("admin.general_settings.delete"));
                }
            },
        });
    });
})();
