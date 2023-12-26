<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Notification;
use App\Models\PreliminaryAnswer;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\WrittenAnswer;
use App\Models\WrittenAnswerQuestion;
use App\Models\WrittenAnswerQuestionScript;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnswerController extends Controller {
    public function storePreliminaryAnswer(Request $request) {

        DB::beginTransaction();

        try {

            if (PreliminaryAnswer::where('user_id', Auth::id())->where('exam_id', $request->exam_id)->exists()) {
                return $this->errorMessage('This answer has been taken before');
            }

            $answer          = new PreliminaryAnswer();
            $answer->user_id = Auth::id();
            $answer->exam_id = $request->exam_id;
            $answer->type    = $request->type;
            $answer->answer  = $request->answer;
            $answer->save();

            //claculating exam result
            $total_question = (int) $request->total_question;
            $positive_count = 0;
            $negative_count = 0;
            $empty_count    = 0;

            $empty_marks = 0;

            $root_answer = str_replace("A", 0, $answer->answer);
            $root_answer = str_replace("B", 1, $root_answer);
            $root_answer = str_replace("C", 2, $root_answer);
            $root_answer = json_decode(str_replace("D", 3, $root_answer));

            $questions = ExamQuestion::where('exam_id', $request->exam_id)->limit($total_question)->with('questionOptions')->get();

            foreach ($questions as $key => $item) {

                if ($item->questionOptions->count() > 0) {

                    foreach ($item->questionOptions as $ie_key => $ie) {

                        if ($ie->is_answer == 1) {

                            if ($ie_key == $root_answer->$key) {
                                ++$positive_count;

                                $q = ExamQuestion::find($ie->exam_question_id);
                                $q->update([
                                    'correct' => $q->correct + 1,
                                    'total'   => $q->total + 1,
                                ]);
                            } elseif ($root_answer->$key == '') {
                                ++$empty_count;
                                ++$empty_marks;

                                $q = ExamQuestion::find($ie->exam_question_id);
                                $q->update([
                                    'empty' => $q->empty + 1,
                                    'total' => $q->total + 1,
                                ]);
                            } else {
                                ++$negative_count;

                                $q = ExamQuestion::find($ie->exam_question_id);
                                $q->update([
                                    'negative' => $q->negative + 1,
                                    'total'    => $q->total + 1,
                                ]);
                            }

                            break;

                        }

                    }

                } else {
                    ++$empty_count;
                    ++$empty_marks;
                }

            }

            $exam_details = Exam::where('id', $answer->exam_id)->first();

            $obtained_mark = $positive_count * $exam_details->per_question_positive_mark - $negative_count * $exam_details->per_question_negative_mark;

            $answer->obtained_marks = $obtained_mark;

            $answer->positive_count = $positive_count;
            $answer->negative_count = $negative_count;
            $answer->empty_count    = $empty_count;

            $answer->positive_marks = $positive_count * $exam_details->per_question_positive_mark;
            $answer->negative_marks = $negative_count * $exam_details->per_question_negative_mark;
            $answer->empty_marks    = $empty_marks;

            $answer->result_status = $obtained_mark > $exam_details->pass_marks ? 1 : 0;
            $answer->save();

            // if (isset($answer->user->fcm_token)) {
            //     $catt = '';

            //     if ($exam_details->childcategory) {

            //         if ($exam_details->childcategory == 'Primary') {
            //             $catt = 'প্রাইমারি';
            //         } elseif ($exam_details->childcategory == '11 to 20 Grade') {
            //             $catt = 'শিক্ষক এবং প্রভাষক';
            //         } elseif ($exam_details->childcategory == 'Non-Cadre') {
            //             $catt = 'নন-ক্যাডার';
            //         } elseif ($exam_details->childcategory == 'Job Solution') {
            //             $catt = 'জব সলুশন';
            //         } elseif ($exam_details->childcategory == 'Weekly') {
            //             $catt = 'সাপ্তাহিক';
            //         } elseif ($exam_details->childcategory == 'Daily') {
            //             $catt = 'দৈনিক';
            //         }

            //     } else {

            //         if ($exam_details->category == 'BCS') {
            //             $catt = 'বিসিএস';
            //         } else {
            //             $catt = 'ব্যাংক';
            //         }

            //     }

            //     FCMService::send(
            //         $answer->user->fcm_token,
            //         [
            //             'title' => "লাইভ পরীক্ষা",
            //             'body'  => $catt . " প্রিলিমিনারি লাইভ পরীক্ষা শেষ হয়েছে, ফলাফল দেখুন।",
            //         ]
            //     );

            //     Notification::create([
            //         'name'       => 'লাইভ পরীক্ষা',
            //         'details'    => $catt . " প্রিলিমিনারি লাইভ পরীক্ষা শেষ হয়েছে, ফলাফল দেখুন।",
            //         'user_id'    => $answer->user->id,
            //         'written_id' => $answer->written_id,
            //         'to'         => 'user',
            //     ]);
            // }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Your answer has been submitted',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th,
            ]);
        }

    }

    public function showPreliminaryAnswer(Request $request) {
        $data = [];

        if ($request->exam_id) {

            if (!PreliminaryAnswer::where('user_id', Auth::id())->where('exam_id', $request->exam_id)->exists()) {
                return $this->errorMessage('Somthing went wrong', '');
            }

            $answer = PreliminaryAnswer::where('user_id', Auth::id())
                ->where('exam_id', $request->exam_id)
                ->with(
                    'user',
                    'exam',
                )
                ->first();

            $data['question_count'] = ExamQuestion::where('exam_id', $answer->exam->id)->count();

            $data['total_examinee']        = PreliminaryAnswer::where('exam_id', $request->exam_id)->count();
            $data['answer']                = $answer;
            $data['total_passed_examinee'] = PreliminaryAnswer::where('exam_id', $request->exam_id)->where('obtained_marks', '>', $answer->exam->pass_marks)->count();

            $get_exam_answer = PreliminaryAnswer::where('exam_id', $answer->exam_id)->orderBy('obtained_marks', 'desc')->pluck('user_id')->toArray();

            $data['my_position'] = array_search(Auth::id(), $get_exam_answer) + 1;
            $data['subjects']    = Subject::whereIn('id', explode(',', $answer->exam->subject_id))->get();
            $data['sources']     = TopicSource::whereIn('id', explode(',', $answer->exam->topic_id))->get();
        } elseif ($request->written_id) {

            if (!WrittenAnswer::where('user_id', Auth::id())->where('written_id', $request->written_id)->exists()) {
                return $this->errorMessage('Somthing went wrong', '');
            }

            $answer = WrittenAnswer::where('user_id', Auth::id())
                ->where('written_id', $request->written_id)
                ->with(
                    'written',
                    'user',
                    'teacher',
                    'writtenAnswerQuestion.writtenAnswerQuestion',
                    'writtenAnswerQuestion.writtenAnswerQuestionScript',
                )
                ->first();

            if ($answer->is_checked == 0) {
                return $this->errorMessage('Your script is under examine.');
            }

            $data['total_examinee'] = WrittenAnswer::where('written_id', $request->written_id)->count();

            $data['total_passed_examinee'] = WrittenAnswer::where('written_id', $request->written_id)->where('obtained_mark', '>', $answer->written->pass_marks)->count();

            $get_exam_answer = WrittenAnswer::where('written_id', $answer->written_id)->orderBy('obtained_mark', 'desc')->pluck('user_id')->toArray();

            $data['my_position'] = array_search(Auth::id(), $get_exam_answer) + 1;
            $data['answer']      = $answer;
            $data['subjects']    = Subject::whereIn('id', explode(',', $answer->written->subject_id))->get();
            $data['sources']     = TopicSource::whereIn('id', explode(',', $answer->written->topic_id))->get();
        }

        return $this->successMessage('ok', $data);
    }

    public function preliminaryAnswerScript(Request $request) {

        if (!PreliminaryAnswer::where('user_id', Auth::id())->where('exam_id', $request->exam_id)->exists()) {
            return $this->errorMessage('Somthing went wrong11', '');
        }

        $answer = PreliminaryAnswer::where('user_id', Auth::id())
            ->where('exam_id', $request->exam_id)
            ->with(
                'user',
                'exam',
            )
            ->first();

        $total_question = (int) $request->total_question;

        $root_answer = (str_replace("A", 0, $answer->answer));
        $root_answer = (str_replace("B", 1, $root_answer));
        $root_answer = (str_replace("C", 2, $root_answer));
        $root_answer = json_decode(str_replace("D", 3, $root_answer));

        $questions = ExamQuestion::where('exam_id', $request->exam_id)->limit($total_question)->with('questionOptions')->get();

        foreach ($questions as $key => $item) {

            if ($item->questionOptions->count() > 0) {

                foreach ($item->questionOptions as $ie_key => $ie) {

                    if ($ie->is_answer == 1) {

                        if ($ie_key == $root_answer->$key) {
                            $item['is_correct']   = 1;
                            $item['given_answer'] = $root_answer->$key;
                        } elseif ($root_answer->$key == '') {
                            $item['is_correct'] = 2;
                            // $item['given_answer'] = $root_answer->$key;
                            $item['given_answer'] = '';
                        } else {
                            $item['is_correct']   = 3;
                            $item['given_answer'] = $root_answer->$key;
                        }

                        break;

                    }

                }

            } else {
                $item['is_correct']   = 2;
                $item['given_answer'] = '';
            }

        }

        return $this->successMessage('ok', $questions);
    }

    public function preliminaryAnswerMeritList(Request $request) {

        if (isset($request->exam_id)) {
            $get_exam_answer = PreliminaryAnswer::where('exam_id', $request->exam_id)
                ->select(['id', 'user_id', 'obtained_marks', 'created_at'])
                ->orderBy('obtained_marks', 'desc');

            if ($request->search) {
                $get_exam_answer = $get_exam_answer->whereHas('user', function ($q) use ($request) {
                    return $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            }

            $get_exam_answer = $get_exam_answer->with('user')->paginate();
        } else {
            $get_exam_answer = WrittenAnswer::where('written_id', $request->written_id)
                ->where('is_checked', 1)
                ->select(['id', 'user_id', 'obtained_mark', 'created_at'])
                ->orderBy('obtained_mark', 'desc');

            if ($request->search) {
                $get_exam_answer = $get_exam_answer->whereHas('user', function ($q) use ($request) {
                    return $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            }

            $get_exam_answer = $get_exam_answer->with('user')->paginate();
        }

        return $this->successMessage('ok', $get_exam_answer);
    }

    public function storeWrittenAnswer(Request $request) {
        DB::beginTransaction();

        try {

            if (WrittenAnswer::where('user_id', $request->user_id)->where('written_id', $request->written_id)->exists()) {
                return $this->errorMessage('This answer has been taken before');
            }

            $answer = WrittenAnswer::create([
                'user_id'    => $request->user_id,
                'written_id' => $request->written_id,
                'category'   => $request->category,
            ]);

            foreach (json_decode($request->question_id) as $question) {
                $answer_question = WrittenAnswerQuestion::create([
                    'written_answer_id'   => $answer->id,
                    'written_question_id' => $question,
                ]);

                $files = [];

                $request_file_name = 'student_script_' . $question;

                if ($request->hasfile($request_file_name)) {

                    foreach ($request->file($request_file_name) as $file) {
                        $name = $question . '-' . $answer_question->written_answer_id . Str::uuid() . '.' . $file->extension();
                        $file->move(public_path('images/script/'), $name);
                        $files[] = 'images/script/' . $name;
                    }

                }

                foreach ($files as $f) {
                    WrittenAnswerQuestionScript::create([
                        'written_answer_question_id' => $answer_question->id,
                        'student_script'             => $f,
                    ]);
                }

            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Your answer has been submitted',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ]);
        }

    }

}
