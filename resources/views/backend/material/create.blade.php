@extends('backend.layouts.master')
@section('title', request()->material_id ? 'Update' : 'Create' . ' new ' . request()->ref . ' ' . ' exam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">{{ request()->material_id ? 'Update' : 'Create' }} new
                        {{ request()->child ?? request()->ref }}{{ ' ' }} material</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ request()->ref . ' ' }}
                                material</a></li>
                        @if (request()->child)
                            <li class="breadcrumb-item"><a href="javascript:void(0);">{{ request()->child . ' ' }}
                                    material</a></li>
                        @endif
                        <li class="breadcrumb-item active">{{ request()->material_id ? 'Update' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_body">
                    <div class="card-body">
                        <form
                            action="{{ request()->material_id ? route('material.storeOrUpdate', request()->material_id) : route('material.storeOrUpdate') }}"
                            method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="category" value="{{ request()->ref }}">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="inputAddress">Subjects <span
                                            class="text-danger">*</span></label>
                                    <select name="subject_id[]" class="form-control" multiple="multiple" id="subjects"
                                        required data-placeholder="Select" data-url="{{ route('material.getTopic') }}">
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ isset($material) && in_array($subject->id, explode(',', $material->subject_id)) ? 'selected' : '' }}>
                                                {{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="inputAddress">Topics & Sources <span
                                            class="text-danger">*</span></label>
                                    <select name="topic_id[]" class="form-control " multiple="multiple" id="topics"
                                        required data-placeholder="Select">
                                        @if (isset($material))
                                            @foreach ($topic_source as $source)
                                                <option value="{{ $source->id }}"
                                                    {{ isset($material) && in_array($source->id, explode(',', $material->topic_id)) ? 'selected' : '' }}>
                                                    {{ $source->topic }} - ({{ $source->source }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Material Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Enter material name"
                                    value="{{ $material->name ?? '' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">What student will learn<span class="text-danger">*</span></label>
                                <textarea name="details" class="form-control summernote" rows="10" required>{!! $material->details ?? '' !!}</textarea>
                            </div>

                            @if (request()->ref !== 'Record Class')
                                <div class="form-group mb-3">
                                    <label class="form-label">Material PDF<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="pdf"
                                        placeholder="Enter material name" {{ request()->material_id ? '' : 'required' }}>
                                    @if (isset($material) && $material->pdf)
                                        <a href="{{ asset($material->pdf ?? '') }}" target="_blank">PDF</a>
                                    @endif
                                </div>
                            @else
                                <div class="form-group mb-3">
                                    <label class="form-label">Video Link<span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" name="video"
                                        placeholder="Enter material video link" value="{{ $material->video ?? '' }}"
                                        {{ request()->material_id ? '' : 'required' }}>


                                    @if (isset($material) && $material->video != null)
                                        <iframe width="560" height="315" src="{{ $material->video }}"
                                            title="YouTube video player" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen></iframe>
                                    @endif
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#subjects').on('change', function() {
                var subjects = $(this).val();
                var url = $(this).data('url');

                $.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data: {
                        subjects
                    },
                    success: function(data) {
                        $('#topics').empty();
                        $.each(data, function(key, value) {
                            $('#topics').append(
                                '<option value="' + value.id + '">' + value.topic +
                                ' - (' + value.source + ')</option>'
                            )
                        });
                    }
                });
            });
        });
    </script>
@endsection
