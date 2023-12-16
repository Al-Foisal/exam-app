<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\TopicSource;
use App\Models\Written;
use App\Models\WrittenQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExamController extends Controller {
    public function index() {
        $data = [];
        $exam = Exam::where('category', request()->ref)
            ->where('subcategory', request()->type);

        if (request()->child) {
            $exam = $exam->where('childcategory', request()->child);
        }

        $exam = $exam->withCount('questions')->latest('published_at')
            ->paginate(20);
        $list = [];

        foreach ($exam as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
            $item['topics']   = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            $list[]           = $item;
        }

        $data['exam'] = $list;

        return view('backend.exam.index', $data);
    }

    public function create($exam_id = null) {
        $data             = [];
        $data['subjects'] = Subject::all();

        if ($exam_id) {
            $data['exam']         = $e         = Exam::find($exam_id);
            $data['topic_source'] = TopicSource::whereIn('subject_id', explode(',', $e->subject_id))->get();
        }

        return view('backend.exam.create', $data);
    }

    public function storeOrUpdate(Request $request, $exam_id = null) {

        if (!$exam_id) {
            Exam::create([
                'category'                   => $request->category,
                'subcategory'                => $request->subcategory,
                'childcategory'              => $request->childcategory,
                'subject_id'                 => implode(',', $request->subject_id),
                'topic_id'                   => implode(',', $request->topic_id),
                'per_question_positive_mark' => $request->per_question_positive_mark,
                'per_question_negative_mark' => $request->per_question_negative_mark,
                'published_at'               => $request->published_at,
                'expired_at'                 => $request->expired_at,
                'duration'                   => ($request->duration * 60),
                'pass_marks'                 => $request->pass_marks,
            ]);
        } else {
            $exam                             = Exam::find($exam_id);
            $exam->category                   = $request->category;
            $exam->subcategory                = $request->subcategory;
            $exam->childcategory              = $request->childcategory;
            $exam->subject_id                 = implode(',', $request->subject_id);
            $exam->topic_id                   = implode(',', $request->topic_id);
            $exam->per_question_positive_mark = $request->per_question_positive_mark;
            $exam->per_question_negative_mark = $request->per_question_negative_mark;
            $exam->published_at               = $request->published_at;
            $exam->expired_at                 = $request->expired_at;
            $exam->duration                   = ($request->duration * 60);
            $exam->pass_marks                 = $request->pass_marks;
            $exam->save();
        }

        return back()->withToastSuccess('Exam created successfully');

    }

    public function mcqQuestion($exam_id) {
        $data         = [];
        $data['exam'] = $exam = Exam::where('id', $exam_id)->first();

        return view('backend.exam.mcq-question', $data);
    }

    public function createOrUpdateMCQQuestion(Request $request) {
        // dd($request->all());
        DB::beginTransaction();

        try {
            $subject_id = $request->subject_id;

            if (isset($request->question_id) && count($request->question_id) > 0) {

                foreach ($request->question_id as $question_id) {
                    $update_question                       = ExamQuestion::find($question_id);
                    $update_question->question_name        = $request->input('question_name_' . $question_id);
                    $update_question->question_explanation = $request->input('question_explanation_' . $question_id);
                    $update_question->save();

                    $update_option = $request->input('question_option_name_' . $question_id);
                    DB::table('exam_question_options')->where('exam_question_id', $question_id)->delete();

                    if ($update_option) {

                        foreach ($update_option as $u_key => $option) {

                            if ($option) {

                                if ($request->input('question_option_' . $question_id) == $u_key) {
                                    $answer = 1;
                                } else {
                                    $answer = 0;
                                }

                                ExamQuestionOption::create([
                                    'exam_question_id' => $update_question->id,
                                    'option'           => $option,
                                    'is_answer'        => $answer,
                                ]);
                            }

                        }

                    }

                }

            }

            if (isset($request->serial_number) && count($request->serial_number) > 0) {

                foreach ($request->serial_number as $key => $serial_number) {
                    $postfix = $request->input('question_option_name_' . $subject_id . $serial_number);

                    if ($request->question_name[$key] != null && $postfix != null) {
                        $question = ExamQuestion::create([
                            'exam_id'              => $request->exam_id,
                            'subject_id'           => $subject_id,
                            'question_name'        => $request->question_name[$key],
                            'question_explanation' => $request->question_explanation[$key],
                        ]);

                        if ($postfix != null) {

                            foreach ($postfix as $o_key => $option) {

                                if ($request->input('question_option_' . $subject_id . $serial_number) == $o_key) {
                                    $answer = 1;
                                } else {
                                    $answer = 0;
                                }

                                ExamQuestionOption::create([
                                    'exam_question_id' => $question->id,
                                    'option'           => $option ?? 'Not set yet',
                                    'is_answer'        => $answer,
                                ]);

                            }

                        }

                    }

                }

            }

            DB::commit();

            return back()->withToastSuccess('Question updated or created successfully');

        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->withToastError($th->getMessage());
        }

    }

    public function deleteQuestion($question_id) {
        $data = ExamQuestion::find($question_id);

        if ($data) {

            foreach ($data->questionOptions as $option) {
                $option->delete();
            }

            $data->delete();

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false]);
        }

    }

    //written question from here
    public function written() {
        $data = [];
        $exam = Written::where('category', request()->ref)->where('subcategory', request()->type)->latest('published_at')->paginate(20);
        $list = [];

        foreach ($exam as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
            $item['topics']   = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            $list[]           = $item;
        }

        $data['exam'] = $list;

        return view('backend.exam.written', $data);
    }

    public function writtenCreate($exam_id = null) {
        $data             = [];
        $data['subjects'] = Subject::all();

        if ($exam_id) {
            $data['exam']         = $e         = Written::find($exam_id);
            $data['topic_source'] = TopicSource::whereIn('subject_id', explode(',', $e->subject_id))->get();
        }

        return view('backend.exam.written-create', $data);
    }

    public function writtenStoreOrUpdate(Request $request, $exam_id = null) {

        if (!$exam_id) {

            if ($request->hasFile('question')) {

                $image_file = $request->file('question');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/written/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name = $img_gen . '.' . $image_ext;
                    $question = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            if ($request->hasFile('answer')) {

                $image_file = $request->file('answer');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/written/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name = $img_gen . '.' . $image_ext;
                    $answer   = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            Written::create([
                'category'      => $request->category,
                'subcategory'   => $request->subcategory,
                'childcategory' => $request->childcategory,
                'subject_id'    => implode(',', $request->subject_id),
                'topic_id'      => implode(',', $request->topic_id),
                'published_at'  => $request->published_at,
                'expired_at'    => $request->expired_at,
                'pass_marks'    => $request->pass_marks,
                'duration'      => ($request->duration * 60),
                'question'      => $question ?? '',
                'answer'        => $answer ?? '',
            ]);
        } else {
            $exam                = Written::find($exam_id);
            $exam->category      = $request->category;
            $exam->subcategory   = $request->subcategory;
            $exam->childcategory = $request->childcategory;
            $exam->subject_id    = implode(',', $request->subject_id);
            $exam->topic_id      = implode(',', $request->topic_id);
            $exam->published_at  = $request->published_at;
            $exam->expired_at    = $request->expired_at;
            $exam->pass_marks    = $request->pass_marks;
            $exam->duration      = ($request->duration * 60);
            $exam->save();

            if ($request->hasFile('question')) {

                $image_file = $request->file('question');

                if ($image_file) {

                    $image_path = public_path($exam->answer);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/written/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name = $img_gen . '.' . $image_ext;
                    $question = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $exam->question = $question;
                    $exam->save();
                }

            }

            if ($request->hasFile('answer')) {

                $image_file = $request->file('answer');

                if ($image_file) {

                    $image_path = public_path($exam->answer);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/written/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name = $img_gen . '.' . $image_ext;
                    $answer   = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                    $exam->answer = $answer;
                    $exam->save();
                }

            }

        }

        return back()->withToastSuccess('Exam created successfully');

    }

    public function writtenQuestion($exam_id) {
        $data         = [];
        $data['exam'] = $exam = Written::where('id', $exam_id)->first();

        return view('backend.exam.written-question', $data);
    }

    public function createOrUpdateWrittenQuestion(Request $request) {
        // dd($request->all());
        DB::beginTransaction();

        try {
            $subject_id = $request->subject_id;

            if (isset($request->question_id) && count($request->question_id) > 0) {

                foreach ($request->question_id as $question_id) {
                    $update_question       = WrittenQuestion::find($question_id);
                    $update_question->name = $request->input('question_name_' . $question_id);
                    $update_question->mark = $request->input('question_mark_' . $question_id);
                    $update_question->save();

                }

            }

            if (isset($request->serial_number) && count($request->serial_number) > 0) {

                foreach ($request->serial_number as $key => $serial_number) {
                    WrittenQuestion::create([
                        'written_id' => $request->written_id,
                        'subject_id' => $subject_id,
                        'name'       => $request->question_name[$key],
                        'mark'       => $request->question_mark[$key],
                    ]);

                }

            }

            DB::commit();

            return back()->withToastSuccess('Question updated or created successfully');

        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->withToastError($th->getMessage());
        }

    }

    public function deleteWrittenQuestion($question_id) {
        $data = WrittenQuestion::find($question_id);

        if ($data) {

            $data->delete();

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false]);
        }

    }

    //syllabus
    public function syllabus() {
        $data = Syllabus::where('category', request()->ref)
            ->where('subcategory', request()->type);

        if (request()->child) {
            $data = $data->where('childcategory', request()->child);
        }

        $data = $data->first();

        return view('backend.exam.syllabus', compact('data'));
    }

    public function uploadSyllabus(Request $request) {

        $data = Syllabus::where('category', $request->category)
            ->where('subcategory', $request->subcategory)
            ->where('childcategory', $request->childcategory)
            ->first();

        if ($request->hasFile('syllabus')) {

            $image_file = $request->file('syllabus');

            if ($image_file) {

                if ($data) {
                    $image_path = public_path($data->syllabus);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                }

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/syllabus/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name = $img_gen . '.' . $image_ext;
                $syllabus = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
            }

        }

        Syllabus::updateOrCreate(
            [
                'id' => $data->id ?? null,
            ],
            [
                'category'      => $request->category,
                'subcategory'   => $request->subcategory,
                'childcategory' => $request->childcategory,
                'syllabus'      => $syllabus ?? '',
            ]);

        return back()->withToastSuccess('Syllabus uploaded successfully');
    }

    //ajax response
    public function getTopic(Request $request) {
        $data = TopicSource::whereIn('subject_id', $request->subjects)->get();

        return json_encode($data);
    }

}
