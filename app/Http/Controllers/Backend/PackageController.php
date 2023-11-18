<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PackageController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $data            = [];
        $data['package'] = Package::latest()->get();

        return view('backend.package.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createOrEdit($id = null) {
        $data = [];

        if ($id) {
            $data['package'] = Package::find($id);
        } else {
            $data['package'] = null;
        }

        return view('backend.package.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
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
                'image'      => $final_name1 ?? null,
            ]);

            return back()->withToastSuccess('Package created successfully');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }

}
