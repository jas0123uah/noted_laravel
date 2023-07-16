<?php

namespace App\Http\Controllers;

use App\Models\Reviewnotecard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ReviewnotecardsController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        $start_of_day = Carbon::today()->startOfDay();
        $end_of_day = Carbon::today()->endOfDay();
        //
        $user = User::find(Auth::id());
        $user->deleteOldReviewNotecards();
        $review_notecards = Reviewnotecard::with('notecard')->where('user_id', Auth::id())
            -> whereBetween('created_at', [$start_of_day, $end_of_day])
            -> get();
        //Fail-safe/lazy-loading 
        if(count($review_notecards) === 0){
            $review_notecards = $user->makeReviewNotecards();
            DB::beginTransaction();
            try {
                foreach ($review_notecards as $review_notecard) {
                    $review_nc_model = new Reviewnotecard();
                    $review_nc_model->stack_id = $review_notecard->stack_id;
                    $review_nc_model->notecard_id = $review_notecard->notecard_id;
                    $review_nc_model->user_id = $review_notecard->user_id;
                    $review_nc_model ->save();
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        $message = 'Review notecards returned successfully!';
        if(!$user->email_verified_at){
            $message = 'Please verify your email to generate your review notecards.';
        }

        return response()->json([
            'data' => $review_notecards,
            'message' => $message

        ]);
    }
}
