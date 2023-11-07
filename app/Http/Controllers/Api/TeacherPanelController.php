<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeacherWallet;
use App\Models\TopicSource;
use App\Models\Written;
use App\Models\WrittenAnswer;
use App\Models\WrittenAnswerQuestion;
use App\Models\WrittenAnswerQuestionScript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

    public function storeExamPaperAssessment(Request $request) {
        $question = WrittenAnswerQuestion::find($request->written_answer_question_id);

        if ($question) {

            $is_checked_before = $question->is_checked == 1 ? true : false;

            if ($request->hasfile('teacher_script')) {

                foreach ($request->file('teacher_script') as $file) {
                    $name = $question->id . '-' . time() . Str::uuid() . '.' . $file->extension();
                    $file->move(public_path('images/script/'), $name);
                    $files[] = 'images/script/' . $name;
                }

            }

            $script = WrittenAnswerQuestionScript::where('written_answer_question_id', $question->id)->get();

            foreach ($script as $key => $item) {
                $image_path = public_path($item->teacher_script);

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $item->update([
                    'teacher_script' => $files[$key],
                ]);
            }

            $question->update([
                'is_checked_by_teacher' => 1,
                'marks'                 => $request->marks,
                'comment'               => $request->comment,
            ]);

            $written_answer                = WrittenAnswer::find($question->written_answer_id);
            $written_answer->obtained_mark = $request->obtained_mark;
            $written_answer->is_checked    = $request->is_checked;
            $written_answer->save();

            if (!$is_checked_before) {

                if (TeacherWallet::where('user_id', Auth::id())->exists()) {
                    $wallet         = TeacherWallet::where('user_id', Auth::id())->first();
                    $wallet->amount = $wallet->amount + auth()->user()->amount;
                    $wallet->save();
                } else {
                    $wallet          = new TeacherWallet();
                    $wallet->user_id = Auth::id();
                    $wallet->amount  = auth()->user()->amount;
                    $wallet->save();
                }

            }

            return $this->successMessage();

        } else {
            return $this->errorMessage('Something went wrong');
        }

    }

}
