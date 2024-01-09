<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Notification;
use App\Models\Package;
use App\Models\User;
use App\Models\Written;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PackageController extends Controller {
    public function index() {
        $data            = [];
        $data['package'] = Package::withCount('packageHistory')->latest()->paginate();

        return view('backend.package.index', $data);
    }

    public function createOrEdit($id = null) {
        $data = [];

        if ($id) {
            $data['package']         = Package::find($id);
            $data['bcs_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'BCS')
                ->where('subcategory', 'Preliminary')
                ->get();
            $data['bcs_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'BCS')
                ->where('subcategory', 'Written')
                ->get();

            $data['bank_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Bank')
                ->where('subcategory', 'Preliminary')
                ->get();
            $data['bank_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Bank')
                ->where('subcategory', 'Written')
                ->get();

            $data['primary_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Primary')
                ->get();

            $data['grade_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', '11 to 20 Grade')
                ->get();
            $data['grade_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Written')
                ->where('childcategory', '11 to 20 Grade')
                ->get();

            $data['non_cader_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Non-Cadre')
                ->get();

            $data['job_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Job Solution')
                ->get();
            $data['job_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Written')
                ->where('childcategory', 'Job Solution')
                ->get();
        } else {
            $data['package']         = null;
            $data['bcs_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'BCS')
                ->where('subcategory', 'Preliminary')
                ->get();
            $data['bcs_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'BCS')
                ->where('subcategory', 'Written')
                ->get();

            $data['bank_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Bank')
                ->where('subcategory', 'Preliminary')
                ->get();
            $data['bank_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Bank')
                ->where('subcategory', 'Written')
                ->get();

            $data['primary_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Primary')
                ->get();

            $data['grade_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', '11 to 20 Grade')
                ->get();
            $data['grade_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Written')
                ->where('childcategory', '11 to 20 Grade')
                ->get();

            $data['non_cader_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Non-Cadre')
                ->get();

            $data['job_preliminary'] = Exam::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Preliminary')
                ->where('childcategory', 'Job Solution')
                ->get();
            $data['job_written'] = Written::whereDate('published_at', '>', date('Y-m-d'))
                ->where('category', 'Others')
                ->where('subcategory', 'Written')
                ->where('childcategory', 'Job Solution')
                ->get();
        }

        return view('backend.package.create', $data);
    }

    public function storeOrUpdate(Request $request, $id = null) {

// dd($request->all());

        if ($id) {
            $package = Package::find($id);

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $image_path = public_path($package->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/pac$package/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                    $package->image = $final_name1;
                    $package->save();

                }

            }

            $package->update([
                'name'       => $request->name,
                'details'    => $request->details,
                'amount'     => $request->amount,
                'permission' => $request->permission,
                'validity'   => $request->validity,
                'status'     => $request->status,
                'type'       => $request->type,
            ]);

            return to_route('packages.index')->withToastSuccess('Package updated successfully');
        } else {

            if ($request->hasFile('image')) {

                $image_file = $request->file('image');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/package/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                }

            }

            $package = Package::create([
                'name'       => $request->name,
                'details'    => $request->details,
                'amount'     => $request->amount,
                'permission' => $request->permission,
                'validity'   => $request->validity,
                'status'     => $request->status,
                'type'       => $request->type,
                'image'      => $final_name1 ?? null,
            ]);

            User::where('type', 'user')->chunk(200, function ($users) {
                foreach ($users as $user) {
                    if (isset($user->fcm_token)) {

                        FCMService::send(
                            $user->fcm_token,
                            [
                                'title' => "নতুন প্যাকেজ",
                                'body'  => "নতুন একটি ব্যাচ চালু হয়েছে। আপনার প্রস্তুতি যাচাই করুন।",
                            ]
                        );

                        Notification::create([
                            'name'    => 'নতুন প্যাকেজ',
                            'details' => "নতুন একটি ব্যাচ চালু হয়েছে। আপনার প্রস্তুতি যাচাই করুন।",
                            'user_id' => $user->id,
                            'to'      => 'user',
                        ]);
                    }

                }

            });

            return back()->withToastSuccess('Package created successfully');
        }

    }

    public function destroy($id) {
        $package = Package::find($id);
        $package->delete();

        return back()->withToastSuccess('Package deleted successfully');
    }

}
