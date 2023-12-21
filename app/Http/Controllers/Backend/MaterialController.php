<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Subject;
use App\Models\TopicSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MaterialController extends Controller {
    public function index() {
        $data     = [];
        $material = Material::where('category', request()->ref)->latest()->paginate(20);
        $list     = [];

        foreach ($material as $item) {
            $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
            $item['topics']   = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
            $list[]           = $item;
        }

        $data['material'] = $list;

        return view('backend.material.index', $data);
    }

    public function create($material_id = null) {
        $data             = [];
        $data['subjects'] = Subject::all();

        if ($material_id) {
            $data['material']     = $e     = Material::find($material_id);
            $data['topic_source'] = TopicSource::whereIn('subject_id', explode(',', $e->subject_id))->get();
        }

        return view('backend.material.create', $data);
    }

    public function storeOrUpdate(Request $request, $material_id = null) {

        if (!$material_id) {

            if ($request->hasFile('pdf')) {

                $image_file = $request->file('pdf');

                if ($image_file) {

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/material/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);

                }

            }

            Material::create([
                'category'   => $request->category,
                'subject_id' => implode(',', $request->subject_id),
                'topic_id'   => implode(',', $request->topic_id),
                'name'       => $request->name,
                'details'    => $request->details,
                'pdf'        => $final_name1 ?? null,
                'video'      => $request->video ?? null,
            ]);

            return back()->withToastSuccess('Material created successfully');
        } else {
            $material = Material::find($material_id);

            if ($request->hasFile('pdf')) {

                $image_file = $request->file('pdf');

                if ($image_file) {

                    $image_path = public_path($material->image);

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    $img_gen   = hexdec(uniqid());
                    $image_url = 'images/material/';
                    $image_ext = strtolower($image_file->getClientOriginalExtension());

                    $img_name    = $img_gen . '.' . $image_ext;
                    $final_name1 = $image_url . $img_gen . '.' . $image_ext;

                    $image_file->move($image_url, $img_name);
                    $material->pdf = $final_name1;
                    $material->save();

                }

            }

            $material->category   = $request->category;
            $material->subject_id = implode(',', $request->subject_id);
            $material->topic_id   = implode(',', $request->topic_id);
            $material->name       = $request->name;
            $material->details    = $request->details;
            $material->video      = $request->video ?? null;
            $material->save();

            return to_route('material.index', ['ref' => $request->category])->withToastSuccess('Material updated successfully');
        }

    }

    public function delete($id) {
        $data = Material::find($id);
        $data->delete();

        return back()->withToastSuccess('Material deleted successfully');
    }

//ajax response
    public function getTopic(Request $request) {
        $data = TopicSource::whereIn('subject_id', $request->subjects)->get();

        return json_encode($data);
    }

}
