<div class="modal fade {{ $className }}" id="{{ $id }}">
    <div class="modal-dialog {{ isset($dialogPosition) ? $dialogPosition : 'modal-dialog-centered' }} {{ $dialogClassName }}">
        <div class="modal-content">
            @if ($isHeader)
            <div class="modal-header">
                <h4 class="mb-0 modal-title" @if($modalTitleId) id="{{ $modalTitleId }}" @endif>{{ $title }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            @endif
            @if (!empty($formId))
            <form action="{{ $action ?? null }}" method="{{ $method ?? 'POST' }}" id="{{ $formId }}" @if($enctype) enctype="multipart/form-data" @endif  autocomplete="off">
                @csrf
            @endif
                <div class="modal-body {{ $modalBodyClass ?? '' }}">
                    {{ $body }}
                </div>
                @if (isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
                @endif
            @if (!empty($formId))
            </form>
            @endif
        </div>
    </div>
</div>
