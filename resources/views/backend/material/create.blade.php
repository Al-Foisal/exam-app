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
                                    <h6>Select subject</h6>
                                    <div style="max-height:200px;overflow:auto;">
                                        @foreach ($subjects as $subject)
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="{{ $subject->id }}"
                                                    onchange="getSelectedsubject(this, '{{ $subject->id }}')"
                                                    name="subject_id[]" data-url="{{ route('exam.getTopic') }}"
                                                    {{ isset($material) && in_array($subject->id, explode(',', $material->subject_id)) ? 'checked' : '' }}
                                                    value="{{ $subject->id }}">
                                                <label class="form-check-label" for="{{ $subject->id }}">
                                                    {{ $subject->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Selected topic and subject</h6>
                                    <div style="max-height:200px;overflow:auto;">
                                        <div id="selectedTS">
                                            @if (isset($material))
                                                @foreach ($topic_source as $key => $source)
                                                    <div class="mb-3 form-check"> <input type="checkbox"
                                                            class="form-check-input" id="{{ $source->id . $key }}"
                                                            name="topic_id[]"
                                                            {{ isset($material) && in_array($source->id, explode(',', $material->topic_id)) ? 'checked' : '' }}
                                                            value="{{ $source->id }}">
                                                        <label class="form-check-label" for="{{ $source->id . $key }}">
                                                            {{ $source->topic }} - ({{ $source->source }})
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
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
        var subject = [];

        function getSelectedsubject(e, subject_id) {
            var url = $(e).data('url');

            if ($(e).is(":checked")) {
                subject.push(subject_id);
            } else {
                subject = $.grep(subject, function(n) {
                    return n != subject_id;
                });
            }

            $.ajax({
                type: 'post',
                url: url,
                dataType: 'json',
                data: {
                    subjects: subject
                },
                success: function(data) {
                    console.log(data);
                    $('#selectedTS').empty();
                    $.each(data, function(key, value) {
                        $('#selectedTS').append(
                            '<div class="mb-3 form-check"> <input type="checkbox" class="form-check-input" id="' +
                            value.id + key +
                            '" name="topic_id[]"> <label class="form-check-label" for="' + value
                            .id + key + '"> ' + value.topic + '(' + value.source +
                            ') </label> </div>'
                        )
                    });
                }
            });

        }
    </script>
@endsection
