(async () => {
    await loadTranslationFile("admin", "common, cms");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
    });
    
    function initTable() {
        $.ajax({
            url: "/admin/section-list",
            type: "GET",
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#sectionTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#sectionTable")) {
                    $("#sectionTable").DataTable().destroy();
                }
    
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;
    
                    $.each(data, function (index, value) {
                        tableBody += `<tr>
                                <td>${value.name}</td>
                                <td>${value.theme_id}</td>
                                <td>
                                    <span class="badge ${
                                        value.status == 1
                                            ? "badge-success-transparent"
                                            : "badge-danger-transparent"
                                    } d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>${
                                            value.status == 1
                                                ? `${_l('admin.common.active')}`
                                                : `${_l('admin.common.inactive')}`
                                        }
                                    </span>
                                </td>
                 ${hasPermission(permissions, 'section', 'edit')  ?
       
                                `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${hasPermission(permissions, 'section', 'edit') ?
    
                                        `<li>
                                            <a class="dropdown-item rounded-1 section_data" 
                                                href="#" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#add_banner_sec"
                                                data-id="${value.id}"
                                                data-name="${value.name}"
                                                data-label_one="${
                                                    value.label_one ?? ""
                                                }"
                                                data-label_two="${
                                                    value.label_two ?? ""
                                                }"
                                                data-description_one="${
                                                    value.description_one ?? ""
                                                }"
                                                data-description_two="${
                                                    value.description_two ?? ""
                                                }"
                                                data-line_two="${
                                                    value.line_two ?? ""
                                                }"
                                                data-thumbnail_image_one="${
                                                    value.thumbnail_image_one ?? ""
                                                }"
                                                data-thumbnail_image_two="${
                                                    value.thumbnail_image_two ?? ""
                                                }"
                                                data-line_one="${
                                                    value.line_one ?? ""
                                                }">
                                                <i class="ti ti-pencil me-1"></i>${_l('admin.common.edit')}
                                            </a>
                                        </li>`:''}
                                    </ul>
                                </div>
                            </td>`:''}
                            </tr>`;
                    });
                } else {
                    tableBody += `
                            <tr>
                                <td colspan="6" class="text-center">${_l('admin.common.empty_table')}</td></td>
                            </tr>`;
                    $(".table-footer").empty();
                }
    
                $("#sectionTable tbody").html(tableBody);
                if (response.data.length > 0) {
                    $("#sectionTable").DataTable({
                        ordering: true,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        "drawCallback": function () {
                            $(".dataTables_info").addClass('d-none');
                            $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');

                            var tableWrapper = $(this).closest('.dataTables_wrapper');
                            var info = tableWrapper.find('.dataTables_info');
                            var pagination = tableWrapper.find('.dataTables_paginate');

                            $('.table-footer').empty()
                                .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                                    .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                                    .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
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
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast('error', error.responseJSON.message);
                } else {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                }
            },
        });
    }
    
})();


$(document).on("click", ".section_data", function (e) {
    e.preventDefault();

    var ID = $(this).data("id");

    $("#section_id_1, #section_id_2, #section_id_3").hide();

    if (ID == 1) {
        $("#section_id_1").show();
        $("#section_id").val($(this).data("id"));
        $("#description_one").val($(this).data("description_one"));
        $("#label_one").val($(this).data("label_one"));
        $("#line_two").val($(this).data("line_two"));
        $("#line_one").val($(this).data("line_one"));
        let thumbnailImageUrl = $(this).data("thumbnail_image_one"); // Already a full URL

        // No need to set input value for a file, but you can store image name if needed in hidden input

        if (thumbnailImageUrl) {
            $("#thumbnail_preview_one").attr("src", thumbnailImageUrl).show();
        } else {
            $("#thumbnail_preview_one").hide();
        }
    } else if (ID == 29) {
        $("#section_id_2").show();
        $("#section_id").val($(this).data("id"));
        $("#description_two").val($(this).data("description_two"));
        $("#label_two").val($(this).data("label_two"));
        let thumbnailImageUrl = $(this).data("thumbnail_image_two"); // Already a full URL

        // No need to set input value for a file, but you can store image name if needed in hidden input

        if (thumbnailImageUrl) {
            $("#thumbnail_preview_two").attr("src", thumbnailImageUrl).show();
        } else {
            $("#thumbnail_preview_two").hide();
        }
    } else if (ID == 42) {
        $("#section_id_3").show();
        $("#section_id").val($(this).data("id"));
    }
});

$(document).ready(function () {
    $("#addBannerOneForm").submit(function (event) {
        event.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: "/admin/section-store",
            method: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $('.banner_one').attr('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                `);
            },
            complete: function () {
                $('.banner_one').attr('disabled', false).html(_l('admin.common.save_changes'));
            },
        })
            .done((response, statusText, xhr) => {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid");
                if (response.code === 200) {
                    showToast("success", response.message);

                    $("#add_banner_sec").modal("hide");
                    initTable();
                } else {
                    showToast("success", response.message);

                }
            })
            .fail((error) => {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid");

                if (error.status == 422) {
                    $.each(error.responseJSON, function (key, val) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(val[0]);
                    });
                } else {
                    showToast("error", error.responseJSON.message);
                }
            });
    });
});

function previewThumbnailOne(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#thumbnail_preview_one").attr("src", e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewThumbnailTwo(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#thumbnail_preview_two").attr("src", e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}
