<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Rental - Installer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('frontend/assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/global/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>

<body>
    <main class="container mt-5 main installer-main">
        <h1 class="text-center text-uppercase text-primary mb-3">Installer</h1>

        <div class="row">
            <ul class="progressbar installer-progress-bar">
                <li class="@if (request()->routeIs('setup.verify') || (session()->has('step-1-complete') && session()->get('step-1-complete'))) active @endif"><a href="{{route('setup.verify')}}">Verification</a></li>

                <li class="@if (request()->routeIs('setup.requirements') ||
                        (session()->has('step-2-complete') && session()->get('step-2-complete'))) active @endif"><a href="@if ((session()->has('step-1-complete') && session()->get('step-1-complete'))) {{route('setup.requirements')}} @else # @endif" class="@if (!session()->has('step-1-complete')) text-muted @endif">Requirements</a></li>

                <li class="@if (request()->routeIs('setup.database') || (session()->has('step-3-complete') && session()->get('step-3-complete'))) active @endif"><a href="@if ((session()->has('step-2-complete') && session()->get('step-2-complete') && session()->has('requirements-complete') && session()->get('requirements-complete'))) {{route('setup.database')}} @else # @endif" class="@if (!session()->has('requirements-complete')) text-muted @endif">Database Setup</a></li>

                <li class="@if (request()->routeIs('setup.account') || (session()->has('step-4-complete') && session()->get('step-4-complete'))) active @endif"><a href="@if ((session()->has('step-3-complete') && session()->get('step-3-complete'))) {{route('setup.account')}} @else # @endif" class="@if (!session()->has('step-3-complete')) text-muted @endif">Account Setup</a></li>

                <li class="@if (request()->routeIs('setup.configuration') ||
                        (session()->has('step-5-complete') && session()->get('step-5-complete'))) active @endif"><a href="@if ((session()->has('step-4-complete') && session()->get('step-4-complete'))) {{route('setup.configuration')}} @else # @endif" class="@if (!session()->has('step-4-complete')) text-muted @endif">Configuration</a></li>

                <li class="@if (request()->routeIs('setup.complete') || (session()->has('step-7-complete') && session()->get('step-7-complete'))) active @endif"><a href="@if ((session()->has('step-6-complete') && session()->get('step-6-complete'))) {{route('setup.complete')}} @else # @endif" class="@if (!session()->has('step-6-complete')) text-muted @endif">Complete</a></li>
            </ul>

        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                @if ($errors->any())
                    <div class="mb-1 card">
                        <div class="card-body text-danger">
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>

    </main>
</body>
<script src="{{ asset('frontend/global/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('frontend/global/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const makeAjaxRequest = (formData, actionUrl) => {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: actionUrl,
                method: "post",
                data: formData,
                success: function(res) {
                    resolve(res);
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    }
    $(document).ready(function () {
        toastr.options = {
                "closeButton": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000",
                "progressBar": true,
                "onShown": function () {
                    $('.toast-success').css({
                        'background-color': '#28a745',
                        'color': '#fff'
                    });
                    $('.toast-error').css({
                        'background-color': '#dc3545',
                        'color': '#fff'
                    });
                    $('.toast-warning').css({
                        'background-color': '#f0ad4e',
                        'color': '#fff'
                    });
                    $('.toast-info').css({
                        'background-color': '#17a2b8',
                        'color': '#fff'
                    });
                }
        };
    });

</script>
@stack('scripts')

</html>
