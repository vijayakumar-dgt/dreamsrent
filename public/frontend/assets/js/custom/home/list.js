(async () => {
    "use strict";
    await loadTranslationFile('web', 'home,common');
    let rtl = $('body').data('dir');
    let pl;
    let dl;
    let viewType = "grid";
    let page = 1;
    let pageLength = 12;
    let keyword = '';
    let availability = 1;
    let vehicle_brand_id = [];
    let vehicle_type_id = [];
    let year = [];
    let fuel_type_id = [];
    let color_id = [];
    let transmission_id = [];
    let vehicle_capacity = [];
    let feature_id = [];
    let from_price;
    let to_price;
    let mileage;
    let ratings;
    let sort_by;
    let rental_type;
    //check if any vehicletype checked or not by default
    $(".vehicle_types").each(function () {
        if ($(this).is(":checked")) {
            vehicle_type_id.push($(this).val());
        }
    })
    $(document).on('change','#sortBy', function(){
        fetchVehicles(); 
    });
    fetchVehicles();
    function fetchVehicles() {
        pl = $("#pickup-suggestions li.selected").data("id") || '';
        dl = $("#drop-suggestions li.selected").data("id") || '';
        let pickuplocation = $("#pickuplocation").val();
        mileage = $("input[name=mileage]:checked").val();
        ratings = $("input[name=rating]:checked").map(function () {
            return $(this).val();
        }).get();

        let pickupdate = $("#pickupdate").val();
        let pickuptime = $("#pickuptime").val()+":00" || "00:00:00"; // Default to midnight if empty
        let returndate = $("#returndate").val();
        let returntime = $("#returntime").val()+":59" || "23:59:59"; // Default to end of the day if empty

        pickupdate = formatDate(pickupdate);
        returndate = formatDate(returndate);
        let pickupdatetime = pickupdate + " " + pickuptime;
        let returndatetime = returndate + " " + returntime;
        if(viewType === "grid"){
            $(".grid_loader_div").removeClass('d-none');
        }else{
            $(".list_loader_div").removeClass('d-none');
        }
        $(".listCardDiv").addClass('d-none');
        sort_by = $("#sortBy").val();
        rental_type = $("input[name=rental_type]:checked").val();
        $.ajax({
            type: "GET",
            url: "/vehicle-list-api",
            dataType: "json",
            data: {
                page: page,
                paginate: pageLength,
                name: keyword,
                status: availability,
                vehicle_brand_id: vehicle_brand_id,
                vehicle_type_id: vehicle_type_id,
                year: year,
                fuel_type_id: fuel_type_id,
                color_id: color_id,
                transmission_id: transmission_id,
                vehicle_capacity: vehicle_capacity,
                feature_id: feature_id,
                from_price: from_price,
                to_price: to_price,
                location: pickuplocation,
                pickup_datetime : pickupdatetime,
                return_datetime : returndatetime,
                mileage : mileage,
                rating : ratings,
                sort_by : sort_by,
                rent_type : rental_type
            },
            success: function (response) {
                $(".listCardDiv").not(".listCardDiv").removeClass();
                    $(".vehicleListCard").empty();
                    if(viewType === "grid"){
                        $(".listCardDiv").addClass('col-lg-9');
                        $(".listCardDiv .row").addClass('vehicleListCard');
                    }else{
                        $(".listCardDiv").addClass('col-xl-9 col-lg-8 col-sm-12 col-12');
                        $(".listCardDiv .row").addClass('vehicleListCard');
                    }
                if (response.code === 200 && response.data && response.data.length > 0) {
                    if(viewType === "grid"){
                        var html = response.data.map(vehicle => createVehicleGridCard(vehicle)).join('');
                    }else{
                        var html = response.data.map(vehicle => createVehicleListCard(vehicle)).join('');
                    }
                    html += renderPagination(response.pagination);
                    $(".vehicleListCard").html(html);
                    let total_vehicles = _l('web.common.showing') + " " + response.pagination.from + " - " + response.pagination.to + " " + _l('web.common.of') + " " + response.pagination.total + " " + _l('web.common.vehicles');
                    $("#total_vehicles").text(total_vehicles);
                    //initialize owl carousel after 150ms,wait for images to load
                    setTimeout(function () {
                        reInitializeCarousel(".img-slider");
                    }, 150);
                } else {
                    $(".vehicleListCard").html(`<p class="text-center">${_l('web.common.no_vehicles_found')}</p>`);
                }
            },
            complete: function(){
                setTimeout(function(){
                    $(".grid_loader_div").addClass('d-none');
                    $(".list_loader_div").addClass('d-none');
                    $(".listCardDiv").removeClass('d-none');
                },500);
            }
        });
    }

    function formatDate(date) {
        if (!date) return "";
        return moment(date, "DD-MM-YYYY").format("YYYY-MM-DD");
    }
    $(document).on('click','.viewType', function(){
        viewType = $(this).data('view');
        $("#gridView, #listView").removeClass('active');
        $(this).addClass('active');
        fetchVehicles();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        page = $(this).data('page');
        if (page) {
            fetchVehicles();
        }
    });

    $(document).on('click','#filterbtn', function(){
          fetchVehicles();
    });

    $(document).on('click', '#filter', function () {
        const getCheckedValues = (selector) => $(selector + ':checked').map(function () {
            return $(this).val();
        }).get();

        vehicle_brand_id = getCheckedValues('.brands');
        availability = $('#status').is(':checked') ? 1 : 0;
        vehicle_type_id = getCheckedValues('.vehicle_types');
        year = getCheckedValues('.years');
        fuel_type_id = getCheckedValues('.fuel_types');
        color_id = getCheckedValues('.colors');
        transmission_id = getCheckedValues('.transmissions');
        vehicle_capacity = getCheckedValues('.capacity');
        feature_id = getCheckedValues('.features');
        let priceRange = $('.price-range').val();
        if(priceRange){
            priceRange = priceRange.split(';');
            if(priceRange.length === 2){
                from_price = priceRange[0];
                to_price = priceRange[1];
            }
        }
        fetchVehicles();
    });

    $(document).on('click', '.reset-filter', function () {
        vehicle_brand_id = [];
        availability = 1;
        vehicle_type_id = [];
        year = [];
        fuel_type_id = [];
        color_id = [];
        transmission_id = [];
        vehicle_capacity = [];
        feature_id = [];
        from_price = null;
        to_price = null;
        mileage = null;
        ratings = null;
        $('.brands, #status, .vehicle_types, .years, .fuel_types, .colors, .transmissions, .capacity, .features, .price-range, .mileage, .ratings').prop('checked', false);
        $("#keyword").val('');
        $("input[name='rental_type']").prop('checked', false);
        fetchVehicles();
    });

    //keyword
    $(document).on('keyup', '#keyword', function(){
        keyword = $(this).val();
        setTimeout(function(){  
            fetchVehicles();
        },500);
    });
    //Paginate
    $(document).on('change', '#pageLength', function(){
        pageLength = $(this).val();
        fetchVehicles();
    });
    function renderPagination(data) {
        let paginationHtml = `
            <nav>
                <ul class="pagination page-item justify-content-center">
                    <li class="previtem ${data.prev_page_url ? '' : 'disabled'}">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}">
                            <i class="fas fa-regular fa-arrow-left me-2"></i> ${_l('web.home.prev')}
                        </a>
                    </li>
                    <li class="justify-content-center pagination-center">
                        <div class="page-group">
                            <ul>`;

        // Generate pages dynamically
        for (let i = 1; i <= data.last_page; i++) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link ${i === data.current_page ? 'active' : ''}" href="#" data-page="${i}">
                        ${i} ${i === data.current_page ? '<span class="visually-hidden">(current)</span>' : ''}
                    </a>
                </li>`;
        }

        paginationHtml += `
                            </ul>
                        </div>
                    </li>
                    <li class="nextlink ${data.next_page_url ? '' : 'disabled'}">
                        <a class="page-link" href="#" data-page="${data.current_page + 1}">
                            ${_l('web.home.next')} <i class="fas fa-regular fa-arrow-right ms-2"></i>
                        </a>
                    </li>
                </ul>
            </nav>`;

        return paginationHtml;
    }

    function createVehicleListCard(vehicle){
        let price_type;
        let price_value;
        const currency = vehicle.currency;
        if (vehicle.price.length > 0) {
            let firstPrice = vehicle.price[0];
            [price_type, price_value] = Object.entries(firstPrice)[0];
        }
        const vehicleImages = vehicle.multiple_vehicle_images.map(img =>
              `<div class="slide-images">
                    <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">
                        <img src="${img}" class="img-fluid" alt="${ucfirst(vehicle.name ?? "")}">
                    </a>
                </div>`
        ).join('');

        const listingImage = vehicle.has_multiple_image
           ? `<div class="blog-img">
                    <div class="img-slider owl-carousel">
                        ${vehicleImages}
                    </div>
                    <div class="fav-item justify-content-end">
                        <span class="img-count"><i class="feather-image"></i>04</span>
                       ${vehicle.authenticated ? ` <a href="javascript:void(0)" class="fav-icon wishlist-icon ${vehicle.wishlist ? 'selected' : ''}" data-id="${vehicle.id}">
                            <i class="feather-heart"></i>
                        </a>` : ''}
                    </div>
                </div>`
              : `<div class="blog-img">
                    <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">
                        <img src="${vehicle.multiple_vehicle_images[0]}" class="img-fluid" alt="${ucfirst(vehicle.name ?? "")}">
                    </a>
                    <div class="fav-item justify-content-end">
                      ${vehicle.authenticated ? `<a href="javascript:void(0)" class="fav-icon wishlist-icon ${vehicle.wishlist ? 'selected' : ''}" data-id="${vehicle.id}">
                            <i class="feather-heart"></i>
                        </a>` : ''}
                    </div>
                </div>`;

        const featureList = `<ul>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${ucfirst(vehicle.transmission ?? '')}"></span>
                                    <p>${ucfirst(vehicle.transmission ?? '')}</p>
                                </li>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="${vehicle.mileage ? Math.ceil(vehicle.mileage) : 0} KM"></span>
                                    <p>${vehicle.mileage ? Math.ceil(vehicle.mileage) : 0} KM</p>
                                </li>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="${ucfirst(vehicle.fuel_type ?? '')}"></span>
                                    <p>${ucfirst(vehicle.fuel_type ?? '')}</p>
                                </li>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-04.svg" alt="Power"></span>
                                    <p>Normal</p>
                                </li>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="Persons"></span>
                                    <p>${vehicle.passenger_capacity ?? 0} Persons</p>
                                </li>
                                <li>
                                    <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${vehicle.year ?? ""}"></span>
                                    <p>${vehicle.year ?? ""}</p>
                                </li>
                            </ul>`;
        const vehicleRating = vehicle.rating ?? 0;
        const listingContent = `<div class="bloglist-content w-100">
                                    <div class="card-body">
                                        <div class="blog-list-head d-flex">
                                            <div class="blog-list-title">
                                                <h3><a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">${ucfirst(vehicle.name)}</a></h3>
                                                <h6>${_l('web.common.category')} : <span>${ucfirst(vehicle.brand ?? "")}</span></h6>
                                            </div>
                                            <div class="blog-list-rate">
                                                <div class="list-rating">
                                                    ${(() => {
                                                        const filledStars = Math.floor(vehicleRating);
                                                        const totalStars = 5;
                                                        let starsHtml = '';

                                                        for (let i = 1; i <= totalStars; i++) {
                                                            starsHtml += `<i class="fas fa-star ${i <= filledStars ? 'filled' : ''}"></i>`;
                                                        }

                                                        return starsHtml;
                                                    })()}
                                                    <span>(${vehicleRating.toFixed(1)}) ${vehicle.review_count || 0} ${_l('web.home.reviews')}</span>
                                                </div>

                                                <h6>${currency}${price_value} <span>/ ${ucfirst(price_type ?? "")}</span></h6>
                                            </div>
                                        </div>
                                        <div class="listing-details-group">
                                              ${featureList}
                                        </div>
                                        <div class="blog-list-head list-head-bottom d-flex">
                                            <div class="blog-list-title">
                                                <div class="title-bottom">
                                                    <div class="car-list-icon">
                                                        <img src="${vehicle.avatar_image ?? '/frontend/assets/img/profiles/avatar-03.jpg'}" alt="user">
                                                    </div>
                                                    <div class="address-info">
                                                        <h6><i class="feather-map-pin"></i>${ucfirst(vehicle.location ?? '')}</h6>
                                                    </div>
                                                    <div class="list-km d-none">
                                                        <span class="km-count"><img src="/frontend/assets/img/icons/map-pin.svg" alt="author">3.7m</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="listing-button">
                                                <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>${_l('web.home.rent_now')}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
            let tag = "";
            if(vehicle.is_featured){
                tag = `<div class="feature-text">
                            <span class="bg-danger">${_l('web.common.featured')}</span>
                        </div>`;
            }
            if(vehicle.is_top_rated){
                tag = `<div class="feature-text">
                        <span class="bg-warning">${_l('web.common.top_rated')}</span>
                      </div>`;
            }

            return `<div class="listview-car">
                        <div class="card">
                            <div class="blog-widget d-flex">
                                ${listingImage}
                                ${listingContent}
                                ${tag}
                            </div>
                        </div>
                    </div>`;

    }
    function createVehicleGridCard(vehicle) {
        let price_type;
        let price_value;
        let allowBooking = $("#general-settings").attr('data-allow_booking');
        let vehicleName = vehicle.name;
        vehicleName = ucfirst(vehicleName);
        const currency = vehicle.currency;
        if (vehicle.price.length > 0) {
            let firstPrice = vehicle.price[0];
            [price_type, price_value] = Object.entries(firstPrice)[0];
        }

        const vehicleImages = vehicle.multiple_vehicle_images.map(img =>
            `<div class="slide-images">
                <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">
                    <img src="${img}" class="img-fluid" alt="${vehicleName}">
                </a>
            </div>`
        ).join('');

        const listingImage = vehicle.has_multiple_image
            ? `<div class="listing-img">
                    <div class="img-slider owl-carousel">${vehicleImages}</div>
                    <div class="fav-item justify-content-end">
                        <span class="img-count"><i class="feather-image"></i>${vehicle.multiple_vehicle_images.length}</span>
                       ${vehicle.authenticated ? `<a href="javascript:void(0)" class="fav-icon wishlist-icon ${vehicle.wishlist ? 'selected' : ''}" data-id="${vehicle.id}"><i class="feather-heart"></i></a>` : ''}
                    </div>
                    <span class="featured-text">${vehicle.brand ?? "" }</span>
                </div>`
            : `<div class="listing-img">
                    <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">
                        <img src="${vehicle.multiple_vehicle_images[0]}" class="img-fluid" alt="${vehicleName}">
                    </a>
                    <div class="fav-item justify-content-end">
                      ${vehicle.authenticated ? `<a href="javascript:void(0)" class="fav-icon wishlist-icon ${vehicle.wishlist ? 'selected' : ''}" data-id="${vehicle.id}"><i class="feather-heart"></i></a>` : ''}
                    </div>
                    <span class="featured-text">${ucfirst(vehicle.brand ?? "")}</span>
                </div>`;

        const featureList = `
            <ul>
                <li><span><img src="/frontend/assets/img/icons/car-parts-01.svg" alt="${ucfirst(vehicle.transmission ?? "")}"></span><p>${ucfirst(vehicle.transmission ?? "")}</p></li>
                <li><span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="${vehicle.mileage ? Math.ceil(vehicle.mileage) : 0} miles"></span><p>${vehicle.mileage ? Math.ceil(vehicle.mileage) : 0} miles</p></li>
                <li><span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="${ucfirst(vehicle.fuel_type ?? "")}"></span><p>${ucfirst(vehicle.fuel_type ?? "")}</p></li>
            </ul>
            <ul>
                <li><span><img src="/frontend/assets/img/icons/car-parts-04.svg" alt="Power"></span><p>Power</p></li>
                <li><span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${vehicle.year ?? ""}"></span><p>${vehicle.year ?? ""}</p></li>
                <li><span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="Persons"></span><p>${vehicle.passenger_capacity ?? 0} Persons</p></li>
            </ul>`;
        const vehicleRating = vehicle.rating ?? 0;
        const listingContent = `
            <div class="listing-content">
                <div class="listing-features d-flex align-items-end justify-content-between">
                 <div class="list-rating">
                 <a href="javascript:void(0)" class="author-img">
                    <img src="${vehicle.avatar_image ?? '/frontend/assets/img/profiles/avatar-03.jpg'}" alt="author">
                </a>
                <h3 class="listing-title"><a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}">${vehicleName}</a></h3>
                    <div class="list-rating">
                        ${(() => {
                            const filledStars = Math.floor(vehicleRating);
                            const totalStars = 5;
                            let starsHtml = '';

                            for (let i = 1; i <= totalStars; i++) {
                                starsHtml += `<i class="fas fa-star ${i <= filledStars ? 'filled' : ''}"></i>`;
                            }

                            return starsHtml;
                        })()}
                        <span>(${vehicleRating.toFixed(1)}) ${vehicle.review_count || 0} ${_l('web.home.reviews')}</span>
                    </div>
                </div>
                    <div class="list-km d-none">
                        <span class="km-count"><img src="/frontend/assets/img/icons/map-pin.svg" alt="author">3.5m</span>
                    </div>
                </div>
                <div class="listing-details-group">${featureList}</div>
                <div class="listing-location-details">
                    <div class="listing-price"><span><i class="feather-map-pin"></i></span>${ucfirst(vehicle.location ?? '')}</div>
                    <div class="listing-price"><h6>${currency}${price_value} <span> / ${ucfirst(price_type)}</span></h6></div>
                </div>
                <div class="listing-button">
                    <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}" class="btn btn-order ${allowBooking != 1 ? 'disabled' : ''}">
                        <span><i class="feather-calendar me-2"></i></span>${_l('web.home.rent_now')}
                    </a>
                </div>
            </div>`;
        let tag = "";
        if(vehicle.is_featured){
            tag = `<div class="feature-text">
                        <span class="bg-danger">${_l('web.common.featured')}</span>
                    </div>`
        }
        if(vehicle.is_top_rated){
            tag = `<div class="feature-text">
                        <span class="bg-warning">${_l('web.common.top_rated')}</span>
                    </div>`
        }
        return `
            <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                <div class="listing-item">
                    ${listingImage}
                    ${listingContent}
                    ${tag}
                </div>
            </div>`;
    }

    function reInitializeCarousel(className){
        let isRtl = $('body').data('dir') && $('body').data('dir') == 'rtl' ? true : false;
        $(className).owlCarousel({
            loop:true,
            margin:24,
            nav:true,
            dots:true,
            rtl: isRtl,
            smartSpeed: 2000,
            autoplay:false,
            navText: [
                '<i class="fa-solid fa-chevron-left"></i>',
                '<i class="fa-solid fa-chevron-right"></i>'
            ],
            responsive:{
                0:{
                    items:1
                },
                550:{
                    items:1
                },
                768:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        });
    }

    if($('.price-range').length > 0) {
		$(".price-range").ionRangeSlider({
			type: "double",
			grid: true,
			min: 0,
			max:5000,
			from: 0,
			to: 500,
			prefix: "$"
		});
	}

	$('.price-range').on('input', function () {
        $('.demo span').html(this.value);
    });

    $(document).on('click', '.wishlist-icon', function () {
        let id = $(this).data('id');
        let button = $(this);

        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                if (response.status == 'success') {
                    if(button.hasClass('selected')){
                        button.removeClass('selected');
                    }else{
                        button.addClass('selected');
                    }
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

})();


$(document).ready(function () {
    let $input = $("#pickuplocation");
    let $suggestions = $("#pickup-suggestions");
    let searchTimeout;
    let cache = {};
    let initialPickupId = $("#initialPickupId").val();
    let initialPickupName = $("#initialPickupName").val();

    if (initialPickupId && initialPickupName) {
        $input.val(initialPickupName);

        $suggestions.html(`<li data-id="${initialPickupId}" class="selected">${initialPickupName}</li>`);
    }
    $input.on("keyup", function () {
        let query = $(this).val().trim().toLowerCase();

        clearTimeout(searchTimeout);

        if (query.length < 1) {
            $suggestions.hide();
            return;
        }

        if (cache[query]) {
            displaySuggestions(cache[query]);
            return;
        }

        searchTimeout = setTimeout(() => {
            $.ajax({
                url: "/search-locations",
                method: "GET",
                data: { query: query },
                success: function (response) {
                    cache[query] = response.data;
                    displaySuggestions(response.data);
                }
            });
        }, 300);
    });

    function displaySuggestions(data) {
        $suggestions.html("");
        if (data.length > 0) {
            data.forEach(location => {
                $suggestions.append(`<li data-id="${location.id}">${location.name}</li>`);
            });
        } else {
            $suggestions.append(`<li class="no-result">${_l('web.home.no_location_found')}</li>`);
        }
        $suggestions.show();
    }
    

    $(document).on("click", "#pickup-suggestions li", function () {
        $("#pickup-suggestions li").removeClass('selected');
        if(!$(this).hasClass('no-result')){
          $input.val($(this).text());
          $(this).addClass('selected');
          $suggestions.hide();
        }
        $suggestions.hide();
        
    });

    $(document).on("click", function (event) {
        if (!$(event.target).closest(".group-img").length) {
            $suggestions.hide();
        }
    });
});
