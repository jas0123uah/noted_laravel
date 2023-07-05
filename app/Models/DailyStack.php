<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dailystack extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function notecards(){
        return $this->hasMany(Notecard::class);
    }
    public function stacks(){
        return $this->hasMany(Stack::class);
    }
    use HasFactory;
}
