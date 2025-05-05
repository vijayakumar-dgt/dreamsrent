@extends('admin.admin')

@section('meta_title', __('admin.others.chat') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper">
        <div class="content pb-0">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.others.chat') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.others.chat') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <div class="chat-wrapper">
                <!-- Chats sidebar -->
                <div class="sidebar-group">
                    <div id="chats" class="sidebar-content active">
                        <div class="slimscroll">
                            <div class="chat-search-header border-bottom">                            
                                <div class="header-title d-flex align-items-center justify-content-between">
                                    <h4>{{ __('admin.others.chats') }}</h4>
                                    <div class="chat-options">
                                        <ul class="d-flex align-items-center">
                                            <li>
                                                <a href="javascript:void(0)" class="btn chat-search-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('admin.common.search') }}">
                                                    <i class="ti ti-search" ></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- Chat Search -->
                                    <div class="chat-search search-wrap contact-search">
                                        <form>
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Search Contacts">
                                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /Chat Search -->
                                </div>
                            </div>
                            <div class="sidebar-body chat-body" id="chatsidebar">
                                <div class="chat-users-wrap">
                                    @if(!empty($users) && count($users) > 0)
                                        @foreach($users as $user)
                                            <div class="chat-list">
                                                <a href="javascript:void(0);" class="chat-user-list userprofile" data-userid="{{ $user->id }}" data-username="{{ ucfirst($user->name) }}" data-avatar="{{ uploadedAsset($user->userDetail ? $user->userDetail->profile_image : 'default','profile') }}">
                                                    <div class="avatar avatar-lg  me-2">
                                                        <img src="{{ uploadedAsset($user->userDetail ? $user->userDetail->profile_image : 'default','profile') }}"  class="rounded-circle avatarimg" alt="image">
                                                    </div>
                                                    <div class="chat-user-info">
                                                        <div class="chat-user-msg">
                                                            <h6>{{ ucfirst($user->name) }}</h6>
                                                        </div>
                                                        <div class="chat-user-time">
                                                        </div>    
                                                    </div>
                                                </a>    
                                            </div>
                                        @endforeach
                                    @endif   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Chats sidebar -->					
                <!-- Chat -->
                <div class="chat chat-messages show" id="middle">
                    <div>
                        <div class="chat-header">
                            <div class="user-details">
                                <div class="d-xl-none">
                                    <a class="text-muted chat-close me-2" href="#">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                                <div class="avatar avatar-lg  flex-shrink-0">
                                    <img src="{{ uploadedAsset('default','profile') }}" class="rounded-circle" alt="image" id="chat_avatar" data-userid="">
                                </div>
                                <div class="ms-2 overflow-hidden">
                                    <h6 class="chat-user-name"></h6>
                                    <span class="last-seen"></span>
                                </div>
                            </div>
                        </div>
                        <div class="chat-body chat-page-group admin-chat" id="messageContainer">
                            <div class="messages" id="messageArea">
                            </div>
                        </div>
                    </div>
                    <div class="p-3 d-flex admin-select">
                        <span class="selected_file border-0 rounded-pill text-light px-2 py-1 me-2 bg-dark d-none"></span>
                    </div>
                    <div class="chat-footer">
                        <div class="footer-form d-flex align-items-center">
                            <div class="form-item input-group">
                                <button type="button" class="btn-file btn" id="openFile">
                                    <i class="fa fa-paperclip"></i>
                                </button>
                                <input type="file" id="fileupload" class="d-none">
                                <input type="text" class="form-control me-3" id="messageinput" placeholder="{{ __('admin.others.type_your_message') }}">
                                <button class="btn btn-primary" type="button" id="sendmsg" data-senderid="{{ $sender->id }}">
                                    <i class="ti ti-send"></i>
                                </button> 
                            </div>                      
                        </div>
                    </div>
                </div>
                <!-- /Chat -->
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
@endsection 

@push('scripts')
<script src="{{ asset('/backend/assets/js/others/mqtt.min.js') }}"></script>
<script src="{{ asset('/backend/assets/js/others/messages.js?v=1.2') }}"></script>
@endpush