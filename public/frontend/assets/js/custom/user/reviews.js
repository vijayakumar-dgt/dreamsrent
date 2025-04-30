(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');
    fetchReviews();
})();

const fetchReviews = (sort_by = '') => {
    $("#reviewsTable").DataTable({
        serverSide: true,
        destroy: true,
        processing: false,
        ajax: {
            url: "/user/reviews-list",
            type: "POST",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: d => {
                d.duration = $(".datefilter.active").data('id');
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
                $(".table-footer").toggleClass("d-none", $("#reviewsTable").DataTable().rows().count() === 0);
            },
        },
        columns: [
            {
                data: "vehicle_name",
                render: (data, type, row) => {
                    const delivery_type = (row.delivery_type ?? "").replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
                    return `
                        <div class="table-avatar">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#add_review" class="avatar flex-shrink-0">
                                <img class="avatar-img" src="${row.vehicle_image}" alt="Booking">
                            </a>
                            <div class="table-head-name flex-grow-1">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#add_review">${ucfirst(row.vehicle_name)}</a>
                                ${delivery_type ? `<p>${delivery_type}</p>` : ""}
                            </div>
                        </div>`;
                }
            },
            { data: "rental_type" },
            {
                data: "comments",
                render: (data, type, row) => `<p>${row.comments.length > 100 ? `${row.comments.substring(0, 100)}...` : row.comments}</p>`
            },
            {
                data: "average_ratings",
                render: (data, type, row) => {
                    const rating = parseFloat(row.average_ratings);
                    const starsHtml = Array.from({ length: 5 }, (_, i) => {
                        if (rating >= i + 1) return '<i class="fas fa-star filled"></i>';
                        if (rating >= i + 0.5) return '<i class="fas fa-star-half-stroke filled"></i>';
                        return '<i class="far fa-star"></i>';
                    }).join('');
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
                            <a class="dropdown-item d-none" href="javascript:void(0);">
                                <i class="feather-eye"></i> ${_l('web.user.view')}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="deleteReview(${row.id});" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="feather-trash-2"></i> ${_l('web.common.delete')}
                            </a>
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
};

$("#reviewDeleteForm").on('submit', async e => {
    e.preventDefault();
    try {
        const response = await $.ajax({
            url: "/review/delete",
            type: "POST",
            data: { id: $('#delete_id').val() },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

const deleteReview = id => $("#delete_id").val(id);

$(document).on('click', '.sort_by_list .dropdown-item', function () {
    const sortBy = $(this).data('sort');
    $('#sort_by_input').val(sortBy);
    $('#current_sort').text(sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase());
    $('.sort_by_list .dropdown-item').removeClass('active');
    $(this).addClass('active');
    $('#reviewsTable').DataTable().ajax.reload();
});

$(document).on('click', '.datefilter', function () {
    const selectedFilter = $(this).data('id');
    $(".datefilter").removeClass("active");
    $(this).addClass("active");
    $(".datefilter_text").text($(this).text().trim());

    if (selectedFilter !== "custom") {
        $("#custom_from_date, #custom_to_date").val("");
        $('#reviewsTable').DataTable().ajax.reload();
    }
});

$(document).on('click', '.sort-filter', function () {
    $(".sort-filter").removeClass("active");
    $(this).addClass("active");
    $(".sortfilter_text").text($(this).text().trim());
    fetchReviews($(".sort-filter.active").data('id'));
});
