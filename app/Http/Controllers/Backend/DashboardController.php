<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function dashboard() {
        $data = [];

        return view('backend.dashboard', $data);
    }

    public function logout() {
        auth()->guard('admin')->logout();

        return to_route('login')->withToastSuccess('Logout Successful!!');
    }

    public function students(Request $request) {
        $data    = [];
        $student = User::where('type', 'user');

        if ($request->package_id && $request->package_id != 'no') {
            $student = $student->whereHas('packageHistory', function ($q) use ($request) {
                $q->where('package_id', $request->package_id);
            });
        } elseif ($request->package_id && $request->package_id == 'no') {
            $student = $student->withCount('packageHistory')->having('package_history_count', '=', 0);
        } else {
            $student = $student->withCount([
                'packageHistory',
            ]);
        }

        if ($request->registration_id) {
            $student = $student->where('registration_id', $request->registration_id);
        }

        $student = $student->paginate()->withQueryString();

        $data['student']  = $student;
        $data['packages'] = Package::latest()->get();

        return view('backend.student.index', $data);
    }

    public function changeStudentStatus($id) {
        $data = User::where('type', 'user')->where('id', $id)->first();

        if (!$data) {
            return back()->withToastError('Unregistered student');
        }

        $data->status = $data->status == 1 ? 0 : 1;
        $data->save();

        return back()->withToastSuccess('Student status change successfully');
    }

    public function showStudentDetails($id) {
        $data = User::where('id', $id)->where('type', 'user')->withCount([
            'packageHistory',
        ])->first();

        if (!$data) {
            return back()->withToastError('No student found');
        }

        return view('backend.student.show', compact('data'));
    }

}
