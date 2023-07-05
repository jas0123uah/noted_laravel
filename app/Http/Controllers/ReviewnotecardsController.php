<?php

namespace App\Http\Controllers;

use App\Models\Reviewnotecard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ReviewnotecardsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        $start_of_day = Carbon::today()->startOfDay();
        $end_of_day = Carbon::today()->endOfDay();
        //
        $review_notecards = Reviewnotecard::where('user_id', Auth::id())
            -> whereBetween('created_at', [$start_of_day, $end_of_day])
            -> get();
        //Fail-safe/lazy-loading 
        $user = User::find(Auth::id());
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
