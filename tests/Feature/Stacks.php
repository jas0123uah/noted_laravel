<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
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
        
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'myPassword',
            'password_confirmation' => 'myPassword',
        ];
        //I can register
        $response = $this->post('/register', $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
        ]);

        //I'm in the db
        $user_id = DB::table('users')->where('first_name', 'John')->value('user_id');
        $user = User::find($user_id);
        $stack = [
            "name" => "Test stack",
        ];
        //I can create a stack
        $this->actingAs($user)->post('/api/stacks/', $stack);
        
        $user_get = $this->actingAs($user)->getJson("/api/users/{$user_id}")->json()["data"];
        //dd($user_get);
        // $user = $this->getJson("/api/users/{$user_id}")->json()["data"][0];
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
            "stack_id" => $response["stack"]["stack_id"],
            "created_at" => $response["stack"]["created_at"],
            "updated_at" => $response["stack"]["updated_at"],
            "user_id" => $user_id,
            "notecards" => $response["stack"]["notecards"],
            "name" => 'Updated name'
        ], $response["stack"]);

        //Add a notecard
        $notecard = [
            "front" => "The front of the notecard",
            "back" => "The back of the notecard",
            "stack_id" => $response["stack"]["stack_id"],

        ];
        $response = $this->actingAs($user)->post("/api/notecards/", $notecard)->json();
    
        //I can get the stack and it returns the notecards associated with it
        $response = $this->actingAs($user)->get("/api/stacks/{$stack_id}")->json();
        //dd($response);
        $this->assertEquals([
            "stack_id" => $response["stack"]["stack_id"],
            "created_at" => $response["stack"]["created_at"],
            "updated_at" => $response["stack"]["updated_at"],
            "user_id" => $user_id,
            "name" => "Test stack",
            "notecards" => [
                [
                    "notecard_id" => $response["stack"]["notecards"][0]["notecard_id"],
                    "user_id" => $user_id,
                    "stack_id" => $response["stack"]["stack_id"],
                    "front" => "The front of the notecard",
                    "back" => "The back of the notecard",
                    "e_factor" => 2.5,
                    "repetition" => 0,
                    "created_at" => $response["stack"]["notecards"][0]["created_at"],
                    "updated_at" => $response["stack"]["notecards"][0]["updated_at"],
                ],
            ],
        ], $response["stack"]);







        //I can delete a stack and the notecards with it are removed
        $response = $this->actingAs($user)->delete("/api/stacks/{$stack_id}")->json();

        $this->assertEquals([
            "stack_id" => $response["stack"]["stack_id"],
            "created_at" => $response["stack"]["created_at"],
            "updated_at" => $response["stack"]["updated_at"],
            "user_id" => $user_id,
            "name" => "Test stack",
            "notecards" => [
                [
                    "notecard_id" => $response["stack"]["notecards"][0]["notecard_id"],
                    "user_id" => $user_id,
                    "stack_id" => $response["stack"]["stack_id"],
                    "front" => "The front of the notecard",
                    "back" => "The back of the notecard",
                    "e_factor" => 2.5,
                    "repetition" => 0,
                    "created_at" => $response["stack"]["notecards"][0]["created_at"],
                    "updated_at" => $response["stack"]["notecards"][0]["updated_at"],
                ],
            ],
        ], $response["stack"]);

        //The database no longer has the notecard associated with the stack
        $this->assertDatabaseMissing('notecards', [
            "notecard_id" => $response["stack"]["notecards"][0]["notecard_id"]
        ]);


    }
}
