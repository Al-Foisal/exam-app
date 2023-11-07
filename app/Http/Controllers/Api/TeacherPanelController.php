<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\Written;
use App\Models\WrittenAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherPanelController extends Controller {
    public function examAndPaper(Request $request) {
        $data             = [];
        $data['category'] = $category = $request->category;
        $examId           = WrittenAnswer::where('category', $category)
            ->whereNotNull('teacher_id')
            ->where('teacher_id', Auth::id())
            ->groupBy('written_id')
            ->select('written_id')
            ->pluck('written_id')
            ->toArray();

        $exam = Written::whereIn('id', $examId)
            ->with([
                'answer.user',
                'answer' => function ($q) {
                    return $q->where('teacher_id', Auth::id())
                        ->withCount([
                            'writtenAnswerQuestion as checked_question' => function ($aq) {
                                return $aq->where('is_checked_by_teacher', 1);
                            },
                            'writtenAnswerQuestion as total_question',
                        ]);
                },
                'answer.writtenAnswerQuestion.writtenAnswerQuestionScript',
            ])
            ->withCount([
                'answer as total_examinee' => function ($q) {
                    return $q->where('teacher_id', Auth::id());
                },
                'answer as total_examined' => function ($q) {
                    return $q->where('teacher_id', Auth::id())->where('is_checked', 1);
                },
            ])
            ->paginate();

        foreach ($exam as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
            $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
        }

        $data['exam'] = $exam;

        return $data;
    }

}
