@extends('backend.layouts.master')
@section('title', 'Manage question for ' . $exam->name)
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">Manage question for "{{ $exam->name }}"</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
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
                            <b>Per question negative mark: </b>{{ $exam->per_question_negative_mark }}
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <h2 class="text-center"><u>Manage question according to subject</u></h2>
                            <br>
                            <br>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <div class="card_box box_shadow position-relative">
                                        <div class="white_box_tittle" style="padding: 20px;">
                                            <div class="d-flex justify-content-between">
                                                <h4>Basic Card</h4>
                                                <button class="btn btn-primary">Basic Card</button>
                                            </div>
                                        </div>
                                        <div class="box_body">
                                            <p class="f-w-400 ">Lorem Ipsum is simply dummy text of the printing and
                                                typesetting industry. Lorem Ipsum has been the industry's standard dummy
                                                text ever since the 1500s, when an unknown printer took a galley of type and
                                                scrambled. Lorem Ipsum is simply dummy text of the printing and typesetting
                                                industry. Lorem Ipsum has been the industry's standard dummy text ever since
                                                the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                                        </div>
                                    </div>
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
