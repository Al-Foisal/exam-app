<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\Written;
use Illuminate\Http\Request;

class QuestionManageController extends Controller {
    public function checkLiveExam(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;
        $is_live               = false;

        if ($sub === 'Preliminary') {
            $exam = Exam::whereDate('published_at', '<=', date('Y-m-d'))
                ->whereDate('expired_at', '>=', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with('questions.questionOptions')->first();

            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();

            if ($exam) {
                $is_live = true;
            }

        } else

        if ($sub === 'Written') {
            $exam = Written::whereDate('published_at', '<=', date('Y-m-d'))
                ->whereDate('expired_at', '>=', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with('writtenQuestion')->first();

            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();

            if ($exam) {
                $is_live = true;
            }

        }

        $data['exam']         = $exam;
        $data['subjects']     = $subjects;
        $data['sources']      = $sources;
        $data['is_live_exam'] = $is_live;

        return $this->successMessage('', $data);
    }

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
                $exam = $exam->where('childcategory', $child);
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
                $exam = $exam->where('childcategory', $child);
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
            $exam = Exam::where('id', $request->exam_id)
                ->with('questions.questionOptions')
                ->first();
            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();
        } else

        if ($sub === 'Written') {
            $exam = Written::where('id', $request->exam_id)->with('writtenQuestion')->first();

            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();
        }

        $data['exam']     = $exam;
        $data['subjects'] = $subjects;
        $data['sources']  = $sources;

        return $this->successMessage('', $data);
    }

}
