@extends('admin.admin')

@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content me-0 me-md-0 me-lg-4">

		<!-- Blogs Details -->
		<div class="row">
			<div class="col-md-10 offset-md-1">
				<div class="blog-details">
					<div>
						<a href="/admin/content/blogs" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-narrow-left me-1"></i>{{__('admin.blog.back_to_blogs')}}</a>
					</div>
					<h3>{{$blogPosts->title}}</h3>
					<div class="blog-details-1">
						<img src="{{asset ('/storage/'.$blogPosts->image)}}" alt="img" class="w-100 rounded-3">
					</div>
					<div class="d-flex align-items-center flex-wrap gap-2">
						<h6 class="me-2">{{ __('admin.blog.tags') }}: </h6>
						@php
						$tagIds = is_array($blogPosts->tags) ? $blogPosts->tags : json_decode($blogPosts->tags, true);
						$tagNames = \Modules\GeneralSetting\Models\BlogTag::whereIn('id', $tagIds)->pluck('name');
						@endphp
						@foreach($tagNames as $tagName)
						<span class="badge badge-blog-details badge-md me-2">{{ $tagName }}</span>
						@endforeach
					</div>

					<p>{{$blogPosts->description}}</p>

				</div>
			</div>
		</div>
		<!-- Blogs Details -->

	</div>
	@include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

@endsection