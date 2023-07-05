<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    public function review_notecard(){
        return $this->belongsTo(Reviewnotecard::class);
    }

    public function calculateNextRepetition()
    {
        if ($this->repetition == 1) {
            $this->next_repetition = now()->addDays(6)->startOfDay();
        } else if($this->repetition > 1) {
            $interval = $this->next_repetition->diffInDays();
            $this->next_repetition = $this->next_repetition->addDays($interval * $this->e_factor)->startOfDay();
        }
    }

    // After each repetition, assess the quality of the repetition response in a 0-5 grade scale.
    public function updateEFactor($quality)
    {
        $new_e_Factor = $this->e_factor + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        $this->e_factor = max(1.3, $new_e_Factor);
    }

    // If the quality response was lower than 3, restart repetitions for the item from the beginning
    // without changing the E-Factor.
    public function restartRepetitions()
    {
        $this->repetition = 0;
        $this->next_repetition = now()->addDay();
    }

    
    //Use this to get notecards needing review in daily stacks
    public static function getItemsForReview()
{
    return DB::select("
        SELECT n.notecard_id, n.user_id, n.stack_id, u.email 
        FROM notecards n
        JOIN users u ON u.user_id = n.user_id
        WHERE 
            n.next_repetition <= NOW() 
            AND u.email_verified_at IS NOT NULL 
            AND u.is_unsubscribed = FALSE
            AND u.user_id NOT IN (
                SELECT rn.user_id
                FROM review_notecards rn
                WHERE DATE(rn.created_at) = CURDATE()
            );
    ");
    // return self::where('next_repetition', '<=', now())
    //     ->whereHas('user', function ($query) {
    //         $query->whereNotNull('email_verified_at')
    //             ->where('is_subscribed', true);
    //     })
    //     ->get();
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
