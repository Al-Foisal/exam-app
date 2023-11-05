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
            $exam = Exam::whereDate('published_at', '<=', date('Y-m-d'))
                ->whereDate('expired_at', '>=', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with('questions.questionOptions', 'userAnswer')->first();

        } elseif ($sub === 'Written') {
            $exam = Written::whereDate('published_at', '<=', date('Y-m-d'))
                ->whereDate('expired_at', '>=', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            $exam = $exam->with('writtenQuestion', 'userAnswer')->first();

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

        return $this->successMessage('', $data);
    }

    public function routine(Request $request) {
        $data = [];

        $data['category']      = $category      = $request->category;
        $data['subcategory']   = $sub   = $request->subcategory;
        $data['childcategory'] = $child = $request->childcategory;

        if ($sub === 'Preliminary') {
            $exam = Exam::whereDate('published_at', '>', date('Y-m-d'))
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
            $exam = Written::whereDate('published_at', '>', date('Y-m-d'))
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

            if ($request->subject_id) {
                $exam = $exam->where('subject_id', 'LIKE', '%' . $request->subject_id . '%');
            }

            $exam = $exam->orderByDesc('id')
                ->with('userAnswer')
                ->withCount('questions')
                ->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        } elseif ($sub === 'Written') {
            $exam = Written::whereDate('expired_at', '<', date('Y-m-d'))
                ->where('category', $category)
                ->where('subcategory', $sub);

            if ($child) {
                $exam = $exam->where('childcategory', $child);
            }

            if ($request->subject_id) {
                $exam = $exam->where('subject_id', 'LIKE', '%' . $request->subject_id . '%');
            }

            $exam = $exam->orderByDesc('id')
                ->with('userAnswer')
                ->withCount('writtenQuestion')
                ->paginate();

            foreach ($exam as $item) {
                $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
                $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            }

        }

        $data['exam'] = $exam;

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

        $answer = PreliminaryAnswer::where('user_id', Auth::id())
            ->whereHas('exam', function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->whereHas('exam', function ($q) use ($sub) {
                return $q->where('subcategory', $sub);
            })
            ->whereHas('exam', function ($q) use ($child) {
                return $q->where('childcategory', $child);
            })->latest()->paginate();

        foreach ($answer as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->exam->subject_id))->get();
            $item['sources']  = TopicSource::whereIn('id', explode(',', $item->exam->topic_id))->get();
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

        $exam = PreliminaryAnswer::where('user_id', Auth::id())
            ->latest()
            ->whereHas('exam', function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->whereHas('exam', function ($q) use ($sub) {
                return $q->where('subcategory', $sub);
            })
            ->whereHas('exam', function ($q) use ($child) {
                return $q->where('childcategory', $child);
            })->first();

        $get_exam_answer = PreliminaryAnswer::where('exam_id', $exam->exam_id)
            ->select(['id', 'user_id', 'obtained_marks', 'created_at'])
            ->orderBy('obtained_marks', 'desc');

        if ($request->search) {
            $get_exam_answer = $get_exam_answer->whereHas('user', function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $get_exam_answer = $get_exam_answer->with('user')->paginate();

        return $this->successMessage('ok', $get_exam_answer);
    }

}