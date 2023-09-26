<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\Subject;
use App\Models\TopicSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller {
    public function index() {
        $data = [];
        $exam = Exam::where('category', request()->ref)->where('subcategory', request()->type)->latest('published_at')->paginate(20);
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
            $exam->save();
        }

        return back()->withToastSuccess('Exam created successfully');

    }

    public function manageQuestion($exam_id) {
        $data         = [];
        $data['exam'] = $exam = Exam::where('id', $exam_id)->first();

        return view('backend.exam.manage-question', $data);
    }

    public function createOrUpdateManageQuestion(Request $request) {
        // dd($request->all());

        $subject_id = $request->subject_id;

        if (isset($request->question_id) && count($request->question_id) > 0) {

            foreach ($request->question_id as $question_id) {
                $update_question                       = ExamQuestion::find($question_id);
                $update_question->question_name        = $request->input('question_name_' . $question_id);
                $update_question->question_explanation = $request->input('question_explanation_' . $question_id);
                $update_question->save();

                $update_option = $request->input('question_option_name_' . $question_id);

                DB::table('exam_question_options')->where('exam_question_id', $question_id)->delete();

                foreach ($update_option as $u_key => $option) {

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

        if (isset($request->serial_number) && count($request->serial_number) > 0) {

            foreach ($request->serial_number as $key => $serial_number) {
                $question = ExamQuestion::create([
                    'exam_id'              => $request->exam_id,
                    'subject_id'           => $subject_id,
                    'question_name'        => $request->question_name[$key],
                    'question_explanation' => $request->question_explanation[$key],
                ]);

                $postfix = $request->input('question_option_name_' . $subject_id . $serial_number);

                foreach ($postfix as $o_key => $option) {

                    if ($request->input('question_option_' . $subject_id . $serial_number) == $o_key) {
                        $answer = 1;
                    } else {
                        $answer = 0;
                    }

                    ExamQuestionOption::create([
                        'exam_question_id' => $question->id,
                        'option'           => $option,
                        'is_answer'        => $answer,
                    ]);
                }

            }

        }

        return back()->withToastSuccess('Question updated or created successfully');
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

    //ajax response
    public function getTopic(Request $request) {
        $data = TopicSource::whereIn('subject_id', $request->subjects)->get();

        return json_encode($data);
    }

}
