@extends('backend.layouts.master')
@section('title', 'Manage question for ' . $exam->name)
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border: none;
        }

        .note-modal-backdrop {
            z-index: 1;
        }
    </style>


@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">Manage question</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Question</a></li>
                        <li class="breadcrumb-item active">{{ request()->exam_id ? 'Update' : 'Create' }}</li>
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
                        <div class="alert alert-danger">
                            <b>
                                <i>{{ $exam->category }}</i> > <i>{{ $exam->subcategory }}</i>
                                @if ($exam->childcategory)
                                    > {{ $exam->childcategory }}
                                @endif
                            </b>
                            <br>
                            @php
                                $subjects = DB::table('subjects')
                                    ->whereIn('id', explode(',', $exam->subject_id))
                                    ->get();

                                $topic = DB::table('topic_sources')
                                    ->whereIn('id', explode(',', $exam->topic_id))
                                    ->get();
                            @endphp
                            <b>Subjects:</b>
                            <div class="ms-5">
                                <ul>
                                    @foreach ($subjects as $subject)
                                        <li style="list-style-type: circle;color: rgb(17, 0, 255);">{{ $subject->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <br>
                            <b>Topics & Sources:</b>
                            <div class="ms-5">
                                <ul>
                                    @foreach ($topic as $top)
                                        <li style="list-style-type: dot;color: rgb(17, 0, 255);">{{ $top->topic }} -
                                            ({{ $top->source }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <br>
                            <b>Per question positive mark: </b>{{ $exam->per_question_positive_mark }} <br>
                            <b>Per question negative mark: </b>{{ $exam->per_question_negative_mark }} <br>
                            <b>Published Date: </b>{{ $exam->published_at->format('d-m-Y h:i A') }} <br>
                            <b>Expired Date: </b>{{ $exam->expired_at->format('d-m-Y H:i A') }}
                        </div>
                        @csrf
                        <h2 class="text-center"><u>Manage question according to subject</u></h2>
                        <br>
                        <br>
                        <br>
                        @if (isset($subjects))
                            @foreach ($subjects as $q_subject)
                                @php
                                    $present_question = App\Models\ExamQuestion::where('exam_id', request()->exam_id)
                                        ->where('subject_id', $q_subject->id)
                                        ->get();
                                @endphp
                                <div class="row justify-content-center mb-5">
                                    <div class="col-lg-10">
                                        <div class="card_box box_shadow position-relative">
                                            <div class="white_box_tittle" style="padding: 20px;">
                                                <div class="d-flex justify-content-between">
                                                    <h4>{{ $q_subject->name }}</h4>

                                                </div>
                                            </div>

                                            <form action="{{ route('exam.createOrUpdateMCQQuestion', $exam->id) }}"
                                                method="post" enctype="multipart/form-data">
                                                @csrf

                                                <div class="box_body">
                                                    {{-- existing question here to update --}}
                                                    @foreach ($present_question as $question)
                                                        @php
                                                            $present_serial = $loop->iteration;
                                                        @endphp
                                                        <div class="mb-2">
                                                            <div class="alert alert-success" style="margin-bottom: 1px;">
                                                                <div class="d-flex justify-content-between">
                                                                    <h4>Question Number #
                                                                        <span>{{ $loop->iteration }}</span>
                                                                    </h4>
                                                                    <div class="d-flex justify-content-end">
                                                                        <i class="fas fa-minus-circle fa-lg"
                                                                            style="padding-top: 8px;display: none;cursor: pointer;"
                                                                            onclick="closeQuestion(this)"
                                                                            title="Collapse"></i>
                                                                        <i class="fas fa-plus-circle fa-lg"
                                                                            style="padding-top: 8px;cursor: pointer;"
                                                                            onclick="openQuestion(this)" title="Expand"></i>
                                                                        <i class="fas fa-times-circle fa-lg ms-2 text-danger"
                                                                            style="padding-top: 8px;cursor: pointer;"
                                                                            onclick="deleteQuestion(this)"
                                                                            title="Delete this question"
                                                                            data-url="{{ route('exam.deleteQuestion', $question->id) }}"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body collaps-question bg-warning"
                                                                style="display: none;">
                                                                <input type="hidden" name="subject_id"
                                                                    value="{{ $q_subject->id }}">
                                                                {{-- <input type="hidden" class="serial_number"
                                                                    name="serial_number[]" value="{{ $present_serial }}"> --}}
                                                                <input type="hidden" name="exam_id"
                                                                    value="{{ request()->exam_id }}">
                                                                <input type="hidden" name="question_id[]"
                                                                    value="{{ $question->id }}">

                                                                <div class="col-md-12">
                                                                    <label for="">Question Name</label>
                                                                    <textarea class="summernote11" placeholder="Enter question name here" name="question_name_{{ $question->id }}">{!! $question->question_name !!}</textarea>
                                                                </div>

                                                                <div class="col-md-12 mt-2">
                                                                    <div class="card card-outline card-info"
                                                                        style="border-radius: 5px;">
                                                                        <div class="card-header">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <h3 class="card-title">
                                                                                        Options with Answer
                                                                                    </h3>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- /.card-header -->
                                                                        <div class="p-5">
                                                                            <div class="form-group clearfix">

                                                                                @foreach ($question->questionOptions as $option_key => $option)
                                                                                    <div
                                                                                        class="d-flex justify-content-start">
                                                                                        <div
                                                                                            class="icheck-success d-inline">
                                                                                            <input type="radio"
                                                                                                @if ($option->is_answer == 1) {{ 'checked' }} @endif
                                                                                                name="question_option_{{ $question->id }}"
                                                                                                value="{{ $option_key }}">
                                                                                            <label for="is_answer">
                                                                                            </label>
                                                                                        </div>
                                                                                        <textarea class="summernote11" name="question_option_name_{{ $question->id }}[]">
                                                                                        {!! $option->option !!}
                                                                                        </textarea>
                                                                                    </div>
                                                                                    <br>
                                                                                    <br>
                                                                                @endforeach

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <label for="">Question Explanation</label>
                                                                    <textarea class="summernote11" name="question_explanation_{{ $question->id }}"
                                                                        placeholder="Enter question explanation here">{!! $question->question_explanation !!}</textarea>
                                                                </div>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    {{-- add multiple question from here --}}
                                                    <div id="subject_{{ $q_subject->id }}">
                                                    </div>

                                                    <button type="submit" class="btn btn-primary "
                                                        id="question-submit-button-{{ $q_subject->id }}"
                                                        @if (count($present_question) > 0) style="display: block;" @else style="display: none;" @endif>Update
                                                        or Create</button>

                                                </div>
                                            </form>

                                            <div class="card-footer" style="text-align: right;">

                                                <button class="btn btn-primary" type="button"
                                                    onclick="addAnotherQuestion(this, '{{ $q_subject->id }}')"
                                                    data-serial_number="{{ count($present_question) > 0 ? 1 + $present_serial : 1 }}">{{ count($present_question) > 0 ? 'Add Another Question' : 'Add Question' }}</button>
                                                <div class="add-more-question" style="display: none;">

                                                    {{-- below html div will add --}}
                                                    {{-- <div class="mb-2">
                                                        <div class="alert alert-success" style="margin-bottom: 1px;">
                                                            <div class="d-flex justify-content-between">
                                                                <h4>Question Number # <span
                                                                        class="serial_number_{{ $q_subject->id }}"></span>
                                                                </h4>
                                                                <div class="d-flex justify-content-end">
                                                                    <i class="fas fa-minus-circle fa-lg"
                                                                        style="padding-top: 8px;display: none;cursor: pointer;"
                                                                        onclick="closeQuestion(this)"
                                                                        title="Collapse"></i>
                                                                    <i class="fas fa-plus-circle fa-lg"
                                                                        style="padding-top: 8px;cursor: pointer;"
                                                                        onclick="openQuestion(this)" title="Expand"></i>
                                                                    <i class="fas fa-times-circle fa-lg ms-2 text-danger"
                                                                        style="padding-top: 8px;cursor: pointer;"
                                                                        onclick="removeQuestion(this)" title="Remove"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body collaps-question bg-warning"
                                                            style="display: none;">
                                                            <input type="hidden" name="subject_id"
                                                                value="{{ $q_subject->id }}">
                                                            <input type="hidden" class="serial_number">
                                                            <input type="hidden" name="exam_id"
                                                                value="{{ request()->exam_id }}">

                                                            <div class="col-md-12">
                                                                <label for="">Question Name</label>
                                                                <textarea class="question_name summernote11" placeholder="Enter question name here" name="" rows="5"
                                                                    style="width: 100%;"></textarea>
                                                            </div>

                                                            <div class="col-md-12 mt-2">
                                                                <div class="card card-outline card-info"
                                                                    style="border-radius: 5px;">
                                                                    <div class="card-header">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <h3 class="card-title">
                                                                                    Options with Answer
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /.card-header -->
                                                                    <div class="p-5">
                                                                        <div class="form-group">
                                                                            @for ($i = 0; $i < 4; $i++)
                                                                                <div class="d-flex justify-content-start">
                                                                                    <div class="icheck-success d-inline">
                                                                                        <input type="radio"
                                                                                            class="question_option"
                                                                                            value="{{ $i }}"
                                                                                            @if ($i == 0) {{ 'checked' }} @endif>
                                                                                        <label
                                                                                            for="is_answer_{{ $q_subject->id }}_{{ $i }}">
                                                                                        </label>
                                                                                    </div>
                                                                                    <textarea class="question_option_name " rows="2" style="width: 100%;"></textarea>
                                                                                </div>
                                                                                <br>
                                                                                <br>
                                                                            @endfor

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label for="">Question Explanation</label> <br>
                                                                <textarea rows="5" style="width: 100%;" class="question_explanation "
                                                                    placeholder="Enter question explanation here"></textarea>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script> --}}
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.9.0/katex.min.js"></script>
    <script src="{{ asset('summernote-math.js') }}"></script>

    <script>
        function addAnotherQuestion(e, subject_id) {
            var serial_number = parseInt($(e).data("serial_number"));


            var data = '';
            data +=
                '<div class="mb-2"><div class="alert alert-success" style="margin-bottom: 1px;"><div class="d-flex justify-content-between"><h4>Question Number # <span>' +
                serial_number +
                '</span> </h4> <div class="d-flex justify-content-end"> <i class="fas fa-minus-circle fa-lg" style="padding-top: 8px;display: none;cursor: pointer;" onclick="closeQuestion(this)" title="Collapse"></i> <i class="fas fa-plus-circle fa-lg" style="padding-top: 8px;cursor: pointer;" onclick="openQuestion(this)" title="Expand"></i> <i class="fas fa-times-circle fa-lg ms-2 text-danger" style="padding-top: 8px;cursor: pointer;" onclick="removeQuestion(this)" title="Remove"></i> </div> </div> </div> <div class="card-body collaps-question bg-warning" style="display: none;"> <input type="hidden" name="subject_id" value="' +
                subject_id + '"> <input type="hidden" class="serial_number" name="serial_number[]" value="' +
                serial_number +
                '"> <input type="hidden" name="exam_id" value="{{ request()->exam_id }}"> <div class="col-md-12"> <label for="">Question Name</label> <textarea class="question_name summernote11" placeholder="Enter question name here" name="question_name[]" rows="5" style="width: 100%;"></textarea> </div> <div class="col-md-12 mt-2"> <div class="card card-outline card-info" style="border-radius: 5px;"> <div class="card-header"> <div class="row"> <div class="col-md-6"> <h3 class="card-title"> Options with Answer </h3> </div> </div> </div> <div class="p-5"> <div class="form-group"> @for ($i = 0; $i < 4; $i++) <div class="d-flex justify-content-start"> <div class="icheck-success d-inline"> <input type="radio" class="question_option" name="question_option_' +
                subject_id +
                serial_number +
                '" value="{{ $i }}" @if ($i == 0) {{ 'checked' }} @endif> <label for="is_answer_{{ $q_subject->id }}_{{ $i }}"> </label> </div> <textarea class="question_option_name summernote11" name="question_option_name_' +
                subject_id +
                serial_number +
                '[]" rows="2" style="width: 100%;"></textarea> </div> <br> <br> @endfor </div> </div> </div> </div> <div class="col-md-12"> <label for="">Question Explanation</label> <br> <textarea rows="5" style="width: 100%;" class="question_explanation summernote11" placeholder="Enter question explanation here" name="question_explanation[]"></textarea> </div> <hr> </div> </div>';
            $("#subject_" + subject_id).append(data);
            $("#question-submit-button-" + subject_id).show();
            // $(e).parent().find(".question_name").addClass('summernote');

            $(e).data('serial_number', ++serial_number);
            // $('.summernote11').summernote({focus:true});
            $('.summernote11').summernote({
                height: 100,
                toolbar: [
                    ['fontsize', ['10', '25']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['insert', ['picture', 'link', 'math']],
                    ['para', ['paragraph']],
                    ['misc', ['codeview']]
                ],
            });
            // $('.').summernote({
            //     height: 100,
            //     width: 1000,
            //     toolbar: [
            //         ['fontsize', ['10', '25']],
            //         ['style', ['bold', 'italic', 'underline', 'clear']],
            //         ['insert', ['picture', 'link', 'math']],
            //         ['para', ['paragraph']],
            //         ['misc', ['codeview']]
            //     ],
            // });


        }

        function openQuestion(e) {
            $(e).parent().parent().parent().parent().find('.collaps-question').show();

            $(e).parent().find('.fa-minus-circle').show();
            $(e).parent().find('.fa-plus-circle').hide();
        }

        function closeQuestion(e) {
            $(e).parent().parent().parent().parent().find('.collaps-question').hide();

            $(e).parent().find('.fa-minus-circle').hide();
            $(e).parent().find('.fa-plus-circle').show();
        }

        function removeQuestion(e) {
            $(e).parent().parent().parent().parent().remove();
        }

        function deleteQuestion(e) {
            var url = $(e).data('url');

            $.ajax({
                method: 'get',
                url: url,
                cache: false,
                success: function(response) {

                    if (response.status == true) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Question deleted successfully'
                        });
                        $(e).parent().parent().parent().parent().remove();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something went wrong! Please try again.'
                        })
                    }

                },
                async: false,
                error: function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong! Please try again.'
                    })
                }
            });
        }

        $('.summernote11').summernote({
            height: 100,
            toolbar: [
                ['fontsize', ['10', '25']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['insert', ['picture', 'link', 'math']],
                ['para', ['paragraph']],
                ['misc', ['codeview']]
            ],
        });
    </script>
@endsection
