<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviewnotecard extends Model
{
    protected $table = 'review_notecards';
    public function getKeyName()
    {
        return 'review_notecard_id';
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function notecard(){
        return $this->belongsTo(Notecard::class, 'notecard_id');
    }

    public function stack(){
        return $this->belongsTo(Stack::class);
    }
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'stack_id',
        'notecard_id',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'stack_id' => 'integer',
        'notecard_id' => 'integer',
    ];
}
