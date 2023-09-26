<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Written extends Model {
    use HasFactory;
    protected $guarded = [];

    public function writtenQuestion() {
        return $this->hasMany(WrittenQuestion::class);
    }
}
