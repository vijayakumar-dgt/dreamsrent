@extends($layout)

@section('content')
	<!-- Breadscrumb Section -->
	<div class="breadcrumb-bar">
		<div class="container">
			<div class="row align-items-center text-center">
				<div class="col-md-12 col-12">
					<h2 class="breadcrumb-title">{{ __('web.user.user_bookings') }}</h2>
					<nav aria-label="breadcrumb" class="page-breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item">
								<a href="/">{{ __('web.home.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								{{ __('web.user.user_bookings') }}
							</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<!-- /Breadscrumb Section -->
	@include('frontend.user.nav_menu')
	<!-- Page Content -->
	<div class="content settings-profile-content">
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
							<h4>Integrations</h4>
						</div>
						<div class="row">
							<div class="col-md-5">
								<div class="integration-grid">
									<div class="integration-calendar">
										<img src="assets/img/icons/integration-icon.svg" alt="Icon">
										<div class="status-toggle">
											<input id="google_calendar" class="check" type="checkbox" checked="">
											<label for="google_calendar" class="checktoggle">checkbox</label>
										</div>
									</div>
									<div class="integration-content">
										<h5>Google Calendar</h5>
										<p>Powerful & free service to organize your schedule and coordinate events</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Settings Details -->
			</div>
		</div>
	</div>
	<!-- /Page Content -->
@endsection
