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
                                    <label class="form-label" for="inputAddress">Subjects <span
                                            class="text-danger">*</span></label>
                                    <select name="subject_id[]" class="form-control" multiple="multiple" id="subjects"
                                        required data-placeholder="Select" data-url="{{ route('exam.getTopic') }}">
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ isset($exam) && in_array($subject->id, explode(',', $exam->subject_id)) ? 'selected' : '' }}>
                                                {{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="inputAddress">Topics & Sources <span
                                            class="text-danger">*</span></label>
                                    <select name="topic_id[]" class="form-control " multiple="multiple" id="topics"
                                        required data-placeholder="Select">
                                        @if (isset($exam))
                                            @foreach ($topic_source as $source)
                                                <option value="{{ $source->id }}"
                                                    {{ isset($exam) && in_array($source->id, explode(',', $exam->topic_id)) ? 'selected' : '' }}>
                                                    {{ $source->topic }} - ({{ $source->source }})</option>
                                            @endforeach
                                        @endif
                                    </select>
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
