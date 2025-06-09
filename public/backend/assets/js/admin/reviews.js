(async () => {
    await loadTranslationFile('admin', 'common, rentals');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initEvents();
    });

    function initEvents() {
        $("#sort_by_date").val('');
        
        $(document).on('click', '.dataTables_paginate a', function() {
            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
        });
        
        $('#search').on('keyup', function() {
            $('#reviewsTable').DataTable().ajax.reload();
        });

        $(document).on('click', '.sort_by_list .dropdown-item', function () {
            let sortBy = $(this).data('sort');
            $('#sort_by_input').val(sortBy);
            $('#current_sort').text(sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase());
            $('.sort_by_list .dropdown-item').removeClass('active');
            $(this).addClass('active');
            $('#reviewsTable').DataTable().ajax.reload();
        });
        
        $('#sort_by_date').on('change', function() {
            var sort_by_date = $(this).val();
            initTable(sort_by_date);
        });

        $(document).on('click', '.delete_review', function () {
            $('#delete_id').val($(this).data('id'));
        });

        $("#reviewDeleteForm").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/review/delete",
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
                        showToast('error', _l('admin.common.default_delete_error'));
                    }
                }
            });
        });

        $(document).on('click', '.view_review', function () {
            let review = $(this).data('review');
            $('#review_text').text(review);
        });
    }

    function initTable(sortByDate = '') {
        $("#reviewsTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/reviews/list",
                type: "POST",
                data: function (d) {
                    d.search = $("#search").val();
                    d.sort_by_date = sortByDate;
                    d.sort_by = $("#sort_by_input").val();
                },
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
                    if ($("#reviewsTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                { data: "vehicle_name",
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg border">
                                        <img src="${row.vehicle_image}" class="img-fluid admin-vehicle-image" alt="${_l('admin.common.image')}">
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="fw-medium text-black">${row.vehicle_name}</h6>
                                    </div>
                                </div>`;
                    } 
                },
                { data: "customer_full_name",
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <div class="avatar me-2 flex-shrink-0">
                                        <img class="rounded-circle" src="${row.profile_image}" alt="${_l('admin.common.image')}">
                                    </div>
                                    <div>
                                        <div class="fw-semibold d-block text-black">${row.customer_full_name ?? ''}</div>
                                    </div>
                                </div>`;
                    } 
                },
                { data: "created_at",
                    render: function (data, type, row) {
                        return row.review_date;
                    },
                },
                { data: "average_ratings",
                    render: function (data, type, row) {
                        let starsHtml = "";
                        for (let i = 1; i <= 5; i++) {
                            if (i <= Math.floor(row.average_ratings)) {
                                starsHtml += '<i class="fas fa-star filled"></i>';
                            } else if (
                                i === Math.ceil(row.average_ratings) &&
                                row.average_ratings % 1 !== 0
                            ) {
                                starsHtml += '<i class="fas fa-star-half-alt filled"></i>';
                            } else {
                                starsHtml += '<i class="far fa-star"></i>';
                            }
                        }
                        return starsHtml + ` (${row.average_ratings})`;
                    },
                },
                { data: "comments",
                    render: function (data, type, row) {
                        return row.comments.length > 50
                            ? row.comments.substring(0, 50) + "..."
                            : row.comments;
                    },
                },
                { data: "id", orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-1 view_review" data-review="${row.comments}" data-bs-toggle="modal" data-bs-target="#view_review"><i class="ti ti-eye me-1"></i>${_l('admin.common.view')}</button>
                                        </li>
                                        ${ hasPermission(permissions, 'reviews', 'delete') ? 
                                            `<li>
                                                <button type="button" class="dropdown-item rounded-1 delete_review" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_review"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</button>
                                            </li>` : ''
                                        }
                                    </ul>
                                </div>`;
                    },
                    visible: hasPermission(permissions, 'reviews', 'delete') || hasPermission(permissions, 'reviews', 'view'),
                }
            ],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
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
            "drawCallback": function() {
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
        });
    }
}) ();