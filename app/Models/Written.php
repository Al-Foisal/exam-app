<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Written extends Model {
    use HasFactory;
    protected $guarded = [];

    public function writtenQuestion() {
        return $this->hasMany(WrittenQuestion::class);
    }

    protected $casts = [
        'published_at' => 'datetime: Y-m-d H:i:s',
        'expired_at'   => 'datetime: Y-m-d H:i:s',
    ];

    public function userAnswer() {
        return $this->hasOne(WrittenAnswer::class)->withDefault('user_id', Auth::id());
    }
}
