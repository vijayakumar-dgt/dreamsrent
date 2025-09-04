<div class="modal fade {{ $className ?? 'deletemodal' }}" id="{{ $id ?? 'delete-modal' }}">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            @if (!empty($formId))
            <form action="{{ $action ?? null }}" method="{{ $method ?? 'POST' }}" id="{{ $formId }}" class="{{ $formClass ?? null }}">
                @csrf
            @endif
                @if(!empty($hiddenInputs))
                @foreach ($hiddenInputs as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" id="{{ $key }}">
                @endforeach
                @endif
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="{{ $modalIconClass }}"></i>
                    </span>
                    <h4 class="mb-1">{{ $title }}</h4>
                    <p class="mb-3">{{ $description }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                        <button type="{{ $deleteBtnType ?? 'submit' }}" class="btn btn-primary" @if ($deleteBtnId) id="{{ $deleteBtnId }}" @endif>{{ $deleteBtnText }}</button>
                    </div>
                </div>
            @if (!empty($formId))
            </form>
            @endif
        </div>
    </div>
</div>
