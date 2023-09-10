<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthenticationController extends Controller {
    public function login() {
        return view('backend.auth.login');
    }

    public function storeLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        if (
            Auth::guard('admin')->attempt([
                'email'    => $request->email,
                'password' => $request->password,
                'status'   => 1,
            ])
        ) {

            return to_route('dashboard');

        }

        return to_route('login')->withToastError('Invalid Credentitials!!');

    }

}
