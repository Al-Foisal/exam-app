<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\Written;
use App\Models\WrittenQuestion;
use Illuminate\Http\Request;

class QuestionManageController extends Controller {
    public function archive(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::whereDate('expired_at', '<', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('chaildcategory', $child);
            }

            $exam = $exam->orderByDesc('id')->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        } else

        if ($sub === 'Written') {
            $exam = Written::whereDate('expired_at', '<', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('chaildcategory', $child);
            }

            $exam = $exam->orderByDesc('id')->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        }

        $data['exam'] = $exam;

        return $this->successMessage('', $data);

    }

    public function archiveExamQuestionDetails(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $question = ExamQuestion::where('exam_id', $request->exam_id)->with('questionOptions')->get();
        } else

        if ($sub === 'Written') {
            $question = WrittenQuestion::where('written_id', $request->exam_id)->with('subject')->get();
        }

        return $this->successMessage('', $question);
    }

}
