@extends($layout)

@section('content')
    <!-- Breadcrumb Section -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">{{ __('web.user.user_wishlist') }}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('web.home.home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_wishlist') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb Section -->
    @include('frontend.user.nav_menu')
    <div class="content dashboard-content">
        <div class="container">
            <!-- Content Header -->
            <div class="content-header">
                <h4>{{ __('web.user.wishlist') }}</h4>
            </div>
            <!-- /Content Header -->
            <div class="row">
                <!-- Wishlist Skeleton -->
                <div class="col-md-12 data-loader position-relative vh-10">
                    @include('frontend.content-loader')
                </div>
                <!-- /Wishlist Skeleton -->
                <!-- Real Wishlist -->
                <div class="col-md-12 d-none real-table">
                    <div class="wishlist-wrap">
                        <div class="listview-car"></div>
                    </div>
                </div>
                <!-- /Real Wishlist -->
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('frontend/assets/js/custom/user/wishlists.js') }}"></script>
@endpush
