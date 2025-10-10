<div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
    <p class="mb-0">
        <a href="{{ route('pages', 'privacy-policy') }}" target="_blank" rel="noopener">{{ __('admin.common.privacy_policy') }}</a>
        <a href="{{ route('pages', 'terms-conditions') }}" target="_blank" rel="noopener" class="ms-4">{{ __('admin.common.terms_and_conditions') }}</a>
    </p>
    <p>© {{ date('Y') }} {{ $companyName }}</p>
</div>
