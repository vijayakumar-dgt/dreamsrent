<div id="cookieConsentBanner" class="cookie-consent d-none">
    <p class="cookie-message">
        {!! $data['cookie_settings']['content'] ?: 'We use cookies to enhance your experience.' !!}
    </p>
    <div class="cookie-actions">
        <a href="{{ $data['cookie_settings']['cookies_page_link'] ?? '#' }}" target="_blank" rel="noopener" class="cookie-link">
        {{ __('web.common.learn_more') }}
    </a>
    </div>
    <div class="cookie-buttons">
        <button id="cookieAgree" class="cookie-btn agree-btn">
            {{ $data['cookie_settings']['agree_btn_text'] ?? 'Agree' }}
        </button>
        @if ($data['cookie_settings']['show_decline_btn'])
            <button id="cookieDecline" class="cookie-btn decline-btn">
                {{ $data['cookie_settings']['decline_btn_text'] ?? 'Decline' }}
            </button>
        @endif
    </div>
</div>
