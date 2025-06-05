(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');
    fetchWishlists();
        
    async function fetchWishlists() {
        try {
            showLoader(true);
            const response = await fetch("/user/ajax-wishlists", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({})
            });

            const data = await response.json();
            if (data.code === 200 && data.data.length > 0) {
                const html = data.data.map(createWishlistCard).join('');
                const cleanHTML = DOMPurify.sanitize(html);
                document.querySelector(".listview-car").innerHTML = cleanHTML;
            } else {
                document.querySelector(".listview-car").innerHTML = `<p class="text-center">${_l('web.common.empty_table')}</p>`;
            }
        } catch (error) {
            console.error("Error fetching wishlists:", error);
        } finally {
            showLoader(false);
        }
    }

    function createWishlistCard(wishlist) {
        const ratingCard = createRatingCard(wishlist.rating);
        return `
            <div class="card">
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
                                    <h6>${wishlist.currency}${wishlist.filtered_price.value} <span>/ ${wishlist.filtered_price.type}</span></h6>
                                </div>
                            </div>
                            <div class="listing-details-group">
                                <ul>
                                    ${createDetailsList(wishlist)}
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

    function createRatingCard(rating) {
        const maxStars = 5;
        const filledStars = Math.round(rating); // In case it's a float
        const unfilledStars = maxStars - filledStars;

        const filled = Array.from({ length: filledStars }, () => `<i class="fas fa-star filled"></i>`).join('');
        const unfilled = Array.from({ length: unfilledStars }, () => `<i class="fas fa-star"></i>`).join('');

        return filled + unfilled;
    }

    function createDetailsList(wishlist) {
        const details = [
            { icon: "car-parts-05.svg", value: wishlist.transmission ?? "", alt: wishlist.transmission ?? "" },
            { icon: "car-parts-02.svg", value: `${wishlist.mileage ? Math.round(wishlist.mileage) : ""} KM`, alt: `${wishlist.mileage ? Math.round(wishlist.mileage) : ""} KM` },
            { icon: "car-parts-03.svg", value: wishlist.fuel_type ?? "", alt: wishlist.fuel_type ?? "" },
            { icon: "car-parts-05.svg", value: wishlist.year ?? "", alt: wishlist.year ?? "" },
            { icon: "car-parts-06.svg", value: `${wishlist.passenger_capacity ?? 0} ${_l('web.user.persons')}`, alt: wishlist.passenger_capacity ?? 0 }
        ];

        return details.map(detail => `
            <li>
                <span><img src="/frontend/assets/img/icons/${detail.icon}" alt="${detail.alt}"></span>
                <p>${detail.value}</p>
            </li>
        `).join('');
    }

    function showLoader(show) {
        document.querySelector(".data-loader").style.display = show ? "block" : "none";
        document.querySelector(".real-table").classList.toggle('d-none', show);
    }

    document.addEventListener('click', async (event) => {
        if (event.target.closest('.wishlist-icon')) {
            const icon = event.target.closest('.wishlist-icon');
            const id = icon.dataset.id;

            try {
                const response = await fetch("/user/add-to-wishlist", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ id })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    showToast('success', data.message);
                    fetchWishlists();
                } else {
                    showToast('error', data.message);
                }
            } catch (error) {
                console.error("Error updating wishlist:", error);
            }
        }
    });

})();
