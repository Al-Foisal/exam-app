<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class DashboardController extends Controller {
    public function dashboard() {
        $data = [];

        return view('backend.dashboard', $data);
    }

    public function logout() {
        auth()->guard('admin')->logout();

        return to_route('login')->withToastSuccess('Logout Successful!!');
    }
}
