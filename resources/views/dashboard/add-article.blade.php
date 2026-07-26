@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between px-2">
            <span class="align-self-center">{{ __('New article') }}</span>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#aiWriterModal">
                <i class="fa-solid fa-microchip"></i>
                Write with AI
            </button>

        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.articles.add') }}" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Hidden Default Image --}}
                <input type="hidden" id="defaultImage" name="defaultImage"
                    value="{{ asset('images/articles/default.jpg') }}" />

                {{-- Title --}}
                <div class="row mb-2">
                    <label for="title" class="col-md-12">{{ __('Title') }}</label>
                    <div class="col-md-12 @error('title') has-error @enderror">
                        <input id="title" type="text" placeholder="Title"
                            class="form-control @error('title') is-invalid @enderror" name="title"
                            value="{{ old('title') }}" autocomplete="title" autofocus>
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Short Description --}}
                <div class="row mb-2">
                    <label for="short_description" class="col-md-12">{{ __('Short description') }}</label>
                    <div class="col-md-12 @error('short_description') has-error @enderror">
                        <input id="short_description" type="text" placeholder="Short description"
                            class="form-control @error('short_description') is-invalid @enderror" name="short_description"
                            value="{{ old('short_description') }}" autocomplete="short_description">
                        @error('short_description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Category --}}
                <div class="row mb-2">
                    <label for="category" class="col-md-12">{{ __('Category') }}</label>
                    <div class="col-md-12 @error('category_id') has-error @enderror">
                        <select name="category_id" id="category_id"
                            class="form-control @error('category_id') is-invalid @enderror">
                            <option value="0">Pick a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $category->id == old('category_id') ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Tags --}}
                <div class="row mb-3">
                    <label for="tagsSelector" class="col-md-12">{{ __('Tags') }}</label>
                    <div class="position-relative">
                        <span id="tagSelectorToggler" class="tag-toggler" onclick="toggleTagSelector(event)">
                            <i class="fas fa-chevron-up"></i>
                        </span>
                        <ul id="tagsList" class="form-control tags-list mb-1" onclick="toggleTagSelector(event)">
                            <li class="text-muted">Select one or more tags from the list</li>
                        </ul>
                    </div>
                    <div id="tagActions" class="tag-actions">
                        <input oninput="filterTags(event)" type="search" class="form-control mb-1"
                            placeholder="Filter available tags" />
                        <select name="tags[]" id="tags" class="form-control tag-select" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    {{ collect(old('tags'))->contains($tag->id) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Featured --}}
                <div class="row mb-3">
                    <div class="col-md-12 d-flex align-items-center switch-toggle">
                        <p class="switch-toggle-label mb-0 me-2">Featured article?</p>
                        <input class="mt-1" type="checkbox" id="featured" name="featured"
                            {{ old('featured') ? 'checked' : '' }}>
                        <label class="px-1" for="featured">{{ __('Toggle') }}</label>
                    </div>
                </div>

                {{-- Disable Comments --}}
                <div class="row mb-3">
                    <div class="col-md-12 d-flex align-items-center switch-toggle">
                        <p class="switch-toggle-label mb-0 me-2">Disable comments?</p>
                        <input class="mt-1" type="checkbox" id="disable_comments" name="disable_comments"
                            {{ old('disable_comments') ? 'checked' : '' }}>
                        <label class="px-1" for="disable_comments">{{ __('Toggle') }}</label>
                    </div>
                </div>

                {{-- Media Tabs --}}
                <ul class="nav nav-tabs mb-2" id="mediaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="image-tab" data-bs-toggle="tab" data-bs-target="#image-section"
                            type="button" role="tab" aria-controls="image-section" aria-selected="true">
                            Article Image
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video-section"
                            type="button" role="tab" aria-controls="video-section" aria-selected="false">
                            Article Video
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="mediaTabContent">
                    {{-- Image Section --}}
                    <div class="tab-pane fade show active" id="image-section" role="tabpanel"
                        aria-labelledby="image-tab">
                        <div class="row mb-2">
                            <label for="file" class="col-md-12">{{ __('Upload image') }}</label>
                            <div class="col-md-12 post-media @error('image') has-error @enderror">
                                <input type="file" name="image" id="file" class="file-upload-btn"
                                    onchange="previewImage(event)">
                                @error('image')
                                    <span class="invalid-feedback invalid-upload"
                                        role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="row my-3 d-none">
                            <div class="col-md-12 post-media text-center">
                                <img id="imagePreview" class="image-preview large"
                                    src="{{ asset('images/articles/default.jpg') }}">
                                <a class="remove-media" id="delete-image" href="#" title="Remove image"
                                    onclick="removeImage(event)">
                                    <i class="fa fa-remove"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Video Section --}}
                    <div class="tab-pane fade" id="video-section" role="tabpanel" aria-labelledby="video-tab">
                        <div class="row mb-2">
                            <label for="video-file" class="col-md-12">{{ __('Upload video') }}</label>
                            <div class="col-md-12 post-video @error('video') has-error @enderror">
                                <input type="file" name="video" id="video-file"
                                    class="file-upload-btn video-upload" onchange="validateVideo(event)">
                                @error('video')
                                    <span class="invalid-feedback invalid-upload"
                                        role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="my-3 post-media video d-none"></div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="row mb-3">
                    <label for="content" class="col-md-12 pt-2">{{ __('Content') }}</label>
                    <div class="col-md-12 @error('content') has-error @enderror">
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror"
                            placeholder="Content" cols="30" rows="6">{{ old('content') }}</textarea>
                        @error('content')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Publication & Expiration Dates --}}
                <div class="row mb-3">
                    {{-- Publish At --}}
                    <div class="col-md-6 pe-1 @error('published_at') has-error @enderror">
                        <label for="published_at">{{ __('Publish at') }}</label>
                        <input type="datetime-local" id="published_at" name="published_at"
                            class="form-control @error('published_at') is-invalid @enderror"
                            value="{{ old('published_at') }}">
                        @error('published_at')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Expire At --}}
                    <div class="col-md-6 ps-1 @error('expires_at') has-error @enderror">
                        <label for="expires_at">{{ __('Unpublish at') }}</label>
                        <input type="datetime-local" id="expires_at" name="expires_at"
                            class="form-control @error('expires_at') is-invalid @enderror"
                            value="{{ old('expires_at') }}">
                        @error('expires_at')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                {{-- Buttons (Submit & Cancel) --}}
                <div class="row mb-0">
                    <div class="col-md-12 d-flex">
                        <div class="w-50 pe-1">
                            <button type="submit" class="btn btn-success w-100">{{ __('Save') }}</button>
                        </div>
                        <div class="w-50 ps-1">
                            <a href="{{ route('dashboard.articles') }}"
                                class="btn btn-success w-100">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('partials.generate-ai-modal')
    @include('partials.media-preview-script')
    @include('partials.generate-ai-script')
@endsection
