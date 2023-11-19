<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts   = [
        'permission' => 'array', // Will convarted to (Array)
    ];

    public function getPackageTypeAttribute() {

        if ($this->type == 1) {
            return 'Course Base(Package without any limited exam)';
        } else {
            return 'Exam Base(Package with limited exam)';
        }

    }

}
