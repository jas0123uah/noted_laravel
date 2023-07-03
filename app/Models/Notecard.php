<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notecard extends Model
{
    use HasFactory;

    /**
     * Get the primary key column name.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'notecard_id';
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function stack(){
        return $this->belongsTo(Stack::class);
    }
    public function daily_stack(){
        return $this->belongsTo(DailyStack::class);
    }
    protected $fillable = [
        'user_id',
        'stack_id',
        'front',
        'back',
        'e_factor',
        'repetition',
    ];
}
