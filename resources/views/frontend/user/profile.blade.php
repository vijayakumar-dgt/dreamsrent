@extends($layout)

@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('web.user.user_bookings')}}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('web.user.user_bookings')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

@include('frontend.user.nav_menu')

<!-- Page Content -->
<div class="content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header content-settings-header">
            <h4>Settings</h4>
        </div>
        <!-- /Content Header -->

        <div class="row">
            @include('frontend.user.user_sidebar')

            <!-- Settings Details -->
            <div class="col-lg-9">
                <div class="settings-info">
                    <div class="settings-sub-heading">
                        <h4>Profile</h4>
                    </div>
                    <form action="#">
                        <!-- Basic Info -->
                        <div class="profile-info-grid">
                            <div class="profile-info-header">
                                <h5>Basic Information</h5>
                                <p>Information about user</p>
                            </div>
                            <div class="profile-inner">
                                <div class="profile-info-pic">
                                    <div class="profile-info-img">
                                        <img src="assets/img/profiles/avatar-15.jpg" alt="Profile">
                                        <div class="profile-edit-info">
                                            <a href="javascript:void(0)"><i class="feather-edit"></i></a>
                                            <a href="javascript:void(0)"><i class="feather-trash-2"></i></a>
                                        </div>
                                    </div>
                                    <div class="profile-info-content">
                                        <h6>Profile picture</h6>
                                        <p>PNG, JPEG under 15 MB</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="profile-form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter First Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Last Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-form-group">
                                            <label>User Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter User Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="+ 1 65656565656">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Email">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Basic Info -->

                        <!-- Address Info -->
                        <div class="profile-info-grid">
                            <div class="profile-info-header">
                                <h5>Address Information</h5>
                                <p>Information about address of user</p>
                            </div>
                            <div class="profile-inner">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-form-group">
                                            <label>Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" placeholder="Enter Address"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Country">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>State <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter State">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter City">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>Pincode <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Pincode">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Address Info -->

                        <!-- Profile Submit -->
                        <div class="profile-submit-btn">
                            <button type="submit" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                        <!-- /Profile Submit -->
                    </form>
                </div>
            </div>
            <!-- /Settings Details -->
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection
