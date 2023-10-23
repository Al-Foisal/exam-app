<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WrittenAnswer extends Model {
    use HasFactory;
    protected $guarded = [];

    public function writtenAnswerQuestion() {
        return $this->hasMany(WrittenAnswerQuestion::class);
    }
}
