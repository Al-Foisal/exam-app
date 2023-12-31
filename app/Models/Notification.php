<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    use HasFactory;
    protected $guarded = [];
    public function user() {
        return $this->belongsTo(USer::class);
    }

    public function written() {
        return $this->belongsTo(Written::class);
    }

    public function package() {
        return $this->belongsTo(Package::class);
    }
}
