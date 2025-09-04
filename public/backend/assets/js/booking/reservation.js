(async function () {
    "use strict";
    await loadTranslationFile('admin', 'common, bookings');
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        getLocations('', function(response) {
            appendLocationData('#pickUpLocationList .custom-scroll', 'pickup_location_checkbox', response);
            appendLocationData('#dropOffLocationList .custom-scroll', 'drop_location_checkbox', response);
        });
        bookingList();
        initEvents();
    });

    function getLocations(search = '', callback) {
        $.ajax({
            url: "/get-locations",
            type: "POST",
            data: {
                search: search,
                order_by: 'asc',
            },
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(error) {
                if (error.responseJSON?.code === 500) {
                    showToast('error', error.responseJSON.message);
                } else {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                }
            }
        });
    }

    function appendLocationData(wrapperSelector, checkboxClass, data) {
        const wrapper = $(wrapperSelector);
        wrapper.empty();

        if (data?.code === 200 && data.data.length > 0) {
            $.each(data.data, function(index, value) {
                wrapper.append(`
                    <li>
                        <label class="dropdown-item d-flex align-items-center rounded-1">
                            <input class="form-check-input m-0 me-2 ${checkboxClass}" type="checkbox" value="${value.id}">${value.name}
                        </label>
                    </li>
                `);
            });
        } else {
            wrapper.append(`<li class="text-center p-2">${_l('admin.common.no_data_found')}</li>`);
        }
    }

    function initEvents() {
        $(document).on('keyup', '#pickup_location_search', function () {
            let search = $(this).val().trim();
            getLocations(search, function(response) {
                appendLocationData('#pickUpLocationList .custom-scroll', 'pickup_location_checkbox', response);
            });
        });
        
        $(document).on('keyup', '#drop_off_location_search', function () {
            let search = $(this).val().trim();
            getLocations(search, function(response) {
                appendLocationData('#dropOffLocationList .custom-scroll', 'drop_location_checkbox', response);
            });
        });
        
        $(document).on('click', '#apply_filter', function () {
            let pickup_location_ids = [];
            let drop_location_ids = [];
            let status = [];
        
            $('.pickup_location_checkbox:checked').each(function() {
                pickup_location_ids.push($(this).val());
            });
        
            $('.drop_location_checkbox:checked').each(function() {
                drop_location_ids.push($(this).val());
            });
        
            $('.status_checkbox:checked').each(function() {
                status.push($(this).val());
            });
        
            $('#reservationTable').DataTable().ajax.reload();
        
        });
        
        $(document).on('click', '#reset_filter', function () {
            $('#pickUpLocationList input:checkbox').prop('checked', false);
            $('#dropOffLocationList input:checkbox').prop('checked', false);
            $('#statusList input:checkbox').prop('checked', false);
            $('#sort_by_date').val('').trigger('change');
            $('#sort_by_input').val('');
            $('#reservationTable').DataTable().ajax.reload();
        });
        
        $('#overall_search').on('keyup', function(e) {
            $('#reservationTable').DataTable().ajax.reload();
        });
        
        $(document).on('click', '.sort_by_list .dropdown-item', function () {
            let sortBy = $(this).data('sort');
            $('#sort_by_input').val(sortBy);
            $('#current_sort').text(
                sortBy.charAt(0).toUpperCase() + sortBy.slice(1).toLowerCase()
            );
            $('.sort_by_list .dropdown-item').removeClass('active');
            $(this).addClass('active');
            $('#reservationTable').DataTable().ajax.reload();
        });
        
        $('#sort_by_date').val('');

        $('#sort_by_date').on('change', function() {
            let sort_by_date = $(this).val();
            bookingList(sort_by_date);
        });

        $(document).on('click', '.dataTables_paginate a', function() {
            $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
        });

        $("#reservation_delete_form").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/delete-reservation",
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
                        $('#reservationTable').DataTable().ajax.reload();
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

        $("#reservation_complete_form").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:"/admin/complete-reservation",
                type:"POST",
                data: {
                    id: $('#compelete_id').val()
                },
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.code === 200){
                        showToast('success', response.message);
                        $("#complete_modal").modal('hide');
                        $('#reservationTable').DataTable().ajax.reload();
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

        $(document).on('click', '.deleteReservation', function() {
            let id = $(this).data('id');
            $("#delete_id").val(id);
        });

        $(document).on('click', '.completeReservation', function() {
            let id = $(this).data('id');
            $("#compelete_id").val(id);
        });
    }

    function bookingList(sort_by_date = '') {
        $('#reservationTable').DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/reservation-list",
                type: "POST",
                data: function(d) {
                    d.pickup_location_ids = $('.pickup_location_checkbox:checked').map(function() { return $(this).val(); }).get();
                    d.drop_location_ids = $('.drop_location_checkbox:checked').map(function() { return $(this).val(); }).get();
                    d.status = $('.status_checkbox:checked').map(function() { return $(this).val(); }).get();
                    d.search = $('#overall_search').val();
                    d.sort_by_date = sort_by_date;
                    d.sort_by = $('#sort_by_input').val();
                    d._token = $('meta[name="csrf-token"]').attr('content');
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $('.real-table, .table-footer').addClass('d-none');
                },
                complete: function() {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $('.real-table, .real-label, .real-input').removeClass('d-none');
                    if($('#reservationTable').DataTable().rows().count() == 0){
                        $(".table-footer").addClass('d-none');
                    } else {
                        $(".table-footer").removeClass('d-none');
                    }
                },
            },
            columns: [
                { data: 'reservation_id', render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                            <div class="avatar me-2 flex-shrink-0"><img src="${row.vehicle_image}" class="admin-vehicle-image" alt="${_l('admin.common.image')}"></div>
                            <div>
                                <a href="/admin/reservation-details/${row.encrypted_id}" class="text-info d-block mb-1">#${row.reservation_id}</a>
                                <h6 class="fs-14 text-black">${row.vehicle_name}</h6>
                            </div>
                        </div>
                    `;
                }},
                { data: 'user_name', render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                                <div class="avatar avatar-rounded me-2 flex-shrink-0">
                                    <img src="${row.customer_image}" alt="${_l('admin.common.image')}">
                                </div>
                                <div>
                                    <h6 class="mb-1 fs-14 text-black">${row.customer_full_name ? row.customer_full_name : ''}</h6>
                                    <span class="badge bg-secondary-transparent rounded-pill">${_l('admin.common.client')}</span>
                                </div>
                            </div>
                    `;
                }},
                { data: 'start_datetime', render: function(data, type, row) {
                    let dateObj = new Date(data);
            
                    let day = String(dateObj.getDate()).padStart(2, '0');
                    let month = dateObj.toLocaleString('en-us', { month: 'short' });
                    let year = dateObj.getFullYear();
                    let time = dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            
                    return `
                        <div class="d-flex align-items-center">
                            <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                <h5 class="mb-2 fs-16">${day}</h5>
                                <span class="fw-medium fs-12 bg-light p-1 rounded-1 d-inline-block text-gray-9">${month}, ${year}</span>
                            </div>
                            <div>
                                <p class="text-gray-9 mb-0">${row.pickup_location}</p>
                                <span class="fs-13">${time}</span>
                            </div>
                        </div>
                    `;
                }},
                { data: 'end_datetime', render: function(data, type, row) {
                    let dateObj = new Date(data);
            
                    let day = String(dateObj.getDate()).padStart(2, '0');
                    let month = dateObj.toLocaleString('en-us', { month: 'short' });
                    let year = dateObj.getFullYear();
                    let time = dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    return `
                        <div class="d-flex align-items-center">
                            <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                <h5 class="mb-2 fs-16">${day}</h5>
                                <span class="fw-medium fs-12 bg-light p-1 rounded-1 d-inline-block text-gray-9">${month}, ${year}</span>
                            </div>
                            <div>
                                <p class="text-gray-9 mb-0">${row.drop_location}</p>
                                <span class="fs-13">${time}</span>
                            </div>
                        </div>
                    `;
                }},
                { data: 'booking_status_text', render: function(data, type, row) {
                    let booking_cls = 'bg-success-transparent';
                    if (row.booking_status == 1) {
                        booking_cls = 'bg-violet-transparent';
                    } else if (row.booking_status == 2) {
                        booking_cls = 'bg-orange-transparent';
                    } else if (row.booking_status == 3) {
                        booking_cls = 'bg-danger-transparent';
                    } else if (row.booking_status == 4) {
                        booking_cls = 'bg-violet-transparent';
                    } else if (row.booking_status == 5) {
                        booking_cls = 'bg-success-transparent';
                    } else if (row.booking_status == 6) {
                        booking_cls = 'bg-danger-transparent';
                    } 
                    return `
                        <span class="badge ${booking_cls} d-inline-flex align-items-center badge-sm">
                            <i class="ti ti-point-filled me-1"></i>${row.booking_status_text}
                        </span>
                    `;
                }},
                { data: 'id', orderable: false, searchable: false, render: function(data, type, row) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-2">
                                <li>
                                    <a class="dropdown-item rounded-1" href="/admin/reservation-details/${row.encrypted_id}"><i class="ti ti-eye me-1"></i>${_l('admin.common.view_details')}</a>
                                </li>
                                ${(row.booking_status != 6 && row.booking_by != 'user') && hasPermission(permissions, 'reservations', 'edit') ?
                                `<li>
                                    <a class="dropdown-item rounded-1" href="/admin/edit-reservation/${row.encrypted_id}"><i class="ti ti-edit me-1"></i>${_l('admin.common.edit')}</a>
                                </li>` : ''
                                }
                                ${hasPermission(permissions, 'reservations', 'delete') ?
                                `<li>
                                    <button type="button" class="dropdown-item rounded-1 deleteReservation" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash me-1"></i>${_l('admin.common.delete')}</button>
                                </li>`:''}
                                ${(hasPermission(permissions, 'reservations', 'edit') && (row.booking_status == 1 || row.booking_status == 2 || row.booking_status == 4)) ?
                                `<li>
                                    <button type="button" class="dropdown-item rounded-1 completeReservation" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#complete_modal"><i class="ti ti-check me-1"></i>${_l('admin.common.booking_complete')}</button>
                                </li>`:''}
                            </ul>
                        </div>`;
                    },
                    visible: hasPermission(permissions, 'reservations', 'edit') || hasPermission(permissions, 'reservations', 'view') || hasPermission(permissions, 'reservations', 'delete')
                }
            ],
            order: [[0, 'desc']],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
            drawCallback: function () {
                customizeTableFooter($(this));
            },
            language: getDataTableLanguage(),
        });
    }   

})();