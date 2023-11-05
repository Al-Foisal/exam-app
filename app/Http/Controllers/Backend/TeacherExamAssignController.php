<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\User;
use App\Models\Written;
use App\Models\WrittenAnswer;
use Illuminate\Http\Request;

class TeacherExamAssignController extends Controller {
    public function index(Request $request) {
        $data                  = [];
        $data['category']      = $category      = $request->ref;
        $data['subcategory']   = $sub   = $request->type;
        $data['childcategory'] = $child = $request->child;

        $exam = Written::where('category', $category)
            ->where('subcategory', $sub);

        if ($child) {
            $exam = $exam->where('childcategory', $child);
        }

        $exam = $exam->orderByDesc('id')
            ->withCount([
                'answer',
            ])
            ->paginate()
            ->withQueryString();

        foreach ($exam as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
            $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
        }

        $data['exam'] = $exam;

        return view('backend.teacher.exam.index', $data);
    }

    public function assignPaper($written_id, $category) {
        $data            = [];
        $data['written'] = Written::find($written_id);
        $data['teacher'] = User::where('permission', 'LIKE', '%' . $category . '%')->get();
        $data['paper']   = WrittenAnswer::where('written_id', $written_id)
            ->where('category', $category)
            ->paginate(100);

        return view('backend.teacher.exam.assign-paper', $data);
    }

}
