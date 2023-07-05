<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Mail;
use App\Mail\NotedMailable;
use App\Models\Notecard;
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
    // Get the current date
    $currentDate = now()->toDateString();

    // Retrieve the notecards for the daily stack
    $notecards = Notecard::getItemsForReview();

    // Group notecards by user
    $grouped_notecards = [];
    foreach ($notecards as $notecard) {
        $user = $notecard->user;
        $grouped_notecards[$user->id][] = $notecard;
    }

    // Array to store error messages
    $errors = [];

    foreach ($grouped_notecards as $user_id => $notecards) {
        $example_notecard = $notecards[0];
        foreach ($notecards as $notecard) {
            dd($notecard);
            # code...
        }

        try {
            // Send the daily stack email
            //Mail::to($user->email)->send(new NotedMailable($data));

            // Log success message
            Log::info("Daily stack email sent to {$user->email}.");

        } catch (\Throwable $th) {
            // Log the error message
            $errors[] = "Failed to email daily stack to {$user->email}. Error: {$th->getMessage()}";
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
