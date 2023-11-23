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
