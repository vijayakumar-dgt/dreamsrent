    @extends($layout)

    @section('content')
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12">
                    <h2 class="breadcrumb-title">{{ __('web.user.user_message') }}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('web.home.home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_message') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.user.nav_menu')
    <div class="content content-chat top-space-chat">
        <div class="container">
            <!-- Content Header -->
            <div class="content-header">
                <h4>{{ __('web.user.messages') }}</h4>
            </div>
            <!-- /Content Header -->
            <div class="row chat-window">
                <div class="col-xl-12">
                    <div class="chat-window">
                        <!-- Chat Left -->
                        <div class="chat-cont-left">
                            <div class="chat-header">
                                <span>{{ __('web.user.chats') }}</span>
                            </div>
                            <form class="chat-search d-none">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" class="form-control rounded-pill" placeholder="{{ __('web.user.search') }}">
                                </div>
                            </form>
                            <div class="chat-users-list">
                                <div class="chat-scroll">
                                    <a href="javascript:void(0);" class="notify-block d-flex open-chat">
                                        <div class="media-img-wrap flex-shrink-0">
                                            <div class="avatar">
                                                <img src="{{ uploadedAsset($receiver->userDetail->profile_image ?? 'default', 'profile') }}" id="profileavatar" class="avatar-img rounded-circle">
                                            </div>
                                        </div>
                                        <div class="media-body chat-custom flex-grow-1">
                                            <div>
                                                <div class="user-name">{{ getCurrentUserFullname($receiver->id) }}</div>
                                                <div class="user-last-chat">
                                                    {{ $lastMessage ? Str::limit($lastMessage->message, 20) : '' }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="last-chat-time block">
                                                    {{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- /Chat Left -->
                        <!-- Chat Right -->
                        <div class="chat-cont-right">
                            <div class="chat-header">
                                <a id="back_user_list" href="javascript:void(0)" class="back-user-list">
                                    <i class="feather-chevron-left"></i>
                                </a>
                                <div class="notify-block d-flex">
                                    <div class="media-img-wrap flex-shrink-0">
                                        <div class="avatar">
                                            <img src="{{ uploadedAsset($receiver->userDetail->profile_image ?? 'default', 'profile') }}" class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                    <div class="media-body flex-grow-1">
                                        <div class="user-name">{{ getCurrentUserFullname($receiver->id) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-body">
                                <div class="chat-scroll" id="messagebody">
                                    <ul class="list-unstyled" id="messageArea"></ul>
                                </div>
                            </div>
                            <div class="p-3 d-flex">
                                <span class="selected_file border-0 rounded-pill text-muted px-2 py-1 me-2 bg-dark d-none"></span>
                            </div>
                            <div class="chat-footer">
                                <div class="input-group">
                                    <div class="btn-file btn">
                                        <i class="fa fa-paperclip"></i>
                                        <input type="file" id="fileupload">
                                    </div>
                                    <input type="text" class="input-msg-send form-control rounded-pill" name="messageinput" id="messageinput" data-senderid="{{ $sender->id }}" data-receiverid="{{ $receiver->id }}" placeholder="{{ __('web.user.type_something') }}">
                                    <button type="button" class="btn msg-send-btn rounded-pill ms-2" id="sendmsg">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- /Chat Right -->
                    </div>
                </div>
            </div>
            <!-- /Row -->
        </div>
    </div>
    @endsection

    @push('scripts')
<script src="{{ asset('frontend/assets/js/custom/user/mqtt.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/user/messages.js') }}"></script>
    @endpush
