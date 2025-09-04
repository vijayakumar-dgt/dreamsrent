<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h4 class="mb-1">{{ $title }}</h4>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                </li>
                @foreach ($breadcrumbs as $label => $url)
                    @if ($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
    @if ($buttonText && $modalId && $permissionModule && hasPermission($permissions ?? [], $permissionModule, $permissionKey))
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="mb-2">
                <button type="button" class="btn btn-primary d-flex align-items-center"
                    id="{{ $buttonId }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                    <i class="ti ti-plus me-2"></i>{{ $buttonText }}
                </button>
            </div>
        </div>
    @endif
    @if (isset($toolbar) && !empty($toolbar))
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            {{ $toolbar }}
        </div>
    @endif
</div>
<!-- /Breadcrumb -->
