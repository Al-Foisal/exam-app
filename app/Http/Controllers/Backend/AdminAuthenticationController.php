<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class AdminAuthenticationController extends Controller {
    public function login() {
        return view('backend.auth.login');
    }
}
