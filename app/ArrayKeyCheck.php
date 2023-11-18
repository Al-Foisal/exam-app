<?php

function multiKeyExists($arr, $key) {

    if (!is_array($arr)) {
        $arr = explode(':', $arr);
    }

    if (array_key_exists($key, $arr)) {
        return true;
    }

// foreach ($arr as $element) {

//     if (is_array($element)) {

//         if (multiKeyExists($element, $key)) {

//             return true;

//         }

//     }

    // }

    return false;
}
