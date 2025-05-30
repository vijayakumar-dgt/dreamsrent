<div class="modal fade {{ $className }}" id="{{ $id }}">
    <div class="modal-dialog {{ isset($dialogPosition) ? $dialogPosition : 'modal-dialog-centered' }} {{ $dialogClassName ?? 'modal-md' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0 modal-title">{{ $title }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form action="{{ $action ?? null }}" method="{{ $method ?? 'POST' }}" id="{{ $formId }}" autocomplete="off">
                @csrf
                <div class="modal-body">
                    {{ $body }}
                </div>
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            </form>
        </div>
    </div>
</div>