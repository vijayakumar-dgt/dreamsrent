@extends('admin.admin')

@section('meta_title', __('admin.blog.add_blog') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Add Blogs -->
            <div class="add-blog-content">
                <div class="mb-4">
                    <a href="/admin/content/blogs" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-narrow-left me-1"></i>{{__('admin.blog.blogs')}}
                    </a>
                </div>
                <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5>{{__('admin.blog.add_blog')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{__('admin.blog.featured_image')}} <span class="text-danger">*</span></label>
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames p-2 preview-image-add">
                                                <img src="{{ asset('/backend/assets/img/default-image-02.jpg') }}" class="rounded-2 img-fluid" alt="Image Preview">
                                            </div>
                                            <div class="profile-upload">
                                                <div class="profile-uploader d-flex align-items-center">
                                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                                        <i class="ti ti-photo-up fs-14"></i>
                                                        {{__('admin.blog.upload')}}
                                                        <input type="file" id="featured_image_add" name="image" class="form-control image-sign" multiple="" required>
                                                    </div>
                                                    <span id="selectedFileNameAdd" class="fs-14 text-muted">{{__('admin.blog.no_file_chosen')}}</span>
                                                </div>
                                                <div class="mt-2">
                                                    <p class="fs-14">{{__('admin.blog.image_dimensions_must_be_exactly_735_310_pixels')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{__('admin.blog.title')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="blog_title" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{__('admin.blog.language')}} <span class="text-danger">*</span></label>
                                        <select class="select" id="blog_language" name="language_id">
                                            @foreach($languages as $language)
                                            <option value="{{ $language->language_id }}">
                                                {{ $language->transLang->name ?? 'N/A' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{__('admin.blog.category')}} <span class="text-danger">*</span></label>
                                        <select class="select" id="blog_category" name="category_id" required>
                                            @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{__('admin.blog.tags')}} <span class="text-danger">*</span></label>
                                        <select class="select" id="blog_tags" name="tag_id[]" multiple required>
                                            @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <label class="form-label">{{__('admin.blog.description')}} <span class="text-danger">*</span></label>
                                        <textarea id="editor" name="description" class="summernote form-control" required>
                                        {{ old('description', $blog->description ?? '') }}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</a>
                                <a href="javascript:void(0);" class="btn btn-primary" id="create_blog_btn">{{__('admin.blog.create_new')}}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Add Blogs -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/blog.js') }}"></script>
@endpush