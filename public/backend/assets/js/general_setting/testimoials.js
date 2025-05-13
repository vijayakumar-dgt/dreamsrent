(async () => {
    "use strict";
    await loadTranslationFile('admin', 'cms,common');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {

        $('#search').on('keyup', function(e) {
            clearTimeout(window.searchTimer);
            window.searchTimer = setTimeout(function() {
                applyFilters();
            }, 500);
        });

        $('.dropdown-toggle .sort').text("Sort By : Latest");


        applyFilters();
        $('.search-button').on('click', function() {
            applyFilters();
        });
        $("#addTestimonials").validate({
            rules: {
                testimonial_image: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2 * 1024 * 1024
                },
                customer_name: {
                    required: true,
                    minlength: 3
                },
                customer_rating: {
                    required: true
                },
                customer_review: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                testimonial_image: {
                    required: _l('admin.common.image_required'),
                    extension: _l('admin.common.image_format'),
                    filesize: _l('admin.common.image_size')
                },
                customer_name: {
                    required: _l('admin.cms.customer_required'),
                    minlength: _l('admin.cms.customer_minlength')
                },
                customer_rating: {
                    required: _l('admin.cms.rating_required')
                },
                customer_review: {
                    required: _l('admin.cms.review_required'),
                    minlength: _l('admin.cms.review_minlength')
                }
            },
            errorPlacement: function (error, element) {
                $("#" + element.attr("id") + "_error").text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $("#" + element.id + "_error").text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/testimonials/store",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.submitbtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.submitbtn').attr('disabled', false).html(_l('admin.common.create_new'));
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            loadTestimonialsSettings();
                            $("#addTestimonials")[0].reset();
                            showToast('success', response.message);
                            $("#addTestimonials .form-control").removeClass("is-valid is-invalid");
                            $("#add_testimonial").modal("hide");
                        }
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
                            showToast('error', error.responseJSON.message);
                        }
                    }
                });
            }
        });

        $("#editTestimonialForm").validate({
            rules: {
                edit_testimonial_image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2 * 1024 * 1024
                },
                edit_testimonial_name: {
                    required: true,
                    minlength: 3
                },
                edit_testimonial_ratings: {
                    required: true
                },
                edit_testimonial_review: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                edit_testimonial_image: {
                    required: _l('admin.common.image_required'),
                    extension: _l('admin.common.image_format'),
                    filesize: _l('admin.common.image_size')
                },
                edit_testimonial_name: {
                    required: _l('admin.cms.customer_required'),
                    minlength: _l('admin.cms.customer_minlength')
                },
                edit_testimonial_ratings: {
                    required: _l('admin.cms.rating_required')
                },
                edit_testimonial_review: {
                    required: _l('admin.cms.review_required'),
                    minlength: _l('admin.cms.review_minlength')
                }
            },
            errorPlacement: function (error, element) {
                $("#" + element.attr("id") + "_error").text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $("#" + element.id + "_error").text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
                formData.append("id", $("#edit_testimonial_id").val());
                formData.append("customer_name", $("#edit_testimonial_name").val());
                formData.append("customer_rating", $("#edit_testimonial_ratings").val());
                formData.append("customer_review", $("#edit_testimonial_review").val());
                formData.append("status", $("#edit_testimonial_status").prop("checked") ? 1 : 0);

                let imageFile = $("#edit_testimonial_image")[0].files[0];
                if (imageFile) {
                    formData.append("testimonial_image", imageFile);
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/testimonials/update",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.savebtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.savebtn').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            loadTestimonialsSettings();
                            $("#editTestimonialForm")[0].reset();
                            showToast('success', response.message);
                            $("#editTestimonialForm .form-control").removeClass("is-valid is-invalid");
                            $("#edit_testimonial").modal("hide");
                        }
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
                            showToast('error', error.responseJSON.message);
                        }
                    }
                });
            }
        });

        $("#edit_testimonial_image").on("change", function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            const preview = $("#edit_testimonial_preview");

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    showToast('error', _l('admin.common.image_size'));
                    $(this).val("");
                    return;
                }

                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;

                    img.onload = function () {
                        if (img.width === 180 && img.height === 180) {
                            preview.attr("src", e.target.result).show();
                        } else {
                            showToast('error', _l('admin.cms.testimonial_image_size'));
                            $("#edit_testimonial_image").val("");
                        }
                    };
                };

                reader.readAsDataURL(file);
            }
        });


        $("#testimonial_image").on("change", function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            const preview = $("#testimonial_image_preview");

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    showToast('error', _l('admin.common.image_size'));
                    $(this).val("");
                    return;
                }

                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;

                    img.onload = function () {
                        if (img.width === 180 && img.height === 180) {
                            preview.attr("src", e.target.result).show();
                        } else {
                            showToast('error', _l('admin.cms.testimonial_image_size'));
                            $("#testimonial_image").val("");
                        }
                    };
                };

                reader.readAsDataURL(file);
            }
        });
    });
    $(document).on('click', '.sort-option', function() {
        const selectedSort = $(this).text().trim();
        $('.dropdown-toggle .sort').text(`Sort By : ${selectedSort}`);
        applyFilters();
    });

    $('.filterbox .text-purple').on('click', function() {
        applyFilters();
    });

    $('.filterbox .text-danger').on('click', function() {
        $('.filterbox input[type="checkbox"]').prop('checked', false);
        applyFilters();
    });

    function applyFilters() {
        const sortText = $('.dropdown-toggle .sort').text().replace('Sort By : ', '').trim();
        const searchText = $('#search').val().trim();
        const selectedRatings = [];

        $('.filterbox input[type="checkbox"]:checked').each(function() {
            selectedRatings.push($(this).parent().text().trim()[0]);
        });

        const filters = {
            sort: sortText,
            search: searchText,
            ratings: selectedRatings
        };

        loadTestimonialsSettings(filters);
    }
    function loadTestimonialsSettings(filters = {}) {
        $.ajax({
            url: '/admin/testimonials/list',
            type: 'GET',
            data: filters,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#testimonialsTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function(response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#testimonialsTable")) {
                    $("#testimonialsTable").DataTable().destroy();
                }

                if (response.success && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function(index, testimonial) {
                        let imageUrl = testimonial.image
                            ? ("/storage/" + testimonial.image)
                            : "/backend/assets/img/blog/blog-1.jpg";

                            let stars = "";
                            for (let i = 1; i <= 5; i++) {
                                if (i <= testimonial.ratings) {
                                    stars += `<span class="me-1"><i class="ti ti-star-filled text-warning"></i></span>`; // Filled star
                                } else {
                                    stars += `<span class="me-1"><i class="ti ti-star text-gray-400"></i></span>`; // Empty star
                                }
                            }


                        let reviewText = testimonial.review.length > 50
                            ? testimonial.review.substring(0, 50) + "..."
                            : testimonial.review;

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

                             ${hasPermission(permissions, 'testimonials', 'edit') || hasPermission(permissions, 'testimonials', 'delete') ?

                                `<td>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            ${hasPermission(permissions, 'testimonials', 'edit') ?

                                            `<li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="editTestimonial(${testimonial.id}, '${testimonial.customer_name}', '${imageUrl}', '${testimonial.review}', ${testimonial.ratings} , ${testimonial.status})">
                                                    <i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}
                                                </a>
                                            </li>`:''}
                                              ${hasPermission(permissions, 'testimonials', 'delete') ?
                                            `<li>
                                                <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="deleteTestimonial(${testimonial.id})" data-bs-toggle="modal" data-bs-target="#delete_testimonials">
                                                    <i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}
                                                </a>
                                            </li>`:''}
                                        </ul>
                                    </div>
                                </td>`:''}
                            </tr>
                        `;
                    });
                } else {
                    tableBody += `
                    <tr>
                        <td colspan="7" class="text-center">${_l('admin.common.empty_table')}</td></td>
                    </tr>`;
                    $('.table-footer').empty();
                }

                $("#testimonialsTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $('#testimonialsTable').DataTable({
                        ordering: false,
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
            error: function(xhr) {
                showToast('error', xhr.responseJSON.message);
            },
        });
    }

    $("#deleteTestimonial").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url:"/admin/testimonials/delete",
            type:"POST",
            data: {
                id: $('#delete_id').val()
            },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.code === 200){
                    showToast('success', response.message);
                    $("#delete_testimonials").modal('hide');
                    loadTestimonialsSettings();
                }
            },
            error: function(res) {
                if(res.responseJSON.code === 500){
                    showToast('error', res.responseJSON.message);
                } else {
                    showToast('error', _l('admin.common.default_delete_error'));
                }
            }
        });
    });

})();




function editTestimonial(id, customerName, image, review, ratings, status) {

    $('#edit_testimonial_id').val(id);
    $('#edit_testimonial_name').val(customerName);
    $('#edit_testimonial_review').val(review);
    $('#edit_testimonial_status').prop('checked', status === 1);
    $('#edit_testimonial_preview').attr('src', image.startsWith("http") ? image : image);


    $('#edit_testimonial_ratings').val(ratings).trigger('change');


    $('#edit_testimonial').modal('show');
}

function deleteTestimonial(id){
    $("#delete_id").val(id);
}





$.validator.addMethod("filesize", function (value, element, param) {
    return this.optional(element) || (element.files[0] && element.files[0].size <= param);
}, "File size is too large.");
let searchTimer;

function handleSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        applyFilters();
    }, 500);
}
