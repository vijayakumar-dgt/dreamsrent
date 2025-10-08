(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, cms");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();

        $("#addBannerOneForm").submit(function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            submitBannerForm(formData);
        });

        function submitBannerForm(formData) {
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
                beforeSend: onBannerBeforeSend,
                complete: onBannerComplete,
            })
                .done(onBannerSuccess)
                .fail(onBannerError);
        }

        function onBannerBeforeSend() {
            $(".banner_one").attr("disabled", true).html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
            `);
        }

        function onBannerComplete() {
            $(".banner_one").attr("disabled", false).html(_l("admin.common.save_changes"));
        }

        function onBannerSuccess(response) {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid");
            if (response.code === 200) {
                showToast("success", response.message);
                $("#add_banner_sec").modal("hide");
                initTable();
            } else {
                showToast("success", response.message);
            }
        }

        function onBannerError(error) {
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
        }


        $(document).on("click", ".section_data", function (e) {
            e.preventDefault();

            var ID = $(this).data("id");

            $(
                "#section_id_1, #section_id_2, #section_id_3, #section_id_4, #section_id_5,  #section_id_6, #section_id_7, #section_id_8, #section_id_9, #section_id_10, #section_id_11, #section_id_12, #section_id_13, section_id_14"
            ).addClass("d-none");

            if (ID == 1) {
                $("#section_id_1").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_one").val($(this).data("section_title_one"));
                $("#description_one").val($(this).data("description_one"));
                $("#label_one").val($(this).data("label_one"));
                $("#line_two").val($(this).data("line_two"));
                $("#line_one").val($(this).data("line_one"));

                let thumbnailImageUrl = $(this).data("thumbnail_image_one");

                if (thumbnailImageUrl) {
                    $("#thumbnail_preview_one")
                        .attr("src", thumbnailImageUrl)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_one").addClass("d-none");
                }
            } else if (ID == 29) {
                $("#section_id_2").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_two").val($(this).data("section_title_two"));
                $("#description_two").val($(this).data("description_two"));
                $("#label_two").val($(this).data("label_two"));

                let thumbnailImageUrl = $(this).data("thumbnail_image_two");

                if (thumbnailImageUrl) {
                    $("#thumbnail_preview_two")
                        .attr("src", thumbnailImageUrl)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_two").addClass("d-none");
                }
            } else if (ID == 42) {
                $("#section_id_3").removeClass("d-none");
                $("#section_title_three").val(
                    $(this).data("section_title_three")
                );
                $("#section_id").val(ID);
                $("#label_1").val($(this).data("label_1"));
                $("#label_2").val($(this).data("label_2"));
                $("#label_3").val($(this).data("label_3"));
                $("#label_4").val($(this).data("label_4"));
                $("#label_5").val($(this).data("label_5"));
                $("#label_6").val($(this).data("label_6"));
                $("#dis_1").val($(this).data("dis_1"));
                $("#dis_2").val($(this).data("dis_2"));
                $("#dis_3").val($(this).data("dis_3"));
                $("#dis_4").val($(this).data("dis_4"));
                $("#dis_5").val($(this).data("dis_5"));
                $("#dis_6").val($(this).data("dis_6"));
            } else if (ID == 75) {
                $("#section_id_13").removeClass("d-none");
                $("#section_title_bike").val(
                    $(this).data("section_title_bike")
                );
                $("#section_id").val(ID);

                $("#bike_label_1").val($(this).data("bike_label_1"));
                $("#bike_label_2").val($(this).data("bike_label_2"));
                $("#bike_label_3").val($(this).data("bike_label_3"));
                $("#bike_label_4").val($(this).data("bike_label_4"));

                $("#bike_dis_1").val($(this).data("bike_dis_1"));
                $("#bike_dis_2").val($(this).data("bike_dis_2"));
                $("#bike_dis_3").val($(this).data("bike_dis_3"));
                $("#bike_dis_4").val($(this).data("bike_dis_4"));

                const imageOne = $(this).data("thumbnail_image_bike_exclusive");
                if (imageOne) {
                    $("#thumbnail_preview_bike_exclusive")
                        .attr("src", imageOne)
                        .removeClass("d-none");
                }
            } else if (ID == 26) {
                $("#section_id_4").removeClass("d-none");
                $("#section_id").val(ID);

                const trigger = $(this);

                $("#section_title_four").val(
                    $(this).data("section_title_four")
                );
                $("#why_label_1").val(trigger.data("why_label_1"));
                $("#why_dis_1").val(trigger.data("why_dis_1"));
                $("#why_label_2").val(trigger.data("why_label_2"));
                $("#why_dis_2").val(trigger.data("why_dis_2"));
                $("#why_label_3").val(trigger.data("why_label_3"));
                $("#why_dis_3").val(trigger.data("why_dis_3"));

                const icon1 = trigger.data("why_icon_1");
                const icon2 = trigger.data("why_icon_2");
                const icon3 = trigger.data("why_icon_3");

                if (icon1) {
                    $("#preview_why_icon_1")
                        .attr("src", icon1)
                        .removeClass("d-none");
                }

                if (icon2) {
                    $("#preview_why_icon_2")
                        .attr("src", icon2)
                        .removeClass("d-none");
                }

                if (icon3) {
                    $("#preview_why_icon_3")
                        .attr("src", icon3)
                        .removeClass("d-none");
                }
            } else if (ID == 58) {
                $("#section_id_7").removeClass("d-none");
                $("#section_id").val(ID);

                const trigger = $(this);

                $("#section_title_boat_benefits").val(
                    trigger.data("section_title_boat_benefits")
                );

                $("#label_boat_benefits_1").val(
                    trigger.data("label_boat_benefits_1")
                );
                $("#description_boat_benefits_1").val(
                    trigger.data("description_boat_benefits_1")
                );

                $("#label_boat_benefits_2").val(
                    trigger.data("label_boat_benefits_2")
                );
                $("#description_boat_benefits_2").val(
                    trigger.data("description_boat_benefits_2")
                );

                $("#label_boat_benefits_3").val(
                    trigger.data("label_boat_benefits_3")
                );
                $("#description_boat_benefits_3").val(
                    trigger.data("description_boat_benefits_3")
                );

                $("#label_boat_benefits_4").val(
                    trigger.data("label_boat_benefits_4")
                );
                $("#description_boat_benefits_4").val(
                    trigger.data("description_boat_benefits_4")
                );

                $("#label_boat_benefits_5").val(
                    trigger.data("label_boat_benefits_5")
                );
                $("#description_boat_benefits_5").val(
                    trigger.data("description_boat_benefits_5")
                );

                $("#label_boat_benefits_6").val(
                    trigger.data("label_boat_benefits_6")
                );
                $("#description_boat_benefits_6").val(
                    trigger.data("description_boat_benefits_6")
                );

                const imageMian = trigger.data(
                    "thumbnail_image_boat_benefits_main"
                );
                const image1 = trigger.data("thumbnail_image_boat_benefits_1");
                const image2 = trigger.data("thumbnail_image_boat_benefits_2");
                const image3 = trigger.data("thumbnail_image_boat_benefits_3");
                const image4 = trigger.data("thumbnail_image_boat_benefits_4");
                const image5 = trigger.data("thumbnail_image_boat_benefits_5");
                const image6 = trigger.data("thumbnail_image_boat_benefits_6");

                if (imageMian) {
                    $("#thumbnail_preview_boat_benefits_main")
                        .attr("src", imageMian)
                        .removeClass("d-none");
                }
                if (image1) {
                    $("#thumbnail_preview_boat_benefits_1")
                        .attr("src", image1)
                        .removeClass("d-none");
                }
                if (image2) {
                    $("#thumbnail_preview_boat_benefits_2")
                        .attr("src", image2)
                        .removeClass("d-none");
                }
                if (image3) {
                    $("#thumbnail_preview_boat_benefits_3")
                        .attr("src", image3)
                        .removeClass("d-none");
                }
                if (image4) {
                    $("#thumbnail_preview_boat_benefits_4")
                        .attr("src", image4)
                        .removeClass("d-none");
                }
                if (image5) {
                    $("#thumbnail_preview_boat_benefits_5")
                        .attr("src", image5)
                        .removeClass("d-none");
                }
                if (image6) {
                    $("#thumbnail_preview_boat_benefits_6")
                        .attr("src", image6)
                        .removeClass("d-none");
                }
            } else if (ID == 68) {
                $("#section_id_8").removeClass("d-none");
                $("#section_id").val(ID);

                const trigger = $(this);

                $("#section_title_boat_experience").val(
                    trigger.data("section_title_boat_experience")
                );

                $("#label_boat_experience_1").val(
                    trigger.data("label_boat_experience_1")
                );
                $("#description_boat_experience_1").val(
                    trigger.data("description_boat_experience_1")
                );

                const image1 = trigger.data(
                    "thumbnail_image_boat_experience_1"
                );
                if (image1) {
                    $("#thumbnail_preview_boat_experience_1")
                        .attr("src", image1)
                        .removeClass("d-none");
                }

                const image2 = trigger.data(
                    "thumbnail_image_boat_experience_2"
                );
                if (image2) {
                    $("#thumbnail_preview_boat_experience_2")
                        .attr("src", image2)
                        .removeClass("d-none");
                }
            } else if (ID == 71) {
                $("#section_id_9").removeClass("d-none");
                $("#section_id").val(ID);

                const trigger = $(this);

                $("#section_title_bike_experience").val(
                    trigger.data("section_title_bike_experience")
                );

                $("#label_bike_experience_1").val(
                    trigger.data("label_bike_experience_1")
                );

                const image1 = trigger.data(
                    "thumbnail_image_bike_experience_1"
                );
                if (image1) {
                    $("#thumbnail_preview_bike_experience_1")
                        .attr("src", image1)
                        .removeClass("d-none");
                }
            } else if (ID == 43) {
                $("#section_id_5").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_five").val(
                    $(this).data("section_title_five")
                );
                $("#description_three").val($(this).data("description_three"));
                $("#label_three_one").val($(this).data("label_three_one"));
                $("#label_three_two").val($(this).data("label_three_two"));
                $("#label_three_three").val($(this).data("label_three_three"));

                let thumbnailImageUrl = $(this).data("thumbnail_image_four");

                if (thumbnailImageUrl) {
                    $("#thumbnail_preview_four")
                        .attr("src", thumbnailImageUrl)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_four").addClass("d-none");
                }
            } else if (ID == 56) {
                $("#section_id_6").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_boat").val(
                    $(this).data("section_title_boat")
                );
                $("#description_boat").val($(this).data("description_boat"));
                $("#label_boat_one").val($(this).data("label_boat_one"));
                $("#label_boat_two").val($(this).data("label_boat_two"));
                $("#label_boat_three").val($(this).data("label_boat_three"));

                let thumbnails = $(this).data("thumbnail_image_boat");

                console.log(thumbnails);
                if (thumbnails && Array.isArray(thumbnails)) {
                    $("#thumbnail_preview_boat_container").empty();
                    thumbnails.forEach((url) => {
                        $("#thumbnail_preview_boat_container").append(
                            `<img src="${url}" class="img-preview-thumb me-2 mb-2" style="width: 100px; height: auto;">`
                        );
                    });
                }
            } else if (ID == 72) {
                $("#section_id_10").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_boat_seasonal").val(
                    $(this).data("section_title_boat_seasonal")
                );
                let thumbnails = $(this).data("thumbnail_image_boat_seasonal");

                if (thumbnails) {
                    $("#thumbnail_preview_boat_seasonal")
                        .attr("src", thumbnails)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_boat_seasonal").addClass("d-none");
                }
            } else if (ID == 25) {
                $("#section_id_14").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_car_ad").val(
                    $(this).data("section_title_car_ad")
                );
                let thumbnails = $(this).data("thumbnail_image_car_ad");

                if (thumbnails) {
                    $("#thumbnail_preview_car_ad")
                        .attr("src", thumbnails)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_car_ad").addClass("d-none");
                }
            } else if (ID == 73) {
                $("#section_id_11").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_boat_offer").val(
                    $(this).data("section_title_boat_offer")
                );
                let thumbnails = $(this).data("thumbnail_image_boat_offer");

                if (thumbnails) {
                    $("#thumbnail_preview_boat_offer")
                        .attr("src", thumbnails)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_boat_offer").addClass("d-none");
                }
            } else if (ID == 74) {
                $("#section_id_12").removeClass("d-none");
                $("#section_id").val(ID);
                $("#section_title_boat_exclusive").val(
                    $(this).data("section_title_boat_exclusive")
                );
                let thumbnails = $(this).data("thumbnail_image_boat_exclusive");

                if (thumbnails) {
                    $("#thumbnail_preview_boat_exclusive")
                        .attr("src", thumbnails)
                        .removeClass("d-none");
                } else {
                    $("#thumbnail_preview_boat_exclusive").addClass("d-none");
                }
            }
        });
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
                $(".real-table, .real-label, .real-input").removeClass(
                    "d-none"
                );
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
                                <td>${value.title}</td>
                                <td>${value.theme_id}</td>
                                <td>
                                    <span class="badge ${
                                        value.status == 1
                                            ? "badge-success-transparent"
                                            : "badge-danger-transparent"
                                    } d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>${
                                            value.status == 1
                                                ? `${_l("admin.common.active")}`
                                                : `${_l(
                                                      "admin.common.inactive"
                                                  )}`
                                        }
                                    </span>
                                </td>
                 ${
                     hasPermission(permissions, "section", "edit")
                         ? `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${
                                                hasPermission(
                                                    permissions,
                                                    "section",
                                                    "edit"
                                                )
                                                    ? `<li>
                                            <a class="dropdown-item rounded-1 section_data"
                                                href="#"
                                                data-bs-toggle="modal"
                                                data-bs-target="#add_banner_sec"
                                                data-id="${value.id}"
                                                data-name="${value.name}"
                                                data-section_title_one="${
                                                    value.title
                                                }"
                                                data-section_title_two="${
                                                    value.title
                                                }"
                                                data-section_title_three="${
                                                    value.title
                                                }"
                                                data-section_title_four="${
                                                    value.title
                                                }"
                                                data-section_title_five="${
                                                    value.title
                                                }"
                                                data-section_title_boat="${
                                                    value.title
                                                }"
                                                data-section_title_boat_benefits="${
                                                    value.title
                                                }"
                                                data-section_title_boat_experience="${
                                                    value.title
                                                }"
                                                data-section_title_bike_experience="${
                                                    value.title
                                                }"
                                                data-section_title_boat_seasonal="${
                                                    value.title
                                                }"
                                                data-section_title_boat_offer="${
                                                    value.title
                                                }"
                                                data-section_title_boat_exclusive="${
                                                    value.title
                                                }"
                                                data-section_title_bike="${
                                                    value.title
                                                }"
                                                data-section_title_car_ad="${
                                                    value.title
                                                }"
                                                data-label_1="${value.label_1}"
                                                data-label_2="${value.label_2}"
                                                data-label_3="${value.label_3}"
                                                data-label_4="${value.label_4}"
                                                data-label_5="${value.label_5}"
                                                data-label_6="${value.label_6}"
                                                data-bike_label_1="${value.bike_label_1}"
                                                data-bike_label_2="${value.bike_label_2}"
                                                data-bike_label_3="${value.bike_label_3}"
                                                data-bike_label_4="${value.bike_label_4}"
                                                data-bike_dis_1="${value.bike_dis_1}"
                                                data-bike_dis_2="${value.bike_dis_2}"
                                                data-bike_dis_3="${value.bike_dis_3}"
                                                data-bike_dis_4="${value.bike_dis_4}"
                                                data-dis_1="${value.dis_1}"
                                                data-dis_2="${value.dis_2}"
                                                data-dis_3="${value.dis_3}"
                                                data-dis_4="${value.dis_4}"
                                                data-dis_5="${value.dis_5}"
                                                data-dis_6="${value.dis_6}"
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
                                                data-thumbnail_image_boat_seasonal="${
                                                    value.thumbnail_image_boat_seasonal ??
                                                    ""
                                                }"
                                                data-thumbnail_image_car_ad="${
                                                    value.thumbnail_image_car_ad ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_offer="${
                                                    value.thumbnail_image_boat_offer ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_exclusive="${
                                                    value.thumbnail_image_boat_exclusive ??
                                                    ""
                                                }"
                                                data-thumbnail_image_one="${
                                                    value.thumbnail_image_one ??
                                                    ""
                                                }"
                                                data-thumbnail_image_three="${
                                                    value.thumbnail_image_two ??
                                                    ""
                                                }"
                                                data-thumbnail_image_four="${
                                                    value.thumbnail_image_four ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat='@json($value->thumbnail_image_boat ?? [])'
                                                data-label_three="${
                                                    value.label_three ?? ""
                                                }"
                                                data-description_three="${
                                                    value.description_three ??
                                                    ""
                                                }"
                                                data-label_three_one="${
                                                    value.label_three_one ?? ""
                                                }"
                                                data-label_three_two="${
                                                    value.label_three_two ?? ""
                                                }"
                                                data-label_three_three="${
                                                    value.label_three_three ??
                                                    ""
                                                }"
                                                data-label_boat_one="${
                                                    value.label_boat_one ?? ""
                                                }"
                                                data-label_boat_two="${
                                                    value.label_boat_two ?? ""
                                                }"
                                                data-label_boat_three="${
                                                    value.label_boat_three ?? ""
                                                }"
                                                data-description_boat="${
                                                    value.description_boat ?? ""
                                                }"
                                                data-line_one="${
                                                    value.line_one ?? ""
                                                }"
                                                data-why_label_1="${
                                                    value.why_label_1 ?? ""
                                                }"
                                                data-why_dis_1="${
                                                    value.why_dis_1 ?? ""
                                                }"
                                                data-why_label_2="${
                                                    value.why_label_2 ?? ""
                                                }"
                                                data-why_dis_2="${
                                                    value.why_dis_2 ?? ""
                                                }"
                                                data-why_label_3="${
                                                    value.why_label_3 ?? ""
                                                }"
                                                data-why_dis_3="${
                                                    value.why_dis_3 ?? ""
                                                }"
                                                data-label_bike_experience_1="${
                                                    value.label_bike_experience_1 ??
                                                    ""
                                                }"
                                                data-label_boat_benefits_1="${
                                                    value.label_boat_benefits_1 ??
                                                    ""
                                                }"
                                                data-description_boat_benefits_1="${
                                                    value.description_boat_benefits_1 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_1="${
                                                    value.thumbnail_image_boat_benefits_1
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_1}`
                                                        : ""
                                                }"
                                                data-thumbnail_image_bike_exclusive="${
                                                    value.thumbnail_image_bike_exclusive
                                                        ? `/storage/${value.thumbnail_image_bike_exclusive}`
                                                        : ""
                                                }"
                                                  data-label_boat_benefits_2="${
                                                      value.label_boat_benefits_2 ??
                                                      ""
                                                  }"
                                                data-description_boat_benefits_2="${
                                                    value.description_boat_benefits_2 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_2="${
                                                    value.thumbnail_image_boat_benefits_2
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_2}`
                                                        : ""
                                                }"

                                                 data-label_boat_benefits_3="${
                                                     value.label_boat_benefits_3 ??
                                                     ""
                                                 }"
                                                data-description_boat_benefits_3="${
                                                    value.description_boat_benefits_3 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_3="${
                                                    value.thumbnail_image_boat_benefits_3
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_3}`
                                                        : ""
                                                }"

                                                 data-label_boat_benefits_4="${
                                                     value.label_boat_benefits_4 ??
                                                     ""
                                                 }"
                                                data-description_boat_benefits_4="${
                                                    value.description_boat_benefits_4 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_4="${
                                                    value.thumbnail_image_boat_benefits_4
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_4}`
                                                        : ""
                                                }"

                                                    data-label_boat_benefits_5="${
                                                        value.label_boat_benefits_5 ??
                                                        ""
                                                    }"
                                                data-description_boat_benefits_5="${
                                                    value.description_boat_benefits_5 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_5="${
                                                    value.thumbnail_image_boat_benefits_5
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_5}`
                                                        : ""
                                                }"

                                                    data-label_boat_benefits_6="${
                                                        value.label_boat_benefits_6 ??
                                                        ""
                                                    }"
                                                data-description_boat_benefits_6="${
                                                    value.description_boat_benefits_6 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_benefits_6="${
                                                    value.thumbnail_image_boat_benefits_6
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_6}`
                                                        : ""
                                                }"

                                                 data-label_boat_experience_1="${
                                                     value.label_boat_experience_1 ??
                                                     ""
                                                 }"
                                                data-description_boat_experience_1="${
                                                    value.description_boat_experience_1 ??
                                                    ""
                                                }"
                                                data-thumbnail_image_boat_experience_1="${
                                                    value.thumbnail_image_boat_experience_1
                                                        ? `/storage/${value.thumbnail_image_boat_experience_1}`
                                                        : ""
                                                }"
                                                data-thumbnail_image_boat_experience_2="${
                                                    value.thumbnail_image_boat_experience_2
                                                        ? `/storage/${value.thumbnail_image_boat_experience_2}`
                                                        : ""
                                                }"

                                                data-thumbnail_image_boat_benefits_main="${
                                                    value.thumbnail_image_boat_benefits_main
                                                        ? `/storage/${value.thumbnail_image_boat_benefits_main}`
                                                        : ""
                                                }"

                                                data-thumbnail_image_bike_experience_1="${
                                                    value.thumbnail_image_bike_experience_1
                                                        ? `/storage/${value.thumbnail_image_bike_experience_1}`
                                                        : ""
                                                }"

                                                data-why_icon_1="${
                                                    value.why_icon_1
                                                        ? `/storage/${value.why_icon_1}`
                                                        : ""
                                                }"
                                                data-why_icon_2="${
                                                    value.why_icon_2
                                                        ? `/storage/${value.why_icon_2}`
                                                        : ""
                                                }"
                                                data-why_icon_3="${
                                                    value.why_icon_3
                                                        ? `/storage/${value.why_icon_3}`
                                                        : ""
                                                }">
                                                <i class="ti ti-pencil me-1"></i>${_l(
                                                    "admin.common.edit"
                                                )}
                                            </a>
                                        </li>`
                                                    : ""
                                            }
                                    </ul>
                                </div>
                            </td>`
                         : ""
                 }
                            </tr>`;
                    });
                } else {
                    tableBody += `
                            <tr>
                                <td colspan="6" class="text-center">${_l(
                                    "admin.common.empty_table"
                                )}</td></td>
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
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(
                                ".dataTables_wrapper .dataTables_paginate"
                            ).addClass("d-none");

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
                            info:
                                _l("admin.common.showing") +
                                " _START_ " +
                                _l("admin.common.to") +
                                " _END_ " +
                                _l("admin.common.of") +
                                " _TOTAL_ " +
                                _l("admin.common.entries"),
                            infoEmpty:
                                _l("admin.common.showing") +
                                " 0 " +
                                _l("admin.common.to") +
                                " 0 " +
                                _l("admin.common.of") +
                                " 0 " +
                                _l("admin.common.entries"),
                            infoFiltered:
                                "(" +
                                _l("admin.common.filtered_from") +
                                " _MAX_ " +
                                _l("admin.common.total_entries") +
                                ")",
                            lengthMenu:
                                _l("admin.common.show") +
                                " _MENU_ " +
                                _l("admin.common.entries"),
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
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    $(document).on("change", "#why_icon_1", function (e) {
        validateAndPreview("why_icon_1", "preview_why_icon_1");
    });

    $(document).on("change", "#why_icon_2", function (e) {
        validateAndPreview("why_icon_2", "preview_why_icon_2");
    });

    $(document).on("change", "#why_icon_3", function (e) {
        validateAndPreview("why_icon_3", "preview_why_icon_3");
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

    function validateAndPreview(inputId, previewId) {
        const input = $("#" + inputId)[0];
        const file = input.files[0];
        const preview = $("#" + previewId);
        preview.addClass("d-none");
        preview.attr("src", "#");

        if (!file) return;

        const allowedTypes = ["image/jpeg", "image/png", "image/svg+xml"];
        if (!allowedTypes.includes(file.type)) {
            showToast("error", "Only JPG, PNG, or SVG files are allowed.");
            input.value = "";
            return;
        }

        const objectURL = URL.createObjectURL(file);

        // SVG preview without dimension check
        if (file.type === "image/svg+xml") {
            preview.attr("src", objectURL).removeClass("d-none");
            return;
        }

        const img = new Image();
        img.onload = function () {
            if (this.width !== 40 || this.height !== 40) {
                showToast("error", "Image must be exactly 40x40 pixels.");
                input.value = "";
                preview.addClass("d-none");
            } else {
                preview.attr("src", objectURL).removeClass("d-none");
            }
        };
        img.onerror = function () {
            showToast("error", "Invalid image file.");
            input.value = "";
            preview.addClass("d-none");
        };

        img.src = objectURL;
    }
    function previewThumbnailThree(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#thumbnail_preview_four")
                    .attr("src", e.target.result)
                    .show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
})();

function previewMultipleThumbnails(input) {
    let container = $("#thumbnail_preview_boat_container");
    container.empty();

    if (input.files) {
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                container.append(
                    `<img src="${e.target.result}" class="img-preview-thumb" style="width: 100px; height: auto;">`
                );
            };
            reader.readAsDataURL(file);
        });
    }
}
