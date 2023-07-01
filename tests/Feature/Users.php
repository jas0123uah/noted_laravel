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
        $user_get = $this->actingAs($user)->getJson("/api/users/{$user_id}")->json()["data"][0];
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

        ], $user_get);

        //dd($user_get);

        //I can update the user
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];

        $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);


        //The user is updated

        $user_get = $this->getJson("/api/users/{$user_id}")->json()["data"][0];
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

        ], $user_get);

        //I can confirm the user's email

        $user = User::find($user_id);
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
        //dd($url);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(302)->assertSessionHas('verified', true);

        $this->assertTrue($user->hasVerifiedEmail());
        //I can unsubscribe
        $this->actingAs($user)->get("/unsubscribe/{$user->subscription_token}");

        //$response = $this->get("/unsubscribe/{$user->subscription_token}");

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
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
            'password' => 'myPassword',
            'password_confirmation' => 'myPassword',
        ];
        $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
        ]);

        // Get the other users id
        $not_my_user_id = DB::table('users')->where('first_name', 'Jane')->value('user_id');
        

        Auth::logout(); 

        //Ensure we are a guest
        $this->assertGuest();
        //Can't get a user if you aren't logged in
        $response = $this->get("/api/users/{$not_my_user_id}");
        $response->assertJson([
            "message" => "USER_NOT_FOUND"
        ]);

        //Can't update user if you aren't logged in
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];
        //Can't delete users if you aren't logged in
        $response = $this->put("/api/users/{$not_my_user_id}/", $updates);
        $response->assertJson([
            "message" => "UNAUTHORIZED"
        ]);
        


        $response = $this->delete("/api/users/{$not_my_user_id}/");
        $response->assertJson([
            "message" => "UNAUTHORIZED"
        ]);


    }
    public function test_user_unauthorized(): void
    {
        //Register someone else as a user. (we do not have access to this person's account)
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
            'password' => 'myPassword',
            'password_confirmation' => 'myPassword',
        ];
        $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
        ]);

        // Get the other users id
        $not_my_user_id = DB::table('users')->where('first_name', 'Jane')->value('user_id');


        //Register myself as a user.
        $my_data = [
            'first_name' => 'Jay',
            'last_name' => 'Spencer',
            'email' => 'jayspencer@example.com',
            'password' => 'myPassword',
            'password_confirmation' => 'myPassword',
        ];
        
        Auth::logout(); 
        $this->post('/register', $my_data);


        $this->assertDatabaseHas('users', [
            'first_name' => 'Jay',
            'last_name' => 'Spencer',
            'email' => 'jayspencer@example.com',
        ]);
        $myself = User::where('first_name', 'Jay')->first();
        //Can't get someone that is not yourself
        $response = $this->actingAs($myself)->get("/api/users/{$not_my_user_id}");
        $response->assertJson([
            "message" => "USER_NOT_FOUND"
        ]);

        //Can't update someone that's not yourself
        $updates = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe II',
            'email' => 'johnnydoe@example.com',
        ];

        $response = $this->actingAs($myself)->put("/api/users/{$not_my_user_id}/", $updates);
        $response->assertJson([
            "message" => "UNAUTHORIZED"
        ]);
        


        $response = $this->actingAs($myself)->delete("/api/users/{$not_my_user_id}/");
        $response->assertJson([
            "message" => "UNAUTHORIZED"
        ]);
        //$myself = $this->getJson("/api/users/{$user_id}")->json()["data"][0];
    }
    public function test_user_invalid_data() : void {
        $data = [
            // 'first_name' => 'Jane',
            // 'last_name' => 'Smith',
            // 'email' => 'janesmith@example.com',
            // 'password' => 'myPassword',
            // 'password_confirmation' => 'myPassword',
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

        $response = $this->post('/register', $data);
        Auth::logout();
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

        $user_id = DB::table('users')->where('first_name', 'Jay')->value('user_id');
        $user = User::find($user_id);

        $response = $this->actingAs($user)->put("/api/users/{$user_id}/", $updates);

        $response->assertSessionHasErrors([
            "first_name" => "The first name field must be at least 1 characters.",
            "last_name" => "The last name field must be at least 1 characters.",
            "email" => "The email field must be at least 1 characters.",
        ]);
    
    }
}
