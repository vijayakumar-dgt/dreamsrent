/* global $, loadTranslationFile, document, showToast, _l */
(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    $(function () {
        // Helper
        const getCsrf = () => $('meta[name="csrf-token"]').attr('content');
        const getActiveDateFilter = () => $(".datefilter.active").data("id");
        const getSortBy = () => $(".sort-filter.active").data('id') || '';

        // Fetch Reviews
        function fetchReviews(sort_by = getSortBy()) {
            $("#reviewsTable").DataTable({
                serverSide: true,
                destroy: true,
                processing: false,
                ajax: {
                    url: "/user/reviews-list",
                    type: "POST",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrf()
                    },
                    data: d => {
                        const filter = getActiveDateFilter();
                        d.duration = filter;
                        d.custom_from_date = filter === "custom" ? $("#custom_from_date").val() : '';
                        d.custom_to_date = filter === "custom" ? $("#custom_to_date").val() : '';
                        d.sort_by = sort_by;
                    },
                    dataSrc: json => {
                        $('#totalReviewsCount').text(json.recordsTotal);
                        return json.data;
                    },
                    beforeSend: () => {
                        $(".table-loader").show();
                        $(".real-table, .table-footer").addClass("d-none");
                    },
                    complete: () => {
                        $(".table-loader, .input-loader, .label-loader").hide();
                        $(".real-table, .real-label, .real-input").removeClass("d-none");
                        $(".table-footer").toggleClass("d-none", !$("#reviewsTable").DataTable().rows().count());
                    },
                },
                columns: [
                    {
                        data: "vehicle_name",
                        render: (data, type, row) => {
                            return `
                                <div class="table-avatar">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_review" class="avatar flex-shrink-0">
                                        <img class="avatar-img" src="${row.vehicle_image}" alt="Booking">
                                    </a>
                                    <div class="table-head-name flex-grow-1">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_review">${ucfirst(row.vehicle_name)}</a>
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: "comments",
                        render: (data, type, row) => `<p>${row.comments.length > 100 ? `${row.comments.substring(0, 100)}...` : row.comments}</p>`
                    },
                    {
                        data: "average_ratings",
                        render: (data, type, row) => {
                            const rating = parseFloat(row.average_ratings);
                            const starsHtml = Array.from({ length: 5 }, (_, i) =>
                                rating >= i + 1 ? '<i class="fas fa-star filled"></i>' :
                                rating >= i + 0.5 ? '<i class="fas fa-star-half-stroke filled"></i>' :
                                '<i class="far fa-star"></i>'
                            ).join('');
                            return `<div class="review-rating">${starsHtml}<span>(${row.average_ratings})</span></div>`;
                        }
                    },
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => `
                            <div class="dropdown dropdown-action">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <button type="button" class="dropdown-item view_review" data-review="${row.comments}" data-bs-toggle="modal" data-bs-target="#view_review">
                                        <i class="feather-eye"></i> ${_l('web.user.view')}
                                    </button>
                                    <button type="button" class="dropdown-item delete_review" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                        <i class="feather-trash-2"></i> ${_l('web.common.delete')}
                                    </button>
                                </div>
                            </div>`
                    }
                ],
                ordering: true,
                searching: false,
                pageLength: 10,
                lengthChange: false,
                responsive: false,
                autoWidth: false,
                language: {
                    emptyTable: _l("web.common.empty_table"),
                    info: `${_l("web.common.showing")} _START_ ${_l("web.common.to")} _END_ ${_l("web.common.of")} _TOTAL_ ${_l("web.common.entries")}`,
                    infoEmpty: `${_l("web.common.showing")} 0 ${_l("web.common.to")} 0 ${_l("web.common.of")} 0 ${_l("web.common.entries")}`,
                    infoFiltered: `(${_l("web.common.filtered_from")} _MAX_ ${_l("web.common.total_entries")})`,
                    lengthMenu: `${_l("web.common.show")} _MENU_ ${_l("web.common.entries")}`,
                    search: `${_l("web.common.search")}:`,
                    zeroRecords: _l("web.common.no_matching_records"),
                    paginate: {
                        first: _l("web.common.first"),
                        last: _l("web.common.last"),
                        next: _l("web.common.next"),
                        previous: _l("web.common.prev"),
                    },
                },
            });
        }

        // Initial fetch
        fetchReviews();

        // Delete Review
        $("#reviewDeleteForm").on('submit', async e => {
            e.preventDefault();
            try {
                const response = await $.ajax({
                    url: "/review/delete",
                    type: "POST",
                    data: { id: $('#delete_id').val() },
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrf()
                    }
                });
                if (response.code === 200) {
                    showToast('success', response.message);
                    $("#delete_modal").modal('hide');
                    $("#reviewsTable").DataTable().ajax.reload();
                }
            } catch (res) {
                const errorMessage = res.responseJSON?.code === 500 ? res.responseJSON.message : _l('web.common.default_delete_error');
                showToast('error', errorMessage);
            }
        });

        // Set delete id
        $(document).on('click', '.delete_review', function () {
            $("#delete_id").val($(this).data('id'));
        });

        // View review
        $(document).on('click', '.view_review', function () {
            $('#review_text').text($(this).data('review'));
        });

        // Custom filter
        $(document).on('click', '#apply-custom-filter', () => {
            const from = $("#custom_from_date").val(), to = $("#custom_to_date").val();
            if (!from || !to) {
                $("#custom_date_error").text(_l('web.common.enter_from_to_date'));
                return;
            }
            if (new Date(to) < new Date(from)) {
                $("#custom_date_error").text(_l('web.common.to_date_must_greater'));
                return;
            }
            $("#custom_date_error").text("");
            $("#custom_date").modal("hide");
            fetchReviews();
        });

        // Date filter
        $(document).on('click', '.datefilter', function () {
            $(".datefilter").removeClass("active");
            $(this).addClass("active");
            $(".datefilter_text").text($(this).text().trim());
            if ($(this).data('id') !== "custom") {
                $("#custom_from_date, #custom_to_date").val("");
                $('#reviewsTable').DataTable().ajax.reload();
            }
        });

        // Sort filter
        $(document).on('click', '.sort-filter', function () {
            $(".sort-filter").removeClass("active");
            $(this).addClass("active");
            $(".sortfilter_text").text($(this).text().trim());
            fetchReviews($(this).data('id'));
        });
    });
})();
