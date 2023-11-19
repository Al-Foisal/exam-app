<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageHistory;
use Illuminate\Http\Request;

class SubscribtionController extends Controller {
    public function packages() {
        $data                = [];
        $data['course_base'] = Package::where('type', 1)->get();
        $data['exam_base']   = Package::where('type', 2)->latest()->paginate();

        return $this->successMessage('', $data);

    }

    public function purchasePackage(Request $request) {
        PackageHistory::create([
            'user_id'                 => $request->user_id,
            'package_id'              => $request->package_id,
            'amount'                  => $request->amount,
            'transaction_id'          => $request->transaction_id,
            'payment_method'          => $request->payment_method,
            'payment_method_identity' => $request->payment_method_identity,
        ]);

        return $this->successMessage();
    }

    public function packageHistory(Request $request) {
        $data                         = [];
        $data['present_subscribtion'] = PackageHistory::where('user_id', $request->user_id)->latest()->with('user', 'package')->first();
        $data['subscribtion_history'] = PackageHistory::where('user_id', $request->user_id)->latest()->with('user', 'package')->paginate();

        return $this->successMessage('', $data);
    }
}
