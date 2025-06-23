/* global loadTranslationFile, document, showToast, _l, fetch, DOMPurify */
(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');

    const listViewCar = document.querySelector(".listview-car");
    const dataLoader = document.querySelector(".data-loader");
    const realTable = document.querySelector(".real-table");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const fetchHeaders = {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken
    };

    const labels = {
        emptyTable: _l('web.common.empty_table'),
        rentNow: _l('web.user.rent_now'),
        removeFromWishlist: _l('web.user.remove_from_wishlist'),
        category: _l('web.common.category'),
        persons: _l('web.user.persons')
    };

    const postJSON = async (url, body = {}) => {
        const response = await fetch(url, {
            method: "POST",
            headers: fetchHeaders,
            body: JSON.stringify(body)
        });
        return response.json();
    };

    const showLoader = (show) => {
        dataLoader.style.display = show ? "block" : "none";
        realTable.classList.toggle('d-none', show);
    };

    const createRatingStars = (rating) => {
        const stars = Array.from({ length: 5 }, (_, i) =>
            `<i class="fas fa-star${i < Math.round(rating) ? ' filled' : ''}"></i>`
        ).join('');
        return `${stars}<span>(${rating})</span>`;
    };

    const createDetailsList = ({ transmission, mileage, fuel_type, year, passenger_capacity }) => {
        const details = [
            { icon: "car-parts-05.svg", value: transmission || "" },
            { icon: "car-parts-02.svg", value: mileage ? `${Math.round(mileage)} KM` : "" },
            { icon: "car-parts-03.svg", value: fuel_type || "" },
            { icon: "car-parts-05.svg", value: year || "" },
            { icon: "car-parts-06.svg", value: `${passenger_capacity || 0} ${labels.persons}` }
        ];

        return details.map(({ icon, value }) => `
            <li>
                <span><img src="/frontend/assets/img/icons/${icon}" alt="${value}"></span>
                <p>${value}</p>
            </li>
        `).join('');
    };

    const createWishlistCard = (wishlist) => `
        <div class="card">
            <div class="blog-widget d-flex">
                <div class="blog-img">
                    <a href="/vehicle-details/${wishlist.slug}">
                        <img src="${wishlist.vehicle_image}" class="img-fluid" alt="${wishlist.name}">
                    </a>
                    <a href="javascript:void(0)" class="fav-icon selected wishlist-icon" data-id="${wishlist.vehicle_id}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="${labels.removeFromWishlist}">
                        <i class="feather-heart"></i>
                    </a>
                </div>
                <div class="bloglist-content w-100">
                    <div class="card-body">
                        <div class="blog-list-head d-flex">
                            <div class="blog-list-title">
                                <h3><a href="/vehicle-details/${wishlist.slug}">${wishlist.name}</a></h3>
                                <h6>${labels.category} : <span>${wishlist.brand || ""}</span></h6>
                            </div>
                            <div class="blog-list-rate">
                                <div class="list-rating">${createRatingStars(wishlist.rating)}</div>
                                <h6>${wishlist.currency}${wishlist.filtered_price.value} <span>/ ${wishlist.filtered_price.type}</span></h6>
                            </div>
                        </div>
                        <div class="listing-details-group">
                            <ul>${createDetailsList(wishlist)}</ul>
                        </div>
                        <div class="blog-list-head list-head-bottom d-flex">
                            <div class="blog-list-title">
                                <div class="title-bottom">
                                    <div class="address-info">
                                        <h6><i class="feather-map-pin me-2"></i>${wishlist.location || ""}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-button">
                                <a href="/vehicle-details/${wishlist.slug}" class="btn btn-order">
                                    <span><i class="feather-calendar me-2"></i></span>${labels.rentNow}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    const fetchWishlists = async () => {
        try {
            showLoader(true);
            const { code, data = [] } = await postJSON("/user/ajax-wishlists");
            listViewCar.innerHTML = (code === 200 && data.length)
                ? (typeof DOMPurify !== 'undefined' ? DOMPurify.sanitize(data.map(createWishlistCard).join('')) : data.map(createWishlistCard).join(''))
                : `<p class="text-center">${labels.emptyTable}</p>`;
        } catch (error) {
            showToast("error", `Error fetching wishlists: ${error.message || error}`);
        } finally {
            showLoader(false);
        }
    };

    let isProcessingWishlist = false;
    document.addEventListener('click', async (event) => {
        const icon = event.target.closest('.wishlist-icon');
        if (!icon || isProcessingWishlist) return;

        isProcessingWishlist = true;
        try {
            const { status, message } = await postJSON("/user/add-to-wishlist", { id: icon.dataset.id });
            showToast(status === 'success' ? 'success' : 'error', message);
            if (status === 'success') await fetchWishlists();
        } catch (error) {
            showToast("error", `Error updating wishlist: ${error.message || error}`);

        } finally {
            isProcessingWishlist = false;
        }
    });

    await fetchWishlists();
})();
