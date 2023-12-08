<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model {
    use HasFactory, SoftDeletes;
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

    public function packageHistory() {
        return $this->hasMany(PackageHistory::class);
    }

}
