@extends('backend.layouts.master')
@section('title', 'Assign paper to teacher')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">Assign paper to teacher</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Teacher</a></li>
                        <li class="breadcrumb-item active">Assign paper</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_body">
                    <div class="alert alert-danger">
                        <b>
                            <a href="{{ asset($written->question) }}" class="btn btn-warning">View Question</a>
                        </b>
                        <br>
                        @php
                            $subjects = DB::table('subjects')
                                ->whereIn('id', explode(',', $written->subject_id))
                                ->get();

                            $topic = DB::table('topic_sources')
                                ->whereIn('id', explode(',', $written->topic_id))
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
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th> <input type="checkbox" class="check_all"> All Check</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paper as $key => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="checkbox" name="written_answer_id[]"
                                                    value="{{ $item->id }}" class="custom_name">
                                                <span>{{ $item->user->registration_id }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mb-3">
                                <label class="form-label" for="inputAddress">Teacher <span
                                        class="text-danger">*</span></label>
                                <select name="teacher_id" class="form-control" id="" required>
                                    <option value="">Select</option>
                                    @foreach ($teacher as $s_item)
                                        <option value="{{ $s_item->id }}">{{ $s_item->name }}</option>
                                    @endforeach
                                </select>
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
            $('.check_all').click(function() {
                $('.custom_name').prop('checked', $(this).prop('checked'));
            });

            $('.custom_name').click(function() {
                var allChecked = $('.custom_name:checked').length === $('.custom_name').length;
                $('.check_all').prop('checked', allChecked);
            });
        });
    </script>
@endsection
