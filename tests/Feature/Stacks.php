<?php

namespace Tests\Feature;
use App\Models\Notecard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\Helpers\TestHelper;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class Stacks extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions; //rolls back db transactions that occur during test
    
    public function test_stack_lifecycle(): void
    {
        
        $user = TestHelper::createFakeUser("John", "Doe");
        $user_id = $user["user_id"];
        $stack = [
            "name" => "Test stack",
        ];
        //I can create a stack
        $this->actingAs($user)->post('/api/stacks/', $stack);
        
        //$user_get = $this->actingAs($user)->getJson("/api/users/{$user_id}")->json()["data"];
        $user_get = $this->actingAs($user)->getJson("/api/users/{$user_id}")->json()["data"];
        //dd($user_get);
    
        $this->assertEquals([
            "user_id" => $user_id,
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "johndoe@example.com",
            "email_verified_at" => null,
            "is_unsubscribed" => false,
            "subscription_token" => $user_get["subscription_token"],
            "created_at" => $user_get["created_at"],
            "updated_at" => $user_get["updated_at"],
            "stacks" => [
                [
                    "stack_id" => $user_get["stacks"][0]["stack_id"],
                    "created_at" => $user_get["stacks"][0]["created_at"],
                    "updated_at" => $user_get["stacks"][0]["updated_at"],
                    "user_id" => $user_id,
                    "name" => "Test stack"

                ]
            ]


        ], $user_get);

        //I can update the stack name
        $updates = [
            "name" => "Updated name"
        ];

        
        $stack_id = $user_get["stacks"][0]["stack_id"]; 
        $response = $this->actingAs($user)->put("/api/stacks/{$stack_id}", $updates)->json();

        $this->assertEquals([
            "stack_id" => $response["data"]["stack_id"],
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "user_id" => $user_id,
            "notecards" => $response["data"]["notecards"],
            "name" => 'Updated name'
        ], $response["data"]);

        //Add a notecard
        $notecard = [
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "stack_id" => $response["data"]["stack_id"],

        ];
        $response = $this->actingAs($user)->post("/api/notecards/", $notecard)->json();
        
        //I can get the stack and it returns the notecards associated with it
        $response = $this->actingAs($user)->get("/api/stacks/{$stack_id}")->json();
        $this->assertEquals([
            "stack_id" => $response["data"]["stack_id"],
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "user_id" => $user_id,
            "name" => "Updated name",
            "notecards" => [
                [
                    "notecard_id" => $response["data"]["notecards"][0]["notecard_id"],
                    "user_id" => $user_id,
                    "stack_id" => $response["data"]["stack_id"],
                    "front" => "The front of the notecard",
                    "back" => "The back of the notecard",
                    "e_factor" => 2.5,
                    "repetition" => 0,
                    "created_at" => $response["data"]["notecards"][0]["created_at"],
                    "updated_at" => $response["data"]["notecards"][0]["updated_at"],
                ],
            ],
        ], $response["data"]);

        //I can delete a stack and the notecards with it are removed
        $response = $this->actingAs($user)->delete("/api/stacks/{$stack_id}")->json();

        $this->assertEquals([
            "stack_id" => $response["data"]["stack_id"],
            "created_at" => $response["data"]["created_at"],
            "updated_at" => $response["data"]["updated_at"],
            "user_id" => $user_id,
            "name" => "Updated name",
            "notecards" => [
                [
                    "notecard_id" => $response["data"]["notecards"][0]["notecard_id"],
                    "user_id" => $user_id,
                    "stack_id" => $response["data"]["stack_id"],
                    "front" => "The front of the notecard",
                    "back" => "The back of the notecard",
                    "e_factor" => 2.5,
                    "repetition" => 0,
                    "created_at" => $response["data"]["notecards"][0]["created_at"],
                    "updated_at" => $response["data"]["notecards"][0]["updated_at"],
                ],
            ],
        ], $response["data"]);

        //The database no longer has the notecard associated with the stack
        $this->assertDatabaseMissing('notecards', [
            "notecard_id" => $response["data"]["notecards"][0]["notecard_id"]
        ]);
    }
    public function test_stack_unauthenticated() : void {
        $stack = [
            "name" => "Test stack",
        ];
        $create_stack_res = $this->post("/api/stacks", $stack);
        $get_stack_res = $this->get("/api/stacks/1");
        $update_stack_res = $this->put("/api/stacks/1");
        $delete_stack_res = $this->delete("/api/stacks/1");
        $responses = [$create_stack_res, $get_stack_res, $update_stack_res, $delete_stack_res];
        foreach ($responses as $response) {
            $response->assertRedirect('/login');
        }
    }
    public function test_stack_unauthorized() : void {
        $user = TestHelper::createFakeUser("John", "Doe");

        $user_id = DB::table('users')->where('first_name', 'John')->value('user_id');
        $user = User::find($user_id);
        $stack = [
            "name" => "Test stack",
        ];
        //create a stack
        $stack_id = $this->actingAs($user)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];
        Auth::logout();
        
        
        //Create user for myself

        $myself = TestHelper::createFakeUser("Jay", "Spencer");


        $get_response = $this->actingAs($myself)->get("/api/stacks/{$stack_id}")->json();
        $put_response = $this->actingAs($myself)->put("/api/stacks/{$stack_id}", [
            "name" => "New name"
        ])->json();
        $delete_response = $this->actingAs($myself)->delete("/api/stacks/{$stack_id}")->json();
        $responses = [$get_response, $put_response, $delete_response];
        foreach ($responses as $response) {
            $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
            ], $response);
        }
    }

    public function test_stacks_invalid_data() : void {
        $user = TestHelper::createFakeUser("John", "Doe");
        $response = $this->actingAs($user)->get("/api/stacks/10000000")->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);

        $response = $this->actingAs($user)->post("/api/stacks/");
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            "0" => "The name field is required."
        ], $errors);

        $response = $this->actingAs($user)->post("/api/stacks/", ["name" => ""]);
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            "0" => "The name field is required."
        ], $errors);


        $response = $this->actingAs($user)->post("/api/stacks/", [
            "name" =>Str::random(256)
        ]);
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            "0" => "The name field must not be greater than 255 characters."
        ], $errors);

        //Create a stack

        $stack = [
            "name" => "Test stack",
        ];
        //I can create a stack
        $stack_id = $this->actingAs($user)->post('/api/stacks/', $stack)->json()["data"]["stack_id"];

        $response = $this->actingAs($user)->put("/api/stacks/{$stack_id}", [
            "name" => ""
        ]);
        $errors = session()->get('errors')->all();

        $this->assertEquals([
            "0" => "The name field must be a string.",
            "1" => "The name field must be at least 1 characters."
        ], $errors);


        $response = $this->actingAs($user)->put("/api/stacks/{$stack_id}", [
            "name" => Str::random(256)
        ]);
        $errors = session()->get('errors')->all();

        $this->assertEquals([
            "0" => "The name field must not be greater than 255 characters."
        ], $errors);

        $response = $this->actingAs($user)->delete("/api/stacks/10000000")->json();
        $this->assertEquals([
                "message" => "STACK_NOT_FOUND"
        ], $response);
        
    }
}
