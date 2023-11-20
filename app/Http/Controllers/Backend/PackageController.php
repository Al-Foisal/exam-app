<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PackageController extends Controller {
    public function index() {
        $data            = [];
        $data['package'] = Package::latest()->paginate();

        return view('backend.package.index', $data);
    }

    public function createOrEdit($id = null) {
        $data = [];

        if ($id) {
            $data['package'] = Package::find($id);
        } else {
            $data['package'] = null;
        }

        return view('backend.package.create', $data);
    }

    public function storeOrUpdate(Request $request, $id = null) {

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

            return back()->withToastSuccess('Package created successfully');
        }

    }

    public function destroy($id) {
        $package = Package::find($id);
        $package->delete();

        return back()->withToastSuccess('Package deleted successfully');
    }

}
