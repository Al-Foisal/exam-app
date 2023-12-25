@extends('backend.layouts.master')
@section('title',
    request()->written_id
    ? 'Update'
    : 'Create' .
    ' new ' .
    request()->ref .
    ' ' .
    request()->type .
    '
    exam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">{{ request()->written_id ? 'Update' : 'Create' }} new
                        {{ request()->child ?? request()->ref }}{{ ' ' . request()->type }} exam</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ request()->ref . ' ' . request()->type }}
                                exam</a></li>
                        <li class="breadcrumb-item active">{{ request()->written_id ? 'Update' : 'Create' }}</li>
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
                            action="{{ request()->written_id ? route('exam.writtenStoreOrUpdate', request()->written_id) : route('exam.writtenStoreOrUpdate') }}"
                            method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="category" value="{{ request()->ref }}">
                            <input type="hidden" name="subcategory" value="{{ request()->type }}">
                            <input type="hidden" name="childcategory" value="{{ request()->child }}">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <h6>Select subject</h6>
                                    <div style="max-height:200px;overflow:auto;">
                                        @foreach ($subjects as $subject)
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="{{ $subject->id }}"
                                                    onchange="getSelectedsubject(this, '{{ $subject->id }}')"
                                                    name="subject_id[]" data-url="{{ route('exam.getTopic') }}"
                                                    {{ isset($exam) && in_array($subject->id, explode(',', $exam->subject_id)) ? 'checked' : '' }}
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
                                            @if (isset($exam))
                                                @foreach ($topic_source as $key => $source)
                                                    <div class="mb-3 form-check"> <input type="checkbox"
                                                            class="form-check-input" id="{{ $source->id . $key }}"
                                                            name="topic_id[]"
                                                            {{ isset($exam) && in_array($source->id, explode(',', $exam->topic_id)) ? 'checked' : '' }}
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
                            <div class="row mb-3">
                                <div class=" col-md-6 mb-3">
                                    <label class="form-label" for="inputZip">Duration (minutes)<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="inputZip" name="duration"
                                        placeholder="30" value="{{ isset($exam) ? $exam->duration / 60 : '' }}">
                                </div>
                                <div class=" col-md-6 mb-3">
                                    <label class="form-label" for="inputZip">Pass Marks<span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="inputZip"
                                        name="pass_marks" placeholder="1"
                                        value="{{ isset($exam) ? $exam->pass_marks : '' }}" required>
                                </div>
                                <div class=" col-md-6 mb-3">
                                    <label class="form-label" for="inputCity">Published date & time<span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="inputCity" name="published_at"
                                        value="{{ isset($exam) ? $exam->published_at : '' }}">
                                </div>
                                <div class=" col-md-6">
                                    <label class="form-label" for="inputCity">Expired date & time<span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="inputCity" name="expired_at"
                                        value="{{ isset($exam) ? $exam->expired_at : '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class=" col-md-6">
                                    <label class="form-label" for="inputZip">Question Paper (PDF)</label>
                                    <input type="file" class="form-control" id="" name="question"
                                        accept=".pdf">

                                    @if (isset($exam) && $exam->question)
                                        <a target="_blank" href="{{ asset($exam->question) }}">View Question</a>
                                    @else
                                        No question set yet
                                    @endif

                                </div>
                                <div class=" col-md-6">
                                    <label class="form-label" for="inputZip">Question Answer (PDF)</label>
                                    <input type="file" class="form-control" id="" name="answer"
                                        accept=".pdf">
                                    @if (isset($exam) && $exam->answer)
                                        <a target="_blank" href="{{ asset($exam->answer) }}">View Answer</a>
                                    @else
                                        No answer set yet
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    Status
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="status" value="1" id="active"
                                        class="form-check-input" required
                                        {{ isset($exam) && $exam->status == 1 ? 'checked' : '' }}>
                                    <label for="active">Active</label>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="status" value="0" id="inactive"
                                        class="form-check-input" required
                                        {{ isset($exam) && $exam->status == 0 ? 'checked' : '' }}>
                                    <label for="inactive">Inctive</label>
                                </div>
                            </div>
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
