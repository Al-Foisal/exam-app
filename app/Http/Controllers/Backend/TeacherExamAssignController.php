<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\User;
use App\Models\Written;
use App\Models\WrittenAnswer;
use App\Services\FCMService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TeacherExamAssignController extends Controller {
    public function writtenMeritlist($written_id) {
        $data = [];

        $exam = WrittenAnswer::where('written_id', $written_id)->where('is_checked', 1)->orderBy('obtained_mark', 'desc');

        if (request()->registration_id) {
            $exam = $exam->whereHas('user', function ($q) {
                $q->where('registration_id', request()->registration_id);
            });
        }

        $exam = $exam->paginate()->withQueryString();

        if (count($exam) == 0) {
            return back()->withToastInfo('Still now for this exam no answer is submitted. So there is nothing to show any of student merit list.');
        }

        $data['exam'] = $exam;

        return view('backend.teacher.exam.meritlist', $data);
    }

    public function writtenMeritlistDownload($written_id) {
        $data = [];

        $exam = WrittenAnswer::where('written_id', $written_id)->where('is_checked', 1)->orderBy('obtained_mark', 'desc')->with('written')->get();

        $data['exam'] = $exam;

        $pdf = Pdf::loadView('backend.teacher.exam.meritlistdownload', $data);

        return $pdf->stream();

        // return view('backend.teacher.exam.meritlistdownload', $data);
    }

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
        $data['teacher'] = $t = User::where('permission', 'LIKE', '%' . $category . '%')->get();
        $data['paper']   = WrittenAnswer::where('written_id', $written_id)
            ->where('category', $category)
            ->orderBy('teacher_id', 'asc')
            ->paginate();

        if (isset($t->fcm_token)) {

            FCMService::send(
                $t->fcm_token,
                [
                    'title' => "Exam paper assign",
                    'body'  => "New exam paper assign to you for assesment",
                ]
            );

            Notification::create([
                'name'    => 'Exam paper assign',
                'details' => "New exam paper assign to you for assesment",
                'user_id' => $t->id,
                'to'      => 'teacher',
            ]);
        }

        return view('backend.teacher.exam.assign-paper', $data);
    }

    public function storeAssignPaper(Request $request) {

        if (!isset($request->written_answer_id)) {
            return back()->withToastInfo('No paper selected');
        }

        if ($request->teacher_id == null) {
            return back()->withToastInfo('No teacher selected');
        }

        WrittenAnswer::whereIn('id', $request->written_answer_id)
            ->update(['teacher_id' => $request->teacher_id]);

        return back()->withToastSuccess('Paper assigned successfully');

    }

    public function removedAssignTeacher($id) {
        $answer = WrittenAnswer::find($id);

        if ($answer->is_checked == 1) {
            return back()->withToastError('No Cheating');
        }

        $answer->teacher_id = null;
        $answer->save();

        return back()->withToastSuccess('Teacher removed from paper');
    }

    public function recheckAssignTeacher($id) {
        $answer = WrittenAnswer::find($id);

        $answer->is_checked = 2;
        $answer->save();

        return back()->withToastSuccess('Paper is recheck able now');
    }

}
