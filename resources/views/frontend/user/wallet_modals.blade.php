<!-- Add Card Modal -->
<div class="modal new-modal fade" id="add_card" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Card</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="modal-form-group">
                        <label>Card Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Card Number">
                    </div>
                    <div class="modal-form-group">
                        <label>Name on Card <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Card Name">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="modal-form-group">
                                <label>CVV <span class="text-danger">*</span></label>
                                <div class="form-icon">
                                    <input type="text" class="form-control" placeholder="Enter CVV Number">
                                    <span class="cus-icon">
                                        <img src="/frontend/assets/img/icons/lock-icon.svg" alt="Icon">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modal-form-group">
                                <label>Expiry Date <span class="text-danger">*</span></label>
                                <div class="form-icon">
                                    <input type="text" class="form-control" placeholder="DD/MM/YYYY">
                                    <span class="cus-icon">
                                        <img src="/frontend/assets/img/icons/calendar-icon.svg" alt="Icon">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label class="custom_check">
                            <input type="checkbox" name="rememberme" class="rememberme">
                            <span class="checkmark"></span>
                            Save this account for future transaction
                        </label>
                    </div>
                    <div class="modal-btn">
                        <button type="submit" class="btn btn-secondary w-100">Pay $4700</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Card Modal -->

<!-- Add Payment Modal -->
<div class="modal new-modal multi-step fade" id="add_payment" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('web.user.add_payment') }}</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div class="total-payment">
                    <p>{{ __('web.user.available_balance') }}</p>
                    <h6 class="available_balance"></h6>
                </div>
                <form id="add_wallet">
                    <div class="choose-payment-info">
                        <h5>{{ __('web.user.choose_your_payment_method') }}</h5>
                        <input type="hidden" name="wallet_amount" id="wallet_amount">
                        <div class="d-flex justify-content-center">
                            <div class="choose-payment d-flex gap-3">
                                @foreach (['paypal' => 'payment-1.svg', 'stripe' => 'payment-2.svg'] as $id => $icon)
                                    <label class="custom_radio text-center">
                                        <input type="radio" name="payment_one" id="{{ $id }}" value="wallet_one" {{ $loop->first ? 'checked' : '' }}>
                                        <span class="checkmark d-block mx-auto"></span>
                                        <img src="/frontend/assets/img/icons/{{ $icon }}" alt="Icon" class="img-fluid mt-2">
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="add-payment-table-info d-none">
                            <div class="wallet-table add-payment-table">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label class="custom_radio">
                                                        <input type="radio" name="payment_two" value="wallet_two">
                                                        <span class="checkmark"></span>
                                                        <img src="/frontend/assets/img/icons/wallet-01.svg" alt="Icon">
                                                    </label>
                                                </td>
                                                <td>
                                                    <h6>3210 **** **** **12</h6>
                                                    <p>Card Number</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-btn">
                        <button type="submit" class="btn btn-secondary w-100" data-bs-target="#order_success" data-bs-toggle="modal" data-bs-dismiss="modal">Add to Wallet</button>
                        <a class="btn btn-outline-cancel" data-bs-dismiss="modal">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Payment Modal -->

<!-- Delete Modal -->
<div class="modal new-modal fade" id="delete_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="delete-action">
                    <div class="delete-header">
                        <h4>Delete Wallet History</h4>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn">
                        <div class="row">
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-secondary w-100">Delete</a>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->
