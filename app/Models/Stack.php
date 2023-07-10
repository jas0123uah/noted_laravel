<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    use HasFactory;

    /**
     * Get the primary key column name.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'stack_id';
    }

    public function notecards()
    {
        return $this->hasMany(Notecard::class, 'stack_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function review_notecards() {
        return $this->hasMany(Reviewnotecard::class, 'stack_id');
        
    }

    protected $fillable = [
        'name',
        'user_id'
    ];
}
