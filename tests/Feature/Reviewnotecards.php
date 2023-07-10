<?php

namespace Tests\Feature;

use App\Models\Notecard;
use App\Models\Reviewnotecard;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Helpers\TestHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
class Reviewnotecards extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions; //rolls back db transactions that occur during test
    public function test_reviewnotecards_lifecycle(): void
    {
        $subscribed_and_verified = TestHelper::createFakeUser("Jay", "Spencer", true, false);
        $not_subscribed_or_verified = TestHelper::createFakeUser("Not", "SubscribedOrVerified", false, true);
        $not_subscribed_but_verified = TestHelper::createFakeUser("NotSubscribed", "ButVerified", true, true);
        $subscribed_but_not_verified = TestHelper::createFakeUser("Subscribed", "ButNotVerified", false, false);

        $users = [$subscribed_and_verified,$not_subscribed_or_verified, $not_subscribed_but_verified, $subscribed_but_not_verified];
        $subscribed_and_verified_user_id = $subscribed_and_verified["user_id"];
        $nc_ids_and_s_ids_to_rev = [];

        //Create 2 stacks and 2 notecards in each stack for all 4 users
        foreach ($users as $index => $user) {
            foreach(range(0,1) as $stack_number ){
                $response = $this->actingAs($user)->post('/api/stacks/', [
                    'name' => 'Stack '. $stack_number .' User '. $user["user_id"],
                ])->json();
                $stack_id = $response["data"]["stack_id"];
                
                $potential_notecard_to_review = $this->actingAs($user)->post('/api/notecards', [
                    "front" => "Notecard front review",
                    "back" => "Notecard back review",
                    "stack_id" => $stack_id
                ])->json();
                //Only mark it for review if the user is subscribed and verified
                if($user->email_verified_at && !$user->is_unsubscribed){
                    $notecard_to_review_id = $potential_notecard_to_review["data"]["notecard_id"];
                    $nc_ids_and_s_ids_to_rev[$notecard_to_review_id] = $stack_id;
                }

                $notecard_to_ignore_id = $this->actingAs($user)->post('/api/notecards', [
                    "front" => "Notecard front do not review",
                    "back" => "Notecard back do not review",
                    "stack_id" => $stack_id
                ])->json();

            }
        }

        $nc_ids_to_rev = array_keys($nc_ids_and_s_ids_to_rev);

        $nc_ids_to_rev = implode(',', $nc_ids_to_rev); // Convert the array to a comma-separated string

        //Manually set the next_repetition for some of the notecards to be yesterday 
        DB::statement("UPDATE notecards nc
            SET nc.next_repetition = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            WHERE nc.notecard_id IN ({$nc_ids_to_rev})
            ");
            
        // `POST` /api/reviewnotecards - I can create review notecards for all users who are both subscribed & verified
        //(Testing that only those ncs are added to the ds table. The notecards can be in any stack, and it only is added if you are verified and subscribed)
        
        $this->actingAs($subscribed_and_verified)->post('/api/reviewnotecards');
        $this->assertEquals(2, Reviewnotecard::all()->count());
        
        //The two notecards in the 2 stacks the verified & subscribed user needs to review
        //If I run the api endpoint again no new notecards are added
        $this->actingAs($subscribed_and_verified)->post('/api/reviewnotecards');
        $this->assertEquals(2, Reviewnotecard::all()->count());
        // If I run the function used to identify notecards that need to be study after review notecards are created  for the day, no notecards are returned --> meaning no emails are sent
        $review_items = Notecard::getItemsForReview();
        $this->assertEquals(0, count($review_items));
        
        //  If I am logged in, my review notecards are returned to me
        $response = $this->actingAs($subscribed_and_verified)->get("/api/reviewnotecards/{$subscribed_and_verified_user_id}")->json();
        //OVERALL PREMISE OF CHECKS BELOW: Ensure ALL review notecards are returned and nothing else
        //We say there are only two notecards to review
        $this->assertEquals(2, count($nc_ids_and_s_ids_to_rev));
        //The api says there are only two cards to review
        $this->assertEquals(2, count($response["data"]));

        //The two notecards the api says we need to review are the ones we were expecting to review and they are matched up with their appropriate stacks
        foreach ($response["data"] as $notecard) {
            $notecard_id = $notecard["notecard_id"];
            $stack_id = $notecard["stack_id"];
            $this->assertEquals(true, isset($nc_ids_and_s_ids_to_rev[$notecard_id]) && $nc_ids_and_s_ids_to_rev[$notecard_id] === $stack_id);
        }

        //Delete all review notecards
        DB::select("DELETE FROM review_notecards");

        $this->assertEquals(0, Reviewnotecard::all()->count());


        //Test lazy-loading review notecards 
        $response = $this->actingAs($subscribed_and_verified)->get("/api/reviewnotecards/{$subscribed_and_verified_user_id}")->json();


        //The api says there are only two cards to review
        $this->assertEquals(2, count($response["data"]));

        //The two notecards the api says we need to review are the ones we were expecting to review and they are matched up with their appropriate stacks
        foreach ($response["data"] as $notecard) {
            $notecard_id = $notecard["notecard_id"];
            $stack_id = $notecard["stack_id"];
            $this->assertEquals(true, isset($nc_ids_and_s_ids_to_rev[$notecard_id]) && $nc_ids_and_s_ids_to_rev[$notecard_id] === $stack_id);
        }

        //There are now 2 notecards in the db
        $this->assertEquals(2, Reviewnotecard::all()->count());
        

    }
    public function test_reviewnotecards_unauthenticated() : void {
        //`GET /api/reviewnotecards/{userid}` - If I am not logged in, my review notecards are not returned to me

        $subscribed_and_verified = TestHelper::createFakeUser("Jay", "Spencer", true, false);

    

        //Create 2 stacks and 2 notecards in each stack for subscribed and verified user
        foreach(range(0,1) as $stack_number ){
            $response = $this->actingAs($subscribed_and_verified)->post('/api/stacks/', [
                'name' => 'Stack '. $stack_number .' User '. $subscribed_and_verified["user_id"],
            ])->json();
            $stack_id = $response["data"]["stack_id"];
            
            $this->actingAs($subscribed_and_verified)->post('/api/notecards', [
                "front" => "Notecard front review",
                "back" => "Notecard back review",
                "stack_id" => $stack_id
            ])->json();

            $this->actingAs($subscribed_and_verified)->post('/api/notecards', [
                "front" => "Notecard front do not review",
                "back" => "Notecard back do not review",
                "stack_id" => $stack_id
            ])->json();

        }
        Auth::logout();

        $response = $this->get("api/reviewnotecards/{$subscribed_and_verified["user_id"]}");
        $response->assertRedirect('/login');

        $response = $this->post("api/reviewnotecards/");
        $response->assertRedirect('/login');

    }

    public function test_reviewnotecards_unauthorized() : void {
        //`GET /api/reviewnotecards/{userid}` - If I am logged in, I can’t see another user’s review notecards.
        $subscribed_and_verified = TestHelper::createFakeUser("Jay", "Spencer", true, false);
        $other_user = TestHelper::createFakeUser("John", "Doe", true, false);

    

        //Create 2 stacks and 2 notecards in each stack for subscribed and verified user
        foreach(range(0,1) as $stack_number ){
            $response = $this->actingAs($subscribed_and_verified)->post('/api/stacks/', [
                'name' => 'Stack '. $stack_number .' User '. $subscribed_and_verified["user_id"],
            ])->json();
            $stack_id = $response["data"]["stack_id"];
            
            $this->actingAs($subscribed_and_verified)->post('/api/notecards', [
                "front" => "Notecard front review",
                "back" => "Notecard back review",
                "stack_id" => $stack_id
            ])->json();

            $this->actingAs($subscribed_and_verified)->post('/api/notecards', [
                "front" => "Notecard front do not review",
                "back" => "Notecard back do not review",
                "stack_id" => $stack_id
            ])->json();

        }
        Auth::logout();
        //Can't get someone elses review notecards
        $response = $this->actingAs($other_user)->get("api/reviewnotecards/{$subscribed_and_verified["user_id"]}")->json();
        $this->assertEquals([
            "message"=> "USER_NOT_FOUND"
        ],$response);
    }
    public function test_reviewnotecards_invalid_data() : void {
       // `GET /api/reviewnotecards/{userid}` - I can’t get review notecards for a non-existent user

        $subscribed_and_verified = TestHelper::createFakeUser("Jay", "Spencer", true, false);
        $response = $this->actingAs($subscribed_and_verified)->get("api/reviewnotecards/100000")->json();
        $this->assertEquals([
            "message"=> "USER_NOT_FOUND"
        ],$response);
    }
}
