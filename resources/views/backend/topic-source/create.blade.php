@extends('backend.layouts.master')
@section('title', 'Create new topic and source')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">Create new topic and source</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Topic & Source</a></li>
                        <li class="breadcrumb-item active">Create</li>
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
                        <form action="{{ route('topic.source.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="inputAddress">Subject <span
                                        class="text-danger">*</span></label>
                                <select name="subject_id" class="form-control" id="" required>
                                    <option value="">Select</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="inputAddress">Topic <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="Enter topic here"
                                    name="topic">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="inputAddress">Source <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputAddress" placeholder="Enter source here"
                                    name="source">
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
