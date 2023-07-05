<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\TestHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
class Notecards extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions; //rolls back db transactions that occur during test
    public function test_notecard_lifecycle(): void
    {
        //Create a User
        $myself = TestHelper::createFakeUser("Jay", "Spencer");
        
        
        $stack = [
            "name" => "Test stack",
        ];
        $other_stack = [
            "name" => "Other stack",
        ];
        //Create a stack
        $stack_id = $this->actingAs($myself)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];

        //Create a Other stack, which we will move a notecard to later
        $other_stack_id = $this->actingAs($myself)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];
        
        
        //Create a notecard for user
        $notecard = [
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "stack_id" => $stack_id,
        ];
        $notecard_id = $this->actingAs($myself)->post("/api/notecards/", $notecard)->json()["data"]["notecard_id"];
        //The stack is in the db
        $this->assertDatabaseHas('stacks',[
            'stack_id' => $stack_id,
            "name" => "Test stack",
            "user_id" => $myself["user_id"]
        ]);
        //The notecard is in the db
        $this->assertDatabaseHas('notecards',[
            "notecard_id" => $notecard_id,
            'stack_id' => $stack_id,
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "repetition" => 0,
            "e_factor" => 2.5,
            "user_id" => $myself["user_id"]
        ]);

        //I can get the notecard I created
        $response = $this->actingAs($myself)->get("/api/notecards/{$notecard_id}")->json();
        $this->assertEquals([
            "notecard_id" => $response["data"]["notecard_id"], 
            "user_id" => $response["data"]["user_id"], 
            "stack_id" => $response["data"]["stack_id"],
            "front" => "The front of the notecard",
            "back" => "The back of the notecard", 
            "e_factor" => 2.5,
            "repetition" => 0,
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "next_repetition" => now()->addDay()->startOfDay()
        ], $response["data"]);

        //I can update the front & back of the notecard and move it to another stack of my own
        $response = $this->actingAs($myself)->put("/api/notecards/{$notecard_id}", [
            "front" => "Updated front",
            "back" => "Updated back",
            "stack_id" => $other_stack_id,
        ])->json();

        //I can study the notecard
        $study_notecard = [
            "quality" => 5,
        ];
        $response = $this->actingAs($myself)->put("/api/notecards/{$notecard_id}", $study_notecard)->json();

        
        $this->assertEquals([
            "notecard_id" => $response["data"]["notecard_id"], 
            "user_id" => $response["data"]["user_id"], 
            "stack_id" => $response["data"]["stack_id"],
            "front" => "Updated front",
            "back" => "Updated back", 
            "e_factor" => 2.6,
            "repetition" => 1,
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "next_repetition" => now()->addDays(6)->startOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ], $response["data"]);

        //If I give a poor response the notecards repetitions are reset, the 

        $study_notecard = [
            "quality" => 0,
        ];

        $response = $this->actingAs($myself)->put("/api/notecards/{$notecard_id}", $study_notecard)->json();

        $this->assertEquals([
            "notecard_id" => $response["data"]["notecard_id"], 
            "user_id" => $response["data"]["user_id"], 
            "stack_id" => $response["data"]["stack_id"],
            "front" => "Updated front",
            "back" => "Updated back", 
            "e_factor" => 1.8,
            "repetition" => 1,
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "next_repetition" => now()->addDays(1)->startOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ], $response["data"]);


        //I can delete the notecard
        $response = $this->actingAs($myself)->delete("/api/notecards/{$notecard_id}")->json();
        //dd($response);
        $this->assertEquals([
            "notecard_id" => $response["data"]["notecard_id"], 
            "user_id" => $response["data"]["user_id"], 
            "stack_id" => $response["data"]["stack_id"],
            "front" => $response["data"]["front"],
            "back" => $response["data"]["back"], 
            "e_factor" => $response["data"]["e_factor"],
            "repetition" => $response["data"]["repetition"],
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
        ], $response["data"]);

        //The notecard is no longer in the db

        $this->assertDatabaseMissing('notecards',[
            "notecard_id" => $response["data"]["notecard_id"], 
            "user_id" => $response["data"]["user_id"], 
            "stack_id" => $response["data"]["stack_id"],
            "front" => $response["data"]["front"],
            "back" => $response["data"]["back"], 
            "e_factor" => $response["data"]["e_factor"],
            "repetition" => $response["data"]["repetition"],
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
        ]);

        //The stack the notecard was in still exists
        $this->assertDatabaseHas('stacks',[
            'stack_id' => $stack_id,
            "name" => "Test stack",
            "user_id" => $myself["user_id"]
        ]);
    }

    public function test_notecard_unauthenticated(): void
    {
        $notecard = [
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "stack_id" => "1",

        ];
        $create_notecard_res = $this->post("/api/notecards", $notecard);
        $get_notecard_res = $this->get("/api/notecards/1");
        $update_notecard_res = $this->put("/api/notecards/1");
        $delete_notecard_res = $this->delete("/api/notecards/1");
        $responses = [$create_notecard_res, $get_notecard_res, $update_notecard_res, $delete_notecard_res];
        foreach ($responses as $response) {
            $response->assertRedirect('/login');
        }
    }
    public function test_notecard_unauthorized(): void
    {
        $myself = TestHelper::createFakeUser("Jay", "Spencer");
        $user = TestHelper::createFakeUser("John", "Doe");
        //Create a stack for $user
        $stack = [
            "name" => "Test stack",
        ];
        
        //create a stack
        $stack_id = $this->actingAs($user)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];
        $notecard = [
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "stack_id" => $stack_id,
        ];

        //create a notecard for user
        $notecard_id = $this->actingAs($user)->post("/api/notecards/", $notecard)->json()["data"]["notecard_id"];

        //Try to do RUD on notecards
        $get_response = $this->actingAs($myself)->get("/api/notecards/{$notecard_id}")->json();
        $put_response = $this->actingAs($myself)->put("/api/notecards/{$notecard_id}", [
            "name" => "New name"
        ])->json();
        $delete_response = $this->actingAs($myself)->delete("/api/notecards/{$notecard_id}")->json();
        $responses = [$get_response, $put_response, $delete_response];
        foreach ($responses as $response) {
            $this->assertEquals([
                "message" => "NOTECARD_NOT_FOUND"
            ], $response);
        }
    }
    public function test_notecard_invalid_data(): void
    {
        $user = TestHelper::createFakeUser("John", "Doe");
        //Create a stack for $user
        $stack = [
            "name" => "Test stack",
        ];
        
        $stack_id = $this->actingAs($user)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];
        $notecard = [
            "front" => "NOT My front of the notecard",
            "back" => "NOT My back of the notecard",
            "stack_id" => $stack_id,
        ];


        //create a notecard for user
        $notecard_id = $this->actingAs($user)->post("/api/notecards/", $notecard)->json()["data"]["notecard_id"];


        // Create a stack for myself

        $myself = TestHelper::createFakeUser("Jay", "Spencer");
        //Create a stack for myself
        $stack = [
            "name" => "My stack",
        ];
        
        $my_stack_id = $this->actingAs($myself)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];

        $notecard = [
            "front" => "My front of the notecard",
            "back" => "My back of the notecard",
            "stack_id" => $stack_id,
        ];
        //Try and create a notecard and associate it with someone else's stack
        $response = $this->actingAs($myself)->post("/api/notecards/", $notecard)->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);

        //Post the notecard in my own stack

        $notecard["stack_id"] = $my_stack_id;
        $response = $this->actingAs($myself)->post("/api/notecards/", $notecard)->json();
        $my_notecard_id = $response["data"]["notecard_id"];

        //Try to POST a notecard and associate it with a stack that does not exist
        $notecard["stack_id"] = "100000";
        $response = $this->actingAs($myself)->post("/api/notecards/", $notecard)->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);
        $response = $this->actingAs($myself)->put("/api/notecards/{$my_notecard_id}", $notecard)->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);

        //Try to PUT a notecard of my own and associate it with a stack that does not exist
        
        //Then try and associate it with someone elses stack via PUT
        $notecard["stack_id"] = $stack_id;
        $response = $this->actingAs($myself)->put("/api/notecards/{$my_notecard_id}", $notecard)->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);
        

        $notecard = [
            "front" => "",
            "back" => "",
            "stack_id" => "",
        ];
        //No updating a notecard to be without front, back, or stack id
        $response = $this->actingAs($user)->put("/api/notecards/{$notecard_id}", $notecard);
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            0 => "The front field is required.",
            1 => "The back field is required.",
            2 => "The stack id field is required.",
        ], $errors);

        
        //No updating a notecard to be without front, back, or stack id
        $response = $this->actingAs($user)->post("/api/notecards/", $notecard);
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            0 => "The front field is required.",
            1 => "The back field is required.",
            2 => "The stack id field is required.",
        ], $errors);

        //No deleting nonexistent notecards

        $response = $this->actingAs($user)->delete("/api/notecards/100000")->json();
        $this->assertEquals([
                "message" => "NOTECARD_NOT_FOUND"
        ], $response);
    }
}
