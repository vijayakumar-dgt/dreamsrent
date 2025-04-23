@extends('admin.admin')

@section('content')


<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 me-md-0 me-lg-4">

        <!-- Edit Blogs -->
        <div class="add-blog-content">
            <div class="mb-4">
                <a href="/admin/content/blogs" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-narrow-left me-1"></i>{{__('admin.blog.blogs')}}</a>
            </div>
            <form id="editBlogForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="blog_id" value="{{ $blog->id }}">

                <div class="card">
                    <div class="card-header">
                        <h5>{{__('admin.blog.edit_blog')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Featured Image -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{__('admin.blog.featured_image')}} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames p-2">
                                            <img src="{{ asset('/storage/'.$blog->image) }}" class="rounded-2 img-fluid preview-image" alt="brands">

                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{__('admin.blog.upload')}}
                                                    <input type="file" name="image" class="form-control image-sign" id="imageInput">
                                                </div>
                                                <span id="selectedFileName" class="fs-14 text-muted">{{__('admin.blog.no_file_chosen')}}</span>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{__('admin.blog.image_dimensions_must_be_exactly_735_310_pixels')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label">{{__('admin.blog.title')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $blog->title }}">
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('admin.blog.category')}} <span class="text-danger">*</span></label>
                                    <select class="select form-control" id="blog_category" name="category_id">
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ (int) old('category', $blog->category) === (int) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('admin.blog.tags')}} <span class="text-danger">*</span></label>
                                    <select class="select form-control" id="blog_tags" name="tag_id[]" multiple>
                                        @php
                                        $selectedTags = is_array($blog->tags) ? $blog->tags : json_decode($blog->tags, true);
                                        @endphp

                                        @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags ?? []) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <label class="form-label">{{__('admin.blog.description')}}</label>
                                    <textarea id="editor" name="description" class="summernote form-control">{{ $blog->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                            <div class="form-check form-check-md form-switch me-2">
                                <label class="form-check-label form-label mt-0 mb-0">
                                    <input class="form-check-input form-label me-2" name="status" type="checkbox" role="switch"
                                        {{ (int) $blog->status == 1 ? 'checked' : '' }}>
                                    {{__('admin.blog.status')}}
                                </label>

                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</button>
                                <button type="button" id="saveBlogBtn" class="btn btn-primary">{{__('admin.blog.save_changes')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- Edit Blogs -->

    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

@endsection

@push('scripts')
<script src="{{ asset('assets/js/general_setting/blog.js') }}"></script>

@endpush