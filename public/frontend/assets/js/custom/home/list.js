/* global $, document, loadTranslationFile, showToast, _l, setTimeout, clearTimeout, DOMPurify, moment */

(async () => {
    "use strict";
    await loadTranslationFile("web", "home,common");
    let pl;
    let dl;
    let pd;
    let pt;
    let rd;
    let rt;
    let viewType = "grid";
    let page = 1;
    let pageLength = 12;
    let keyword = "";
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
    const _currency = $("#general-settings").attr("data-currency");

    $(".vehicle_types").each(function () {
        if ($(this).is(":checked")) {
            vehicle_type_id.push($(this).val());
        }
    });
    $(document).on("change", "#sortBy", function () {
        fetchVehicles();
    });
    setTimeout(() => {
        fetchVehicles();
    }, 200);
    function fetchVehicles() {
        const $pickupSelected = $("#pickup-suggestions li.selected");
        const $dropSelected = $("#drop-suggestions li.selected");
        const $pickupLocation = $("#pickuplocation");
        const $pickupdate = $("#pickupdate");
        const $pickuptime = $("#pickuptime");
        const $returndate = $("#returndate");
        const $returntime = $("#returntime");
        const $sortBy = $("#sortBy");
        const $gridLoaderWrapper = $("#grid-loader-wrapper");
        const $listLoaderWrapper = $("#list-loader-wrapper");

        pl = $pickupSelected.data("id") || "";
        dl = $dropSelected.data("id") || "";
        const pickuplocation = $pickupLocation.val() || "";

        mileage = $("input[name=mileage]:checked").val() || "";
        ratings = $("input[name=rating]:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        const rawPickupDate = $pickupdate.val();
        const rawPickupTime = $pickuptime.val();
        const rawReturnDate = $returndate.val();
        const rawReturnTime = $returntime.val();

        pt = rawPickupTime ? rawPickupTime + ":00" : "00:00:00";
        rt = rawReturnTime ? rawReturnTime + ":59" : "23:59:59";
        pd = rawPickupDate;
        rd = rawReturnDate;

        const pickupDatetime = `${formatDate(rawPickupDate)} ${pt}`;
        const returnDatetime = `${formatDate(rawReturnDate)} ${rt}`;
        const loaderHtml = $("#global-loader").html();

        if (viewType === "grid") {
            $gridLoaderWrapper.removeClass("d-none").html(loaderHtml);
            $listLoaderWrapper.addClass("d-none").empty();
        } else {
            $listLoaderWrapper.removeClass("d-none").html(loaderHtml);
            $gridLoaderWrapper.addClass("d-none").empty();
        }

        $(".listCardDiv").addClass("d-none");
        sort_by = $sortBy.val();
        rental_type = $("input[name=rental_type]:checked").val() || "";

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
                pickup_datetime: pickupDatetime,
                return_datetime: returnDatetime,
                mileage: mileage,
                rating: ratings,
                sort_by: sort_by,
                rent_type: rental_type,
            },
            success: function (response) {
                $(".listCardDiv")
                    .removeClass()
                    .addClass(
                        viewType === "grid"
                            ? "listCardDiv col-lg-9"
                            : "listCardDiv col-xl-9 col-lg-8 col-sm-12 col-12"
                    );

                $(".listCardDiv .row").addClass("vehicleListCard");
                const $vehicleListCard = $(".vehicleListCard");
                $vehicleListCard.empty();

                if (
                    response.code === 200 &&
                    Array.isArray(response.data) &&
                    response.data.length > 0
                ) {
                    const html =
                        response.data
                            .map((vehicle) =>
                                viewType === "grid"
                                    ? createVehicleGridCard(vehicle)
                                    : createVehicleListCard(vehicle)
                            )
                            .join("") + renderPagination(response.pagination);

                    $vehicleListCard.html(DOMPurify.sanitize(html));

                    const { from, to, total } = response.pagination;
                    const totalVehiclesText = `${_l(
                        "web.common.showing"
                    )} ${from} - ${to} ${_l("web.common.of")} ${total} ${_l(
                        "web.common.vehicles"
                    )}`;
                    $("#total_vehicles").text(totalVehiclesText);

                    setTimeout(() => {
                        reInitializeCarousel(".img-slider");
                    }, 150);
                } else {
                    $vehicleListCard.html(
                        `<p class="text-center">${_l(
                            "web.common.no_vehicles_found"
                        )}</p>`
                    );
                }
            },
            complete: function () {
                setTimeout(() => {
                    $gridLoaderWrapper.addClass("d-none");
                    $listLoaderWrapper.addClass("d-none");
                    $(".listCardDiv").removeClass("d-none");
                }, 500);
            },
        });
    }

    /**
     * Format a date string from "DD-MM-YYYY" to "YYYY-MM-DD"
     * @param {string} date - The date string to format
     * @returns {string} - Formatted date string
     */
    function formatDate(date) {
        if (!date) return "";
        return moment(date, "DD-MM-YYYY").format("YYYY-MM-DD");
    }

    // Handle view type toggle (grid/list)
    $(document).on("click", ".viewType", function () {
        viewType = $(this).data("view");
        $("#gridView, #listView").removeClass("active");
        $(this).addClass("active");
        fetchVehicles();
    });

    // Handle pagination click to fetch vehicles for selected page
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        const selectedPage = $(this).data("page");
        if (selectedPage) {
            page = selectedPage;
            fetchVehicles();
        }
    });

    // Filter button click handler to refresh vehicle listing
    $(document).on("click", "#filterbtn", () => {
        fetchVehicles();
    });

    // Apply filters to fetch filtered vehicles
    $(document).on("click", "#filter", () => {
        /**
         * Helper function to get selected checkbox values for a selector
         * @param {string} selector - CSS selector for checkboxes
         * @returns {Array} - Array of selected values
         */
        const getCheckedValues = (selector) =>
            $(`${selector}:checked`)
                .map(function () {
                    return $(this).val();
                })
                .get();

        vehicle_brand_id = getCheckedValues(".brands");
        availability = $("#status").is(":checked") ? 1 : 0;
        vehicle_type_id = getCheckedValues(".vehicle_types");
        year = getCheckedValues(".years");
        fuel_type_id = getCheckedValues(".fuel_types");
        color_id = getCheckedValues(".colors");
        transmission_id = getCheckedValues(".transmissions");
        vehicle_capacity = getCheckedValues(".capacity");
        feature_id = getCheckedValues(".features");

        const priceRangeRaw = $(".price-range").val();
        if (priceRangeRaw) {
            const [from, to] = priceRangeRaw.split(";");
            from_price = from || undefined;
            to_price = to || undefined;
        }

        fetchVehicles();
    });

    // Reset filter
    // Reset all filter values to default
    $(document).on("click", ".reset-filter", () => {
        // Clear all filter state variables
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
        keyword = "";

        // Uncheck all filter inputs
        const $allInputs = $(
            ".brands, #status, .vehicle_types, .years, .fuel_types, .colors, .transmissions, .capacity, .features, .price-range, .mileage, .ratings"
        );
        $allInputs.prop("checked", false);

        // Clear keyword and rental type selections
        $("#keyword").val("");
        $("input[name='rental_type']").prop("checked", false);

        // Fetch updated vehicle list with cleared filters
        fetchVehicles();
    });

    // Debounce the keyword filter input (wait 500ms after typing stops)
    let keywordTimeout;
    $(document).on("keyup", "#keyword", function () {
        keyword = $(this).val();

        // Clear previously set timeout
        clearTimeout(keywordTimeout);

        // Set a new timeout
        keywordTimeout = setTimeout(() => {
            fetchVehicles();
        }, 500);
    });

    // Update results when user changes the number of items per page
    $(document).on("change", "#pageLength", function () {
        const selected = $(this).val();
        pageLength = selected ? parseInt(selected, 10) : 12;
        fetchVehicles();
    });

    // Function to render pagination HTML based on the API response
    function renderPagination(data) {
        const { current_page, last_page, prev_page_url, next_page_url } = data;
        let html = `
        <nav>
            <ul class="pagination page-item justify-content-center">
                <li class="previtem ${prev_page_url ? "" : "disabled"}">
                    <a class="page-link" href="#" data-page="${
                        current_page - 1
                    }">
                        <i class="fas fa-regular fa-arrow-left me-2"></i> ${_l(
                            "web.home.prev"
                        )}
                    </a>
                </li>
                <li class="justify-content-center pagination-center">
                    <div class="page-group">
                        <ul>`;

        // Generate page number links
        for (let i = 1; i <= last_page; i++) {
            const isActive = i === current_page ? "active" : "";
            const currentText =
                i === current_page
                    ? "<span class=\"visually-hidden\">(current)</span>"
                    : "";

            html += `
            <li class="page-item">
                <a class="page-link ${isActive}" href="#" data-page="${i}">
                    ${i} ${currentText}
                </a>
            </li>`;
        }

        html += `
                        </ul>
                    </div>
                </li>
                <li class="nextlink ${next_page_url ? "" : "disabled"}">
                    <a class="page-link" href="#" data-page="${
                        current_page + 1
                    }">
                        ${_l(
                            "web.home.next"
                        )} <i class="fas fa-regular fa-arrow-right ms-2"></i>
                    </a>
                </li>
            </ul>
        </nav>`;

        return html;
    }

    /**
     * Generate HTML for a vehicle listing card
     * @param {Object} vehicle - Vehicle data object
     * @returns {string} - HTML string for the vehicle card
     */
    function createVehicleListCard(vehicle) {
        "use strict";

        let priceType, priceValue;
        const currency = vehicle.currency;

        // Extract first price entry if exists
        if (vehicle.price.length > 0) {
            const firstPrice = vehicle.price[0];
            [priceType, priceValue] = Object.entries(firstPrice)[0];
        }

        // Create HTML for multiple images
        const vehicleImages = vehicle.multiple_vehicle_images
            .map(
                (img) => `
        <div class="slide-images">
            <a href="/vehicle-details/${
                vehicle.slug
            }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">
                <img src="${img}" class="img-fluid" alt="${ucfirst(
                    vehicle.name ?? ""
                )}">
            </a>
        </div>`
            )
            .join("");

        // Construct image block (slider or single)
        const listingImage = vehicle.has_multiple_image
            ? `
            <div class="blog-img">
                <div class="img-slider owl-carousel">
                    ${vehicleImages}
                </div>
                <div class="fav-item justify-content-end">
                    <span class="img-count"><i class="feather-image"></i>04</span>
                    ${
                        vehicle.authenticated
                            ? `
                        <button type="button" class="fav-icon wishlist-icon ${
                            vehicle.wishlist ? "selected" : ""
                        }" data-id="${vehicle.id}">
                            <i class="feather-heart"></i>
                        </button>`
                            : ""
                    }
                </div>
            </div>`
            : `
            <div class="blog-img">
                <a href="/vehicle-details/${
                    vehicle.slug
                }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">
                    <img src="${
                        vehicle.multiple_vehicle_images[0]
                    }" class="img-fluid" alt="${ucfirst(vehicle.name ?? "")}">
                </a>
                <div class="fav-item justify-content-end">
                    ${
                        vehicle.authenticated
                            ? `
                        <button type="button" class="fav-icon wishlist-icon ${
                            vehicle.wishlist ? "selected" : ""
                        }" data-id="${vehicle.id}">
                            <i class="feather-heart"></i>
                        </button>`
                            : ""
                    }
                </div>
            </div>`;

        // Attribute icon depending on vehicle type
        const attrIcon =
            vehicle.category === "Car"
                ? `<li><span><img src="/frontend/assets/img/icons/door-icon.svg" alt="${
                      vehicle.num_doors ?? 0
                  }"></span><p>${vehicle.num_doors ?? 0}</p></li>`
                : `<li><span><img src="/frontend/assets/img/icons/color.svg" alt="${
                      vehicle.color ?? ""
                  }"></span><p>${vehicle.color ?? ""}</p></li>`;

        // Feature list block
        const featureList = `
        <ul>
            <li><span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${ucfirst(
                vehicle.transmission ?? ""
            )}"></span><p>${ucfirst(vehicle.transmission ?? "")}</p></li>
            <li><span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="${
                vehicle.mileage ? Math.ceil(vehicle.mileage) : 0
            } KM"></span><p>${
            vehicle.mileage ? Math.ceil(vehicle.mileage) : 0
        } KM</p></li>
            <li><span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="${ucfirst(
                vehicle.fuel_type ?? ""
            )}"></span><p>${ucfirst(vehicle.fuel_type ?? "")}</p></li>
            ${attrIcon}
            <li><span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="${_l(
                "web.home.persons"
            )}"></span><p>${vehicle.passenger_capacity ?? 0} ${_l(
            "web.home.persons"
        )}</p></li>
            <li><span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${
                vehicle.year ?? ""
            }"></span><p>${vehicle.year ?? ""}</p></li>
        </ul>`;

        const vehicleRating = vehicle.rating ?? 0;

        // Listing content block
        const listingContent = `
        <div class="bloglist-content w-100">
            <div class="card-body">
                <div class="blog-list-head d-flex">
                    <div class="blog-list-title">
                        <h3><a href="/vehicle-details/${
                            vehicle.slug
                        }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">${ucfirst(
            vehicle.name
        )}</a></h3>
                        <h6>${_l("web.common.category")} : <span>${ucfirst(
            vehicle.brand ?? ""
        )}</span></h6>
                    </div>
                    <div class="blog-list-rate">
                        <div class="list-rating">
                            ${(() => {
                                let starsHtml = "";
                                const filledStars = Math.floor(vehicleRating);
                                for (let i = 1; i <= 5; i++) {
                                    starsHtml += `<i class="fas fa-star ${
                                        i <= filledStars ? "filled" : ""
                                    }"></i>`;
                                }
                                return starsHtml;
                            })()}
                            <span>(${vehicleRating.toFixed(1)}) ${
            vehicle.review_count || 0
        } ${_l("web.home.reviews")}</span>
                        </div>
                        <h6>${currency}${priceValue} <span>/ ${ucfirst(
            priceType ?? ""
        )}</span></h6>
                    </div>
                </div>
                <div class="listing-details-group">${featureList}</div>
                <div class="blog-list-head list-head-bottom d-flex">
                    <div class="blog-list-title">
                        <div class="title-bottom">
                            <div class="car-list-icon">
                                <img src="${
                                    vehicle.avatar_image ??
                                    "/frontend/assets/img/profiles/avatar-01.jpg"
                                }" alt="user">
                            </div>
                            <div class="address-info">
                                <h6><i class="feather-map-pin"></i>${ucfirst(
                                    vehicle.location ?? ""
                                )}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="listing-button">
                        <a href="/vehicle-details/${
                            vehicle.slug
                        }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}" class="btn btn-order">
                            <span><i class="feather-calendar me-2"></i></span>${_l(
                                "web.home.rent_now"
                            )}
                        </a>
                    </div>
                </div>
            </div>
        </div>`;

        // Optional tag block
        let tag = "";
        if (vehicle.is_featured) {
            tag = `<div class="feature-text"><span class="bg-danger">${_l(
                "web.common.featured"
            )}</span></div>`;
        } else if (vehicle.is_top_rated) {
            tag = `<div class="feature-text"><span class="bg-warning">${_l(
                "web.common.top_rated"
            )}</span></div>`;
        }

        return `
        <div class="listview-car">
            <div class="card">
                <div class="blog-widget d-flex">
                    ${listingImage}
                    ${listingContent}
                    ${tag}
                </div>
            </div>
        </div>`;
    }

    function ucfirst(str) {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Creates a grid card HTML structure for a vehicle item.
     * @param {Object} vehicle - The vehicle object.
     * @returns {string} HTML string for the vehicle grid card.
     */
    function createVehicleGridCard(vehicle) {
        const currency = vehicle.currency ?? "";
        const vehicleName = ucfirst(vehicle.name ?? "");
        const allowBooking = $("#general-settings").attr("data-allow_booking");
        let price_type = "";
        let price_value = "";

        if (vehicle.price.length > 0) {
            const firstPrice = vehicle.price[0];
            [price_type, price_value] = Object.entries(firstPrice)[0];
        }

        const vehicleImages = vehicle.multiple_vehicle_images
            .map(
                (img) => `
      <div class="slide-images">
        <a href="/vehicle-details/${vehicle.slug}?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">
          <img src="${img}" class="img-fluid" alt="${vehicleName}">
        </a>
      </div>
    `
            )
            .join("");

        const hasMultipleImages = vehicle.has_multiple_image;
        const authWishlistBtn = vehicle.authenticated
            ? `<button type="button" class="fav-icon wishlist-icon ${
                  vehicle.wishlist ? "selected" : ""
              }" data-id="${vehicle.id}"><i class="feather-heart"></i></button>`
            : "";

        const listingImage = hasMultipleImages
            ? `
      <div class="listing-img">
        <div class="img-slider owl-carousel">${vehicleImages}</div>
        <div class="fav-item justify-content-end">
          <span class="img-count"><i class="feather-image"></i>${
              vehicle.multiple_vehicle_images.length
          }</span>
          ${authWishlistBtn}
        </div>
        <span class="featured-text">${vehicle.brand ?? ""}</span>
      </div>
    `
            : `
      <div class="listing-img">
        <a href="/vehicle-details/${
            vehicle.slug
        }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">
          <img src="${
              vehicle.multiple_vehicle_images[0]
          }" class="img-fluid" alt="${vehicleName}">
        </a>
        <div class="fav-item justify-content-end">${authWishlistBtn}</div>
        <span class="featured-text">${ucfirst(vehicle.brand ?? "")}</span>
      </div>
    `;

        const attrIcon =
            vehicle.category === "Car"
                ? `<li><span><img src="/frontend/assets/img/icons/door-icon.svg" alt="Doors"></span><p>${
                      vehicle.num_doors ?? 0
                  }</p></li>`
                : `<li><span><img src="/frontend/assets/img/icons/color.svg" alt="Color"></span><p>${
                      vehicle.color ?? ""
                  }</p></li>`;

        const featureList = `
    <ul>
      <li><span><img src="/frontend/assets/img/icons/car-parts-01.svg" alt="${ucfirst(
          vehicle.transmission ?? ""
      )}"></span><p>${ucfirst(vehicle.transmission ?? "")}</p></li>
      <li><span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="${
          vehicle.mileage ? Math.ceil(vehicle.mileage) : 0
      } ${_l("web.home.miles")}"></span><p>${
            vehicle.mileage ? Math.ceil(vehicle.mileage) : 0
        } ${_l("web.home.miles")}</p></li>
      <li><span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="${ucfirst(
          vehicle.fuel_type ?? ""
      )}"></span><p>${ucfirst(vehicle.fuel_type ?? "")}</p></li>
    </ul>
    <ul>
      ${attrIcon}
      <li><span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="${
          vehicle.year ?? ""
      }"></span><p>${vehicle.year ?? ""}</p></li>
      <li><span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="${_l(
          "web.home.persons"
      )}"></span><p>${vehicle.passenger_capacity ?? 0} ${_l(
            "web.home.persons"
        )}</p></li>
    </ul>
  `;

        const vehicleRating = vehicle.rating ?? 0;
        const ratingStars = (() => {
            const filledStars = Math.floor(vehicleRating);
            return Array.from(
                { length: 5 },
                (_, i) =>
                    `<i class="fas fa-star ${
                        i < filledStars ? "filled" : ""
                    }"></i>`
            ).join("");
        })();

        const listingContent = `
    <div class="listing-content">
      <div class="listing-features d-flex align-items-end justify-content-between">
        <div class="list-rating">
          <button type="button" class="author-img btn border-0">
            <img src="${
                vehicle.avatar_image ??
                "/frontend/assets/img/profiles/avatar-01.jpg"
            }" alt="author">
          </button>
          <h3 class="listing-title">
            <a href="/vehicle-details/${
                vehicle.slug
            }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}">${vehicleName}</a>
          </h3>
          <div class="list-rating">
            ${ratingStars}
            <span>(${vehicleRating.toFixed(1)}) ${
            vehicle.review_count || 0
        } ${_l("web.home.reviews")}</span>
          </div>
        </div>
        <div class="list-km d-none">
          <span class="km-count"><img src="/frontend/assets/img/icons/map-pin.svg" alt="author">3.5m</span>
        </div>
      </div>
      <div class="listing-details-group">${featureList}</div>
      <div class="listing-location-details">
        <div class="listing-price"><span><i class="feather-map-pin"></i></span>${ucfirst(
            vehicle.location ?? ""
        )}</div>
        <div class="listing-price"><h6>${currency}${price_value} <span> / ${ucfirst(
            price_type
        )}</span></h6></div>
      </div>
      <div class="listing-button">
        <a href="/vehicle-details/${
            vehicle.slug
        }?pl=${pl}&dl=${dl}&pd=${pd}&pt=${pt}&rd=${rd}&rt=${rt}" class="btn btn-order ${
            allowBooking !== "1" ? "disabled" : ""
        }">
          <span><i class="feather-calendar me-2"></i></span>${_l(
              "web.home.rent_now"
          )}
        </a>
      </div>
    </div>
  `;

        let tag = "";
        if (vehicle.is_featured) {
            tag = `<div class="feature-text"><span class="bg-danger">${_l(
                "web.common.featured"
            )}</span></div>`;
        }
        if (vehicle.is_top_rated) {
            tag = `<div class="feature-text"><span class="bg-warning">${_l(
                "web.common.top_rated"
            )}</span></div>`;
        }

        return `
    <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
      <div class="listing-item">
        ${listingImage}
        ${listingContent}
        ${tag}
      </div>
    </div>
  `;
    }

    /**
     * Re-initializes an Owl Carousel for a given className.
     * Applies RTL support and responsive behavior.
     * @param {string} className - The CSS class selector for the carousel container.
     */
    function reInitializeCarousel(className) {
        const isRtl = $("body").data("dir") === "rtl";

        $(className).owlCarousel({
            loop: true,
            margin: 24,
            nav: true,
            dots: true,
            rtl: isRtl,
            smartSpeed: 2000,
            autoplay: false,
            navText: [
                "<i class=\"fa-solid fa-chevron-left\"></i>",
                "<i class=\"fa-solid fa-chevron-right\"></i>",
            ],
            responsive: {
                0: { items: 1 },
                550: { items: 1 },
                768: { items: 1 },
                1000: { items: 1 },
            },
        });
    }

    // Initialize ionRangeSlider for price-range element if it exists
    if ($(".price-range").length > 0) {
        $(".price-range").ionRangeSlider({
            type: "double",
            grid: true,
            min: 0,
            max: 5000,
            from: 0,
            to: 500,
            prefix: typeof _currency !== "undefined" ? _currency : "$",
        });
    }

    // Update demo span with slider value on input change
    $(".price-range").on("input", function () {
        $(".demo span").html(this.value);
    });

    // Wishlist icon toggle with AJAX request
    $(document).on("click", ".wishlist-icon", function () {
        const id = $(this).data("id");
        const $button = $(this);
        const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");

        if (!id || !csrfToken) return;

        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: { id: id, _token: csrfToken },
            dataType: "json",
            success(response) {
                if (response.status === "success") {
                    $button.toggleClass("selected");
                    showToast("success", response.message);
                } else {
                    showToast("error", response.message);
                }
            },
            error() {
                showToast("error", "Something went wrong. Please try again.");
            },
        });
    });

    /**
     * Handles location input and displays suggestions with caching and debounce.
     */
    $(document).ready(function () {
        const $input = $("#pickuplocation");
        const $suggestions = $("#pickup-suggestions");
        const initialId = $("#initialPickupId").val();
        const initialName = $("#initialPickupName").val();
        const cache = {};
        let searchTimeout;

        // Prepopulate input if initial values are available
        if (initialId && initialName) {
            $input.val(initialName);
            $suggestions
                .html(
                    `<li data-id="${initialId}" class="selected">${initialName}</li>`
                )
                .hide();
        }

        // Handles user input with debounce for location search
        $input.on("keyup", function () {
            const query = $(this).val().trim().toLowerCase();
            clearTimeout(searchTimeout);

            if (query.length < 1) {
                $suggestions.hide();
                return;
            }

            if (cache[query]) {
                displaySuggestions(cache[query]);
                return;
            }

            // Delay search to avoid rapid AJAX calls
            searchTimeout = setTimeout(() => {
                $.ajax({
                    url: "/search-locations",
                    method: "GET",
                    data: { query },
                    success(response) {
                        if (Array.isArray(response.data)) {
                            cache[query] = response.data;
                            displaySuggestions(response.data);
                        }
                    },
                    error() {
                        $suggestions.hide();
                    },
                });
            }, 300);
        });

        /**
         * Display suggestions list from search data
         * @param {Array} data - Location results
         */
        function displaySuggestions(data) {
            $suggestions.empty();

            if (data.length > 0) {
                data.forEach((location) => {
                    $("<li>")
                        .attr("data-id", location.id)
                        .text(location.name)
                        .appendTo($suggestions);
                });
            } else {
                $("<li>")
                    .addClass("no-result")
                    .text(_l("web.home.no_location_found"))
                    .appendTo($suggestions);
            }

            $suggestions.show();
        }

        // Set selected location on click
        $(document).on("click", "#pickup-suggestions li", function () {
            if (!$(this).hasClass("no-result")) {
                $input.val($(this).text());
                $("#pickup-suggestions li").removeClass("selected");
                $(this).addClass("selected");
            }
            $suggestions.hide();
        });

        // Hide suggestion list when clicking outside
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".group-img").length) {
                $suggestions.hide();
            }
        });
    });
})();
