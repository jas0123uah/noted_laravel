<?php

namespace Tests\Feature;


use Tests\TestCase;
use Tests\Helpers\TestHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
class Users extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions; //rolls back db transactions that occur during test
    
    public function test_user_lifecycle(): void
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

        $response =  $this->actingAs($user)->post("/api/stacks/", [
            "name" => "My first stack"

        ])->json();
        $user_get = $this->actingAs($user)->getJson("/api/users/{$user_id}")->json()["data"];
        
        //Create a stack
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
                    "name" => "My first stack"

                ]
            ]

        ], $user_get);


        //I can update the user
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];

        $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);


        //The user is updated

        $user_get = $this->getJson("/api/users/{$user_id}")->json()["data"];
        $this->assertEquals([
            "user_id" => $user_id,
            "first_name" => "Johnny",
            "last_name" => "Doe II",
            "email" => "johnnydoe@example.com",
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
                    "name" => "My first stack"

                ]
            ]

        ], $user_get);

        //I can confirm the user's email

        $user = User::find($user_id);
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(302)->assertSessionHas('verified', true);

        $this->assertTrue($user->hasVerifiedEmail());
        //I can unsubscribe
        $this->actingAs($user)->get("/unsubscribe/{$user->subscription_token}");

        //I can change my password

        //I can delete a user
        $response = $this->delete("/api/users/{$user_id}");

        $response->assertJsonFragment([
            'user_id' => $user_id,
            "first_name" => "Johnny",
            "last_name" => "Doe II",
            "email" => "johnnydoe@example.com",
            "email_verified_at" => $user["email_verified_at"],
            "is_unsubscribed" => true,
            "created_at" => $user["created_at"],
            // Add other user attributes you expect in the response
        ]);


        //The database no longer has the user
        $this->assertDatabaseMissing('users', [
            "user_id" => $user_id
        ]);
    }
    public function test_user_unauthenticated() : void {
        //Register someone else as a user.
        $user = TestHelper::createFakeUser("Jane", "Smith");
        $not_my_user_id = $user["user_id"];
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];
        
        $this->resetAuth(); 
        
        //Ensure we are a guest
        $this->assertGuest();
        //Can't get a user if you aren't logged in
        $response_get = $this->get("/api/users/{$not_my_user_id}");
        $response_update = $this->put("/api/users/{$not_my_user_id}/", $updates);
        $response_delete = $this->delete("/api/users/{$not_my_user_id}/");
        $responses = [$response_get, $response_update, $response_delete ];
        foreach ($responses as $response) {
            $response->assertRedirect('/login');
        }
    }
    public function test_user_unauthorized(): void
    {
        //Register someone else as a user. (we do not have access to this person's account)
        $user = TestHelper::createFakeUser("Jane", "Smith");
        $not_my_user_id = $user["user_id"];

        //Register myself as a user.
        $myself = TestHelper::createFakeUser("Jay", "Spencer");
        //Can't get someone that is not yourself
        $response = $this->actingAs($myself)->get("/api/users/{$not_my_user_id}")->json();
        $this->assertEquals([
            "message" => "USER_NOT_FOUND"
        ], $response);
        //Can't update someone that's not yourself
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];

        $response = $this->actingAs($myself)->put("/api/users/{$not_my_user_id}/", $updates)->json();
        $this->assertEquals([
            "message" => "USER_NOT_FOUND"
        ], $response);
        

        $response = $this->actingAs($myself)->delete("/api/users/{$not_my_user_id}/")->json();
        $this->assertEquals([
            "message" => "USER_NOT_FOUND"
        ], $response);
    }
    public function test_user_invalid_data() : void {
        //Create the two users we're going to be testing with

        $user = TestHelper::createFakeUser("Jay", "Spencer");
        TestHelper::createFakeUser("Jane", "Smith");
        $data = [
        ];
        $response = $this->post('/register', $data);
        
        $response->assertStatus(302); // Assert initial response is a redirect

        $response->assertSessionHasErrors([
            "first_name" => "The first name field is required.",
            "last_name" => "The last name field is required.",
            "email" => "The email field is required.",
            "password" => "The password field is required.",
        ]);


        $data = [
            'first_name' => Str::random(256),
            'last_name' => Str::random(256),
            'email' => 'notAnEmail',
            'password' => 'myPassword',
            'password_confirmation' => 'myPassword!',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            "first_name" => "The first name field must not be greater than 255 characters.",
            "last_name" => "The last name field must not be greater than 255 characters.",
            "email" => "The email field must be a valid email address.",
            "password" => "The password field confirmation does not match.",
        ]);

        //No dupe emails

        $data = [
            'first_name' => 'Jay',
            'last_name' => 'Spencer',
            'email' => 'jayspencer@example.com',
            'password' => 'myPassword!',
            'password_confirmation' => 'myPassword!',
        ];

        
        $this->resetAuth();
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            "email" => "The email has already been taken.",
        ]);
        $long_string = Str::random(255);
        $data["email"] = "{$long_string}@example.com";

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            "email" => "The email field must not be greater than 255 characters.",
        ]);

        $updates = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
        ];

        $user_id = $user["user_id"];
        $user = User::find($user_id);

        $response = $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);

        $response->assertSessionHasErrors([
            "first_name" => "The first name field must be at least 1 characters.",
            "last_name" => "The last name field must be at least 1 characters.",
            "email" => "The email field must be at least 1 characters.",
        ]);


        //Try to update user's email to someone else's email

        $updates = [
            'first_name' => 'Jay',
            'last_name' => 'Spencer',
            'email' => 'janesmith@example.com',
        ];

        $response = $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);
        $this->assertEquals(session()->get('errors')->get('email')[0], "The email has already been taken.");
        $updates = [
            'first_name' => Str::random(256),
            'last_name' => Str::random(256),
            'email' => "{$long_string}@example.com",
        ];
        $response = $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);
        $errors = session()->get('errors')->all();
        $this->assertEquals([
            "0" => "The first name field must not be greater than 255 characters.",
            "1" => "The last name field must not be greater than 255 characters.",
            "2" => "The email field must not be greater than 255 characters.",
        ], $errors);
    }
}
