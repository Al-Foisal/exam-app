<?php

use App\Models\Exam;
use Illuminate\Support\Facades\DB;

function multiKeyExists($arr, $key) {

    if (!is_array($arr)) {
        $arr = explode(':', $arr);
    }

    if (array_key_exists($key, $arr)) {
        return true;
    }

    return false;
}

function getPreli($id) {
    $data = Exam::find($id) ?? [];

    return $data;
}

function sendSMS($phone, $otp) {
    $to      = $phone;
    $token   = "10312213457170187689785afbc1f9a74ff9c04faa745a13d7212";
    $message = "Your OTP is " . $otp;

    $url = "http://api.greenweb.com.bd/api.php?json";

    $data = [
        'to'      => "$to",
        'message' => "$message",
        'token'   => "$token",
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
}
