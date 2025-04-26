(async () => {
    await loadTranslationFile('web', 'user,common');

    fetchReviews();

})();

function fetchReviews(sort_by = '') {
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
            data: function (d) {
                // d.search = $("#search").val();
                d.duration = $(".datefilter.active").data('id');
                d.sort_by = sort_by;
            },
            dataSrc: function (json) {
                $('#totalReviewsCount').text(json.recordsTotal);
                return json.data;
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($("#reviewsTable").DataTable().rows().count() === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
        },
        columns: [
            { data: "vehicle_name",
                render: function(data, type, row) {
                    let delivery_type = row.delivery_type ?? "";
                    delivery_type = delivery_type.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
                    return `<div class="table-avatar">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#add_review"  class="avatar  flex-shrink-0">
                                    <img class="avatar-img" src="${row.vehicle_image}" alt="Booking">
                                </a>
                                <div class="table-head-name flex-grow-1">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_review" >${ucfirst(row.vehicle_name)}</a>
                                    ${delivery_type ? `<p>${delivery_type}</p>` : ""}
                                </div>
                            </div>`;
                }
            },
            { data: "rental_type" },
            { data: "comments",
                render: function(data, type, row) {
                    return `<p>${row.comments.length > 100 ? row.comments.substring(0, 100) + "..." : row.comments}</p>`;
                }
            },
            {
                data: "average_ratings",
                render: function(data, type, row) {
                    const rating = parseFloat(row.average_ratings);
                    let starsHtml = '<div class="review-rating">';

                    for (let i = 1; i <= 5; i++) {
                        if (rating >= i) {
                            starsHtml += '<i class="fas fa-star filled"></i>';
                        } else if (rating >= i - 0.5) {
                            starsHtml += '<i class="fas fa-star-half-stroke filled"></i>';
                        } else {
                            starsHtml += '<i class="far fa-star"></i>';
                        }
                    }

                    starsHtml += `<span>(${row.average_ratings})</span></div>`;
                    return starsHtml;
                }
            },
            { data: "id", orderable: false, searchable: false, render: function(data, type, row) {
                return `
                    <div class="dropdown dropdown-action">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item d-none" href="javascript:void(0);">
                                <i class="feather-eye"></i> ${_l('web.user.view')}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="deleteReview(${row.id});" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                <i class="feather-trash-2"></i> ${_l('web.user.delete')}
                            </a>
                        </div>
                    </div>
                `;
            }},
        ],
        ordering: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        responsive: false,
        autoWidth: false,
        language: {
            emptyTable: _l("web.common.empty_table"),
            info: _l("web.common.showing") + " _START_ " + _l("web.common.to") + " _END_ " + _l("web.common.of") + " _TOTAL_ " + _l("web.common.entries"),
            infoEmpty: _l("web.common.showing") + " 0 " + _l("web.common.to") + " 0 " + _l("web.common.of") + " 0 " + _l("web.common.entries"),
            infoFiltered: "(" + _l("web.common.filtered_from") + " _MAX_ " + _l("web.common.total_entries") + ")",
            lengthMenu: _l("web.common.show") + " _MENU_ " + _l("web.common.entries"),
            search: _l("web.common.search") + ":",
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

$("#reviewDeleteForm").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/review/delete",
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
                $("#delete_modal").modal('hide');
                $("#reviewsTable").DataTable().ajax.reload();
            }
        },
        error: function(res) {
            if(res.responseJSON.code === 500){
                showToast('error', res.responseJSON.message);
            } else {
                showToast('error', _l('web.common.default_delete_error'));
            }
        }
    });
});

function deleteReview(id){
    $("#delete_id").val(id);
}

$(document).on('click', '.sort_by_list .dropdown-item', function () {
    let sortBy = $(this).data('sort');
    $('#sort_by_input').val(sortBy);
    $('#current_sort').text(
        sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase()
    );
    $('.sort_by_list .dropdown-item').removeClass('active');
    $(this).addClass('active');
    $('#maintenanceTable').DataTable().ajax.reload();
});


$(document).on('click', '.datefilter', function () {
    let selectedFilter = $(this).data('id');

    $(".datefilter").removeClass("active");
    $(this).addClass("active");
    $(".datefilter_text").text($(this).text().trim());

    if (selectedFilter === "custom") {
        return;
    }

    $("#custom_from_date").val("");
    $("#custom_to_date").val("");
    $('#reviewsTable').DataTable().ajax.reload();

});

$(document).on('click','.sort-filter', function(){
    $(".sort-filter").removeClass("active");
    $(this).addClass("active");
    $(".sortfilter_text").text($(this).text().trim());
    fetchReviews($(".sort-filter.active").data('id'));
 });
