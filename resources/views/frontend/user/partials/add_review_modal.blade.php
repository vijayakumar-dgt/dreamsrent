<!-- Add Review Modal start -->
<div class="modal new-modal fade" id="addReviewModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('web.home.leave_reply') }}</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Leave a Reply -->
                    <div class="leave-reply-form mb-0">
                        <div class="review-list-rating">
                            <div class="row">
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="set-rating">
                                        <p>{{__('web.home.service')}}</p>
                                        <div class="rating-selection" id="service_ratings">
                                            <input type="checkbox" id="service1" class="service_ratings" value="1">
                                            <label for="service1"></label>
                                            <input type="checkbox" id="service2" class="service_ratings" value="2">
                                            <label for="service2"></label>
                                            <input type="checkbox" id="service3" class="service_ratings" value="3">
                                            <label for="service3"></label>
                                            <input type="checkbox" id="service4" class="service_ratings" value="4">
                                            <label for="service4"></label>
                                            <input type="checkbox" id="service5" class="service_ratings" value="5">
                                            <label for="service5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="set-rating">
                                        <p>{{__('web.user.location')}}</p>
                                        <div class="rating-selection" id="location_ratings">
                                            <input type="checkbox" id="loc1" class="location_ratings" value="1">
                                            <label for="loc1"></label>
                                            <input type="checkbox" id="loc2" class="location_ratings" value="2">
                                            <label for="loc2"></label>
                                            <input type="checkbox" id="loc3" class="location_ratings" value="3">
                                            <label for="loc3"></label>
                                            <input type="checkbox" id="loc4" class="location_ratings" value="4">
                                            <label for="loc4"></label>
                                            <input type="checkbox" id="loc5" class="location_ratings" value="5">
                                            <label for="loc5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.facilities') }}</p>
                                        <div class="rating-selection" id="facility_ratings">
                                            <input type="checkbox" id="fac1" class="facility_ratings" value="1">
                                            <label for="fac1"></label>
                                            <input type="checkbox" id="fac2" class="facility_ratings" value="2">
                                            <label for="fac2"></label>
                                            <input type="checkbox" id="fac3" class="facility_ratings" value="3">
                                            <label for="fac3"></label>
                                            <input type="checkbox" id="fac4" class="facility_ratings" value="4">
                                            <label for="fac4"></label>
                                            <input type="checkbox" id="fac5" class="facility_ratings" value="5">
                                            <label for="fac5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.value_for_money') }}</p>
                                        <div class="rating-selection" id="value_for_money_ratings">
                                            <input type="checkbox" id="val1" class="value_for_money_ratings" value="1">
                                            <label for="val1"></label>
                                            <input type="checkbox" id="val2" class="value_for_money_ratings" value="2">
                                            <label for="val2"></label>
                                            <input type="checkbox" id="val3" class="value_for_money_ratings" value="3">
                                            <label for="val3"></label>
                                            <input type="checkbox" id="val4" class="value_for_money_ratings" value="4">
                                            <label for="val4"></label>
                                            <input type="checkbox" id="val5" class="value_for_money_ratings" value="5">
                                            <label for="val5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 col-lg-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.cleanliness') }}</p>
                                        <div class="rating-selection" id="cleanliness_ratings">
                                            <input type="checkbox" id="clean1" class="cleanliness_ratings" value="1">
                                            <label for="clean1"></label>
                                            <input type="checkbox" id="clean2" class="cleanliness_ratings" value="2">
                                            <label for="clean2"></label>
                                            <input type="checkbox" id="clean3" class="cleanliness_ratings" value="3">
                                            <label for="clean3"></label>
                                            <input type="checkbox" id="clean4" class="cleanliness_ratings" value="4">
                                            <label for="clean4"></label>
                                            <input type="checkbox" id="clean5" class="cleanliness_ratings" value="5">
                                            <label for="clean5"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="review-list">
                                <ul>
                                    <li class="review-box feedbackbox mb-0">
                                        <div class="review-details">
                                            <form id="reviewForm" autocomplete="off">
                                                <input type="hidden" name="vehicle_id" class="vehicle_id">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="input-block">
                                                            <label>{{ __('web.home.comments') }}<span class="text-danger"> *</span></label>
                                                            <textarea rows="4" class="form-control" name="comments" id="comments" placeholder="{{ __('web.home.comments') }}"></textarea>
                                                            <span class="text-danger error-text" id="comments_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="submit-btn text-end">
                                                    <button class="btn btn-primary submit-review" type="submit">{{__('web.home.submit_review')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /Leave a Reply -->
            </div>
        </div>
    </div>
</div>
<!-- Add Review Modal end -->
