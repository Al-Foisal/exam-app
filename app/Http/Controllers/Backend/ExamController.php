<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\TopicSource;
use Illuminate\Http\Request;

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
                'name'                       => $request->name,
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
            $exam->name                       = $request->name;
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
        dd($request->all());
    }

    //ajax response
    public function getTopic(Request $request) {
        $data = TopicSource::whereIn('subject_id', $request->subjects)->get();

        return json_encode($data);
    }

}
