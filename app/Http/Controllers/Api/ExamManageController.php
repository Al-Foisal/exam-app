<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Favorite;
use App\Models\PreliminaryAnswer;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\TopicSource;
use App\Models\Written;
use App\Models\WrittenAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamManageController extends Controller {
    public function checkLiveExam(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;
        $is_live               = false;

        $exam = [];

        if ($sub === 'Preliminary') {
            $exam = Exam::where('status', 1)->where('published_at', '<=', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('expired_at', '>=', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with([
                'questions.questionOptions',
                'userAnswer' => function ($q) {
                    return $q->where('user_id', Auth::id());
                },
            ])->first();

        } elseif ($sub === 'Written') {
            $exam = Written::where('status', 1)->where('published_at', '<=', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('expired_at', '>=', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with([
                'writtenQuestion',
                'userAnswer' => function ($q) {
                    return $q->where('user_id', Auth::id());
                },
            ])->first();

        }

        $data['exam'] = $exam;
        $subjects     = [];
        $sources      = [];

        if ($exam) {
            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();

            $is_live = true;
        }

        $data['subjects']     = $subjects;
        $data['sources']      = $sources;
        $data['is_live_exam'] = $is_live;

        if (isset($exam) && $exam->userAnswer != null) {
            return $this->errorMessage('আপনি ইতিমধ্যে এই পরিখাতে অংশগ্রোন কোরেছেন');
        }

        return $this->successMessage('', $data);
    }

    public function routine(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::where('status', 1)->where('published_at', '>', Carbon::now('Asia/Dhaka')->toDateTimeString())
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

        } elseif ($sub === 'Written') {
            $exam = Written::where('status', 1)->where('published_at', '>', Carbon::now('Asia/Dhaka')->toDateTimeString())
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

    public function allRoutine(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::where('status', 1)->where('published_at', '>', Carbon::now('Asia/Dhaka')->toDateTimeString());

            $exam = $exam->orderByDesc('id')->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        } elseif ($sub === 'Written') {
            $exam = Written::where('status', 1)->where('published_at', '>', Carbon::now('Asia/Dhaka')->toDateTimeString());

            $exam = $exam->orderByDesc('id')->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        }

        $data['exam'] = $exam;

        return $this->successMessage('', $data);

    }

    public function archive(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::where('status', 1)->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            if ($request->subject_id) {
                $exam = $exam->where('subject_id', 'LIKE', $request->subject_id . '%');
            }

            $exam = $exam->orderByDesc('id')
                ->with([
                    'userAnswer' => function ($q) {
                        return $q->where('user_id', Auth::id());
                    },
                ])
                ->withCount('questions')
                ->paginate();

            if ($request->search) {
                $exam = Exam::where('status', 1)->whereDate('expired_at', '<', date('Y-m-d'))
                    ->where('category', $category)
                    ->where('subcategory', $sub);

                if ($child) {
                    $exam = $exam->where('childcategory', $child);
                }

                $exam = $exam->orderByDesc('id')
                    ->with([
                        'userAnswer' => function ($q) {
                            return $q->where('user_id', Auth::id());
                        },
                    ])
                    ->withCount('questions')
                    ->get();
            }

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

            if ($request->search) {
                $new_data = [];
                $flag     = false;

                foreach ($exam as $e_item) {
                    $flag = false;

                    foreach ($e_item->subjects as $sub) {

                        if (str_starts_with($sub->name, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    foreach ($e_item->sources as $src) {

                        if (str_starts_with($src->topic, $request->search) || str_starts_with($src->source, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    if ($flag == true) {

                        $new_data[] = $e_item;
                    }

                }

                $data['exam'] = $new_data;

                return $this->successMessage('', $data);
            }

            $data['exam'] = $exam;

            return $this->successMessage('', $data);

        } elseif ($sub === 'Written') {
            $exam = Written::where('status', 1)->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString())
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            if ($request->subject_id) {
                $exam = $exam->where('subject_id', 'LIKE', $request->subject_id . '%');
            }

            $exam = $exam->orderByDesc('id')
                ->with([
                    'userAnswer' => function ($q) {
                        return $q->where('user_id', Auth::id());
                    },
                ])
                ->withCount('writtenQuestion')
                ->paginate();

            if ($request->search) {
                $exam = Written::where('status', 1)->whereDate('expired_at', '<', date('Y-m-d'))
                    ->where('category', $category)
                    ->where('subcategory', $sub);

                if ($child) {
                    $exam = $exam->where('childcategory', $child);
                }

                $exam = $exam->orderByDesc('id')
                    ->with([
                        'userAnswer' => function ($q) {
                            return $q->where('user_id', Auth::id());
                        },
                    ])
                    ->withCount('writtenQuestion')
                    ->get();
            }

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

            if ($request->search) {
                $new_data = [];
                $flag     = false;

                foreach ($exam as $e_item) {
                    $flag = false;

                    foreach ($e_item->subjects as $sub) {

                        if (str_starts_with($sub->name, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    foreach ($e_item->sources as $src) {

                        if (str_starts_with($src->topic, $request->search) || str_starts_with($src->source, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    if ($flag == true) {

                        $new_data[] = $e_item;
                    }

                }

                $data['exam'] = $new_data;

                return $this->successMessage('', $data);
            }

            $data['exam'] = $exam;

            return $this->successMessage('', $data);
        }

        return $this->successMessage('', $data);

    }

    public function syllabus(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;
        $data['syllabus']      = Syllabus::where('category', $category)
            ->where('subcategory', $sub)
            ->where('childcategory', $child)
            ->first();

        return $this->successMessage('', $data);
    }

    public function archiveExamQuestionDetails(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::where('status', 1)->where('id', $request->exam_id)
                ->with('questions.questionOptions')
                ->first();
            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();
        } else

        if ($sub === 'Written') {
            $exam = Written::where('status', 1)->where('id', $request->exam_id)->with('writtenQuestion')->first();

            $subjects = Subject::whereIn('id', explode(',', $exam->subject_id))->get();
            $sources  = TopicSource::whereIn('id', explode(',', $exam->topic_id))->get();
        }

        $data['exam']     = $exam;
        $data['subjects'] = $subjects;
        $data['sources']  = $sources;

        return $this->successMessage('', $data);
    }

    public function toggleFavorite(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('question_id', $request->question_id)
            ->where('category', $category)
            ->where('subcategory', $sub)
            ->where('childcategory', $child)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return $this->successMessage('Question removed from favorite');
        }

        Favorite::create([
            'category'      => $category,
            'subcategory'   => $sub,
            'childcategory' => $child,
            'question_id'   => $request->question_id,
            'subject_id'    => $request->subject_id,
            'user_id'       => Auth::id(),
        ]);

        return $this->successMessage('Question added to favourite');
    }

    public function favoriteList(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {

            $favorite = Favorite::where('user_id', Auth::id())
                ->where('category', $category)
                ->where('subcategory', $sub)
                ->where('childcategory', $child);

            if ($request->subject_id) {
                $favorite = $favorite->where('subject_id', $request->subject_id);
            }

            if ($request->search) {
                $favorite = $favorite->whereHas('preliQuestion', function ($q) use ($request) {
                    return $q->where('question_name', 'LIKE', '%' . $request->search . '%');
                });
            }

            $favorite = $favorite->with('preliQuestion.questionOptions')
                ->latest()
                ->paginate();
        } else {
            $favorite = Favorite::where('user_id', Auth::id())
                ->where('category', $category)
                ->where('subcategory', $sub)
                ->where('childcategory', $child);

            if ($request->subject_id) {
                $favorite = $favorite->where('subject_id', $request->subject_id);
            }

            if ($request->search) {
                $favorite = $favorite->whereHas('writtenQuestion', function ($q) use ($request) {
                    return $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            }

            $favorite = $favorite->with('writtenQuestion')
                ->latest()
                ->paginate();
        }

        $data['favorite'] = $favorite;

        return $this->successMessage('', $data);

    }

    public function resultList(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $answer = PreliminaryAnswer::where('user_id', Auth::id())
                ->whereHas('exam', function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->whereHas('exam', function ($q) use ($sub) {
                    return $q->where('subcategory', $sub);
                });

            if ($child) {
                $answer = $answer->whereHas('exam', function ($q) use ($child) {
                    return $q->where('childcategory', $child);
                });
            }

            if ($request->subject_id) {
                $answer = $answer->whereHas('exam', function ($q) use ($request) {
                    return $q->where('subject_id', 'LIKE', $request->subject_id . '%');
                });
            }

            $answer = $answer->latest()->paginate();

            foreach ($answer as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->exam->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->exam->topic_id))->get();
            }

            if ($request->search) {
                $new_data = [];
                $flag     = false;

                foreach ($answer as $e_item) {
                    $flag = false;

                    foreach ($e_item->subjects as $sub) {

                        if (str_starts_with($sub->name, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    foreach ($e_item->sources as $src) {

                        if (str_starts_with($src->topic, $request->search) || str_starts_with($src->source, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    if ($flag == true) {

                        $new_data[] = $e_item;
                    }

                }

                $answer = $new_data;
            }

        } else {
            $answer = WrittenAnswer::where('user_id', Auth::id())
                ->where('is_checked', 1)
                ->whereHas('written', function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->whereHas('written', function ($q) use ($sub) {
                    return $q->where('subcategory', $sub);
                });

            if ($child) {
                $answer = $answer->whereHas('written', function ($q) use ($child) {
                    return $q->where('childcategory', $child);
                });
            }

            if ($request->subject_id) {
                $answer = $answer->whereHas('written', function ($q) use ($request) {
                    return $q->where('subject_id', 'LIKE', $request->subject_id . '%');
                });
            }

            $answer = $answer->latest()->paginate();

            foreach ($answer as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->written->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->written->topic_id))->get();
            }

            if ($request->search) {
                $new_data = [];
                $flag     = false;

                foreach ($answer as $e_item) {
                    $flag = false;

                    foreach ($e_item->subjects as $sub) {

                        if (str_starts_with($sub->name, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    foreach ($e_item->sources as $src) {

                        if (str_starts_with($src->topic, $request->search) || str_starts_with($src->source, $request->search)) {
                            $flag = true;
                            break;
                        }

                    }

                    if ($flag == true) {

                        $new_data[] = $e_item;
                    }

                }

                $answer = $new_data;
            }

        }

        return $this->successMessage('ok', $answer);
    }

    public function subjectList() {
        $data = Subject::with('topicAndSources')->get();

        return $this->successMessage('', $data);
    }

    public function meritList(Request $request) {

        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;
        $examName              = 'প্রিলিমিনারি';

        if ($sub === 'Preliminary') {
            $exam = PreliminaryAnswer::where('user_id', Auth::id())
                ->latest()
                ->whereHas('exam', function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->whereHas('exam', function ($q) use ($sub) {
                    return $q->where('subcategory', $sub);
                });

            if ($child) {
                $exam = $exam->whereHas('exam', function ($q) use ($child) {
                    return $q->where('childcategory', $child);
                });
            }

            $exam = $exam->first();

            $get_exam_answer = [];

            if ($exam) {
                $get_exam_answer = PreliminaryAnswer::where('exam_id', $exam->exam_id)
                    ->select(['id', 'user_id', 'obtained_marks', 'created_at'])
                    ->orderBy('obtained_marks', 'desc');

                if ($request->search) {
                    $get_exam_answer = $get_exam_answer->whereHas('user', function ($q) use ($request) {
                        return $q->where('name', 'LIKE', '%' . $request->search . '%');
                    });
                }

                $get_exam_answer = $get_exam_answer->with('user')->paginate();
            }

            $examName = 'প্রিলিমিনারি';
        } else {
            $written = WrittenAnswer::where('user_id', Auth::id())
                ->where('is_checked', 1)
                ->latest()
                ->whereHas('written', function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->whereHas('written', function ($q) use ($sub) {
                    return $q->where('subcategory', $sub);
                });

            if ($child) {
                $written = $written->whereHas('written', function ($q) use ($child) {
                    return $q->where('childcategory', $child);
                });
            }

            $written = $written->first();

            $get_exam_answer = [];

            if ($written) {
                $get_exam_answer = WrittenAnswer::where('written_id', $written->written_id)
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

            $examName = 'লিখিত';
        }

        if (count($get_exam_answer) > 0) {
            return $this->successMessage('ok', $get_exam_answer);
        } else {
            return $this->errorMessage('আপনি কোন ' . $examName . ' পরীক্ষায় অংশগ্রহণ করেননি।');
        }

    }

}
