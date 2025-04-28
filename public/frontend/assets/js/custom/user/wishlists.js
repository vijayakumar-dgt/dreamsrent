(function($) {
    "use strict";
(async () => {
    await loadTranslationFile('web', 'user,common');
    fetchWishlists();
})();

function fetchWishlists(){
    $.ajax({
        type: "POST",
        url: "/user/ajax-wishlists",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $(".data-loader").show();
            $(".real-table").addClass('d-none');
        },
        success: function(response) {
            if(response.code === 200 && response.data.length > 0){
                let html = response.data.map(wishlist => createWishlistCard(wishlist)).join('');
                $(".listview-car").html(html);
            }else{
                $(".listview-car").html(`<p class="text-center">${_l('web.user.no_wishlist_found')}</p>`);
            }
        },
        complete: function(){
            $(".data-loader").hide();
            $(".real-table").removeClass('d-none');
        }
    });
}

function createWishlistCard(wishlist){
    let ratingCard = createRatingCard(wishlist.rating);
    return `<div class="card">
                <div class="blog-widget d-flex">
                    <div class="blog-img">
                        <a href="/vehicle-details/${wishlist.slug}">
                            <img src="${wishlist.vehicle_image}" class="img-fluid" alt="${wishlist.name}">
                        </a>
                        <a href="javascript:void(0)" class="fav-icon selected wishlist-icon" data-id="${wishlist.vehicle_id}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="${_l('web.user.remove_from_wishlist')}">
                            <i class="feather-heart"></i>
                        </a>
                    </div>
                    <div class="bloglist-content w-100">
                        <div class="card-body">
                            <div class="blog-list-head d-flex">
                                <div class="blog-list-title">
                                    <h3><a href="/vehicle-details/${wishlist.slug}">${wishlist.name}</a></h3>
                                    <h6>${_l('web.common.category')} : <span>${wishlist.brand ?? ""}</span></h6>
                                </div>
                                <div class="blog-list-rate">
                                    <div class="list-rating">
                                       ${ratingCard}
                                        <span>(${wishlist.rating})</span>
                                    </div>
                                    <h6>${wishlist.currency}${wishlist.filtered_price['value']} <span>/ ${wishlist.filtered_price['type']}</span></h6>
                                </div>
                            </div>
                            <div class="listing-details-group">
                                <ul>
                                    <li>
                                        <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${wishlist.transmission ?? ""}"></span>
                                        <p>${wishlist.transmission ?? ""}</p>
                                    </li>
                                    <li>
                                        <span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="${wishlist.mileage ? Math.round(wishlist.mileage) : ""} KM"></span>
                                        <p>${wishlist.mileage ? Math.round(wishlist.mileage) : ""} KM</p>
                                    </li>
                                    <li>
                                        <span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="${wishlist.fuel_type ?? ""}"></span>
                                        <p>${wishlist.fuel_type ?? ""}</p>
                                    </li>
                                    <li>
                                        <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${wishlist.year ?? ""}"></span>
                                        <p>${wishlist.year ?? ""}</p>
                                    </li>
                                    <li>
                                        <span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="${wishlist.passenger_capacity ?? 0}"></span>
                                        <p>${wishlist.passenger_capacity ?? 0} ${_l('web.user.persons')}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="blog-list-head list-head-bottom d-flex">
                                <div class="blog-list-title">
                                    <div class="title-bottom">
                                        <div class="address-info">
                                            <h6><i class="feather-map-pin me-2"></i>${wishlist.location ?? ""}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="/vehicle-details/${wishlist.slug}" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>${_l('web.user.rent_now')}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
}

function createRatingCard(rating){
    let html = '';
    for(let i = 0; i < rating; i++){
        html += `<i class="fas fa-star filled"></i>`;
    }

    return html;
}

$(document).on('click','.wishlist-icon', function(){
    let id = $(this).data('id');
    $.ajax({
       type: "POST",
       url: "/user/add-to-wishlist",
       data: {
           id: id,
           _token: $('meta[name="csrf-token"]').attr('content')
       },
       dataType: "json",
       success: function (response) {
           if(response.status == 'success'){
               showToast('success', response.message);
               fetchWishlists();
           }else{
               showToast('error', response.message);
           }
       },
       error: function (error) {
           console.log(error);
       }
    });
});
})(jQuery);

