@extends('admin.admin')

@section('meta_title', __('admin.finance_accounts.add_invoice') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <div class="mb-3">
                <a href="/admin/invoices" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-narrow-left me-2"></i>{{ __('admin.common.back_to_list') }}</a>
            </div>
            <div class="filterbox mb-3 d-flex align-items-center invoice-title">
                <h4 class="me-3"><i class="ti ti-menu-2 me-2"></i>{{ __('admin.finance_accounts.add_invoice') }}</h4>
            </div>
            <div class="card mb-0">
                <form action="{{ route('admin.invoiceStore') }}" id="invoiceAdd" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="border-bottom mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="me-lg-3">
                                        <h5 class="mb-3">{{ __('admin.finance_accounts.invoice_details') }}</h5>
                                        <div class="row gx-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.finance_accounts.invoice_number') }}</label>
                                                    <input type="text" name="invoice_number" id="invoice_number" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.common.vehicle') }}</label>
                                                    <select class="select form-control" name="car_id">
                                                        <option>{{ __('admin.common.select') }}</option>
                                                        @foreach($cars as $car)
                                                        <option value="{{$car->id}}">{{$car->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.finance_accounts.from_date') }}</label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" name="from_date" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.finance_accounts.due_date') }}</label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" name="to_date" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.finance_accounts.currency') }}</label>
                                                    <select class="select form-control" name="currency_id">
                                                        <option>{{ __('admin.common.select') }}</option>
                                                        @foreach($currencies as $currency)
                                                        <option value="{{$currency->id}}">{{$currency->currency_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.common.status') }}</label>
                                                    <select class="select form-control" name="status">
                                                        <option>{{ __('admin.common.select') }}</option>
                                                        <option value="Paid">{{ __('admin.finance_accounts.paid') }}</option>
                                                        <option value="Pending">{{ __('admin.finance_accounts.pending') }}</option>
                                                        <option value="Overdue">{{ __('admin.finance_accounts.overdue') }}</option>
                                                        <option value="Unpaid">{{ __('admin.finance_accounts.unpaid') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ms-lg-3">
                                        <h5 class="mb-3">{{ __('admin.finance_accounts.billing_details') }}</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('admin.common.from') }}</label>
                                                    <input type="text" class="form-control" name="biller" value='{{$currentUser->name}}' readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="form-label">{{ __('admin.common.to') }}</label>
                                                        <a href="admin/customers" class="text-info d-block mb-1">{{ __('admin.common.add_new') }}</a>
                                                    </div>
                                                    <select class="select" name="customer_id">
                                                        <option>{{ __('admin.finance_accounts.select_customer') }}</option>
                                                        @foreach($users as $user)
                                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-3">{{ __('admin.finance_accounts.rental_details') }}</h5>
                            <div class="table-responsive border border-gray br-10 mb-3">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="w-50">{{ strtoupper(__('admin.common.description')) }}</th>
                                            <th>{{ strtoupper(__('admin.finance_accounts.quantity')) }}</th>
                                            <th>{{ strtoupper(__('admin.finance_accounts.net_price')) }}</th>
                                            <th>{{ strtoupper(__('admin.finance_accounts.tax')) }}</th>
                                            <th>{{ strtoupper(__('admin.finance_accounts.total_price')) }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="rental-details-body">
                                        <tr>
                                            <td class="pe-0">
                                                <div><input type="text" name="items[0][description]" class="form-control"></div>
                                            </td>
                                            <td class="pe-0">
                                                <div><input type="number" name="items[0][qty]" class="form-control qty"></div>
                                            </td>
                                            <td class="pe-0">
                                                <div><input type="number" name="items[0][price]" class="form-control price"></div>
                                            </td>
                                            <td class="pe-0">
                                                <div><input type="number" name="items[0][tax]" class="form-control"></div>
                                            </td>
                                            <td class="pe-0">
                                                <div><input type="number" name="items[0][total_price]" class="form-control total" readonly></div>
                                            </td>
                                            <td>
                                                <div><a href="javascript:void(0);" class="btn btn-icon btn-sm text-danger delete-row"><i class="ti ti-trash"></i></a></div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                <a href="javascript:void(0);" class="btn btn-secondary d-inline-flex align-items-center me-2" id="addMoreRow">
                                    <i class="ti ti-plus me-1"></i>{{ __('admin.finance_accounts.add_more') }}
                                </a>
                                <a href="javascript:void(0);" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#link_reservation">{{ __('admin.finance_accounts.link_reservation') }}</a>
                            </div>
                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="me-lg-4">
                                            <h5 class="mb-3">{{ __('admin.finance_accounts.others') }}</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.finance_accounts.payment_method') }}</label>
                                                         <select class="select" name="payment_method">
                                                            @foreach($payments as $payment)
                                                            <?php
                                                            $paymentKey = Illuminate\Support\Str::before($payment->key, '_');
                                                            if($paymentKey == 'payment') {
                                                                $payment = 'Bank Transfer';
                                                            }
                                                            else{
                                                                $payment = ucfirst($paymentKey);
                                                            }
                                                            ?>
                                                            <option value="{{ $paymentKey }}">{{ $payment }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.common.terms_and_conditions') }} <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" rows="3" name="terms"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.finance_accounts.notes') }} <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" rows="3" name="notes"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="border-bottom mb-3">
                                                    <input type="hidden" name="subtotal" id="subtotal-value">
                                                    <input type="hidden" name="tax" id="tax-value">
                                                    <input type="hidden" name="discount" id="discount-value">
                                                    <input type="hidden" name="grand_total" id="grand-total-value">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span>{{ __('admin.finance_accounts.subtotal') }}</span>
                                                        <h6 id="sub-total">{{$symbol}}0.00</h6>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span>{{ __('admin.finance_accounts.discount')}} (0%)</span>
                                                        <h6 class="text-danger fs-14 fw-medium">$0.00</h6>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span>{{ __('admin.finance_accounts.tax')}} (0%)</span>
                                                        <h6>$0.00</h6>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h4>{{ __('admin.finance_accounts.total_amount')}} </h4>
                                                    <h4 id="grand-total">{{$symbol}}0.00 </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="javascript:void(0);" class="btn btn-light me-2">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.save')}} & {{ __('admin.common.send') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Link Reservation -->
    <div class="modal fade" id="link_reservation">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">{{ __('admin.finance_accounts.link_reservation') }}</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Custom Data Table -->
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table" id="linkReservationTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="no-sort">{{ __('admin.common.id') }}</th>
                                    <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                                    <th>{{ strtoupper(__('admin.common.customer')) }}</th>
                                    <th>{{ strtoupper(__('admin.finance_accounts.pick_up_details')) }}</th>
                                    <th>{{ strtoupper(__('admin.finance_accounts.drop_off_details')) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr class="booking-row"
                                    data-id="{{ $booking->reservation_id }}"
                                    data-vehicle="{{ $booking->vehicle }}"
                                    data-final_price="{{ $booking->final_price }}"
                                    data-price="{{ $booking->price ?? 0 }}"
                                    data-tax="{{ $booking->tax ?? 0 }}">
                                    <td>
                                        <a href="javascript:;" class="text-info d-block mb-1">#{{$booking->reservation_id}}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                                @php
                                                $imagePath = 'storage/' . $booking->vehicle_image;
                                                $defaultImage = asset('backend/assets/img/default-profile.png');
                                                @endphp

                                                <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="profile image">
                                            </a>
                                            <div>
                                                <h6 class="fs-14"><a href="javascript:void(0);">{{$booking->vehicle}}</a></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-rounded me-2 flex-shrink-0"> @php
                                                $imagePath = 'storage/' . $booking->profile_image;
                                                $defaultImage = asset('backend/assets/img/default-profile.png');
                                                @endphp

                                                <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="profile image"></a>
                                            <div>
                                                <h6 class="mb-1 fs-14"><a href="javascript:void(0);">{{$booking->customer}}</a></h6>

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                                <h5 class="mb-2 fs-16">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('d') }}</h5>
                                                <span class="fw-medium fs-12 bg-light p-1 rounded-1 d-inline-block text-gray-9">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('M, Y') }}</span>
                                            </div>
                                            <div>
                                                <?php
                                                $start = Illuminate\Support\Facades\DB::table('locations')->where('id', $booking->pickup_location)->first();
                                                ?>
                                                <p class="text-gray-9 mb-0">{{$start->name ?? ''}}</p>
                                                <span class="fs-13">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('h:m a') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                                <h5 class="mb-2 fs-16">{{ \Carbon\Carbon::parse($booking->end_datetime)->format('d') }}</h5>
                                                <span class="fw-medium fs-12 bg-light p-1 rounded-1 d-inline-block text-gray-9">{{ \Carbon\Carbon::parse($booking->end_datetime)->format('M, Y') }}</span>
                                            </div>
                                            <div>
                                                <?php
                                                $drop = Illuminate\Support\Facades\DB::table('locations')->where('id', $booking->return_location)->first();
                                                ?>
                                                <p class="text-gray-9 mb-0">{{$drop->name ?? ''}} </p>
                                                <span class="fs-13">{{ \Carbon\Carbon::parse($booking->end_datetime)->format('h:m a') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Custom Data Table -->
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                        <a href="/admin/invoices" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <a href="javascript:void(0);" class="btn btn-primary">{{ __('admin.common.create_new') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Link Reservation -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/invoice.js') }}"></script>
@endpush