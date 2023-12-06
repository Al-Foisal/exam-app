<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserProfileController extends Controller {
    public function user() {
        $user = User::where('id', auth()->user()->id)->first();

        if ($user) {
            return $this->successMessage('', $user);
        } else {
            return $this->errorMessage();
        }

    }

    public function update(Request $request) {
        $user = User::find(Auth::id());

        if ($request->hasFile('image')) {

            $image_file = $request->file('image');

            if ($image_file) {

                $image_path = public_path($user->image);

                if (File::exists($image_path)) {
                    File::delete($image_path);
                }

                $img_gen   = hexdec(uniqid());
                $image_url = 'images/user/';
                $image_ext = strtolower($image_file->getClientOriginalExtension());

                $img_name    = $img_gen . '.' . $image_ext;
                $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                $image_file->move($image_url, $img_name);
                $user->image = $final_name1;
                $user->save();

            }

        }

        $user->name  = $request->name;
        $user->about = $request->about;
        $user->save();

        return $this->successMessage();

    }

    public function askingQuery(Request $request) {
        FAQ::create([
            'user_id'  => Auth::id(),
            'question' => $request->question,
        ]);

        return $this->successMessage('Your query submitted successfully');
    }

}
