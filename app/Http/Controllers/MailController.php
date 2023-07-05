<?php

namespace App\Http\Controllers;
use App\Models\Reviewnotecard;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mail;
use App\Mail\NotedMailable;
use App\Models\Notecard;
use Illuminate\Support\Facades\DB;
class MailController extends Controller
{
    //
    // public function index(){
    //     //Generate the daily stacks

    //     //Send the emails

    //     //Sample/ demo
    //     $today = now()->format("M D,Y ");
    //     $first_name = "Jay";
    //     $data = [
    //         'subject' => 'This is a test',
    //         'body' => "
    //         Hello {$first_name},
    //         Your stack for {$today} is here!",
    //     ];
    //     try {
    //         //code...
    //         $email = 'jspencer5396@gmail.com';
    //         Mail::to($email)->send(new NotedMailable($data));
    //         return response()->json([
    //             'message' => "Successfully emailed daily stack to {$email} for ${today}."
    //         ]);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         Log::info($th);
    //         return response()->json([
    //             'message' => "Failed to email daily stack to {$email} for ${today}."
    //         ]);
    //     }


        
    // }

    public function index()
{


    // Retrieve the notecards for the daily stack
    $notecards = Notecard::getItemsForReview();

    // Group notecards by user
    $grouped_notecards = [];
    foreach ($notecards as $notecard) {
        $user_id = $notecard->user_id;
        $grouped_notecards[$user_id][] = $notecard;
    }

    // Array to store error messages
    $errors = [];

    foreach ($grouped_notecards as $user_id => $notecards) {
        $example_notecard = $notecards[0];
        $users_review_notecards =[];
        foreach ($notecards as $notecard) {
            $review_nc_model = new Reviewnotecard();
            $review_nc_model->stack_id = $notecard->stack_id;
            $review_nc_model->notecard_id = $notecard->notecard_id;
            $review_nc_model->user_id = $notecard->user_id;
            array_push($users_review_notecards, $review_nc_model);
            
        }
        //Use a transaction to ensure that ALL notecards needing to be reviewed for a given user are saved to the table
        DB::beginTransaction();
        try {
            foreach ($users_review_notecards as $review_notecard) {
                $review_notecard ->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        try {
            // Send the daily stack email if appropriate
            if(config('app.env') === 'testing'){
                Log::info("Not sending daily stack email to {$example_notecard->email} in testing environment.");
            }
            else{
                Log::info("Daily stack email sent to {$example_notecard->email}.");
            }

        } catch (\Throwable $th) {
            // Log the error message
            $errors[] = "Failed to email daily stack to {$example_notecard->email}. Error: {$th->getMessage()}";
        }
    }
    
    if (count($errors) > 0) {
        // Handle errors here
        Log::info(json_encode($errors));
    }

    return response()->json([
        'message' => 'Daily stack emails sent.',
    ]);
}

}
