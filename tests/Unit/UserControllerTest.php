<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_login_form()
    {
        // Act
        $response = $this->get(route('login'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('instructions');
    }

/** @test */
    public function it_logs_in_the_user_with_valid_credentials()
    {
        // Arrange: Create a user with a known password
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Start the session explicitly before sending the request
        session()->start();

        // Act: Send a POST request to the login route with valid credentials
        $response = $this->post(route('login'), [
            'username' => 'nimda',
            'password' => 'catz',
        ]);

        // Assert: Check that the user is redirected
        $response->assertRedirect('/');

        // Assert: Check if the session contains the success message
        $response->assertSessionHas('success', 'Welcome back!');

        // Assert: Check if the user is authenticated
        $this->assertAuthenticatedAs($user);
    }


/** @test */
    public function it_fails_to_log_in_with_invalid_credentials()
    {
        // Act: Send a POST request to the login route with invalid credentials
        $response = $this->post(route('login'), [
            'username' => 'nonexistent',
            'password' => 'invalid',
        ]);

        // Assert: Check that the user is redirected back to the login page
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest(); // Ensure the user is not authenticated
    }


    /** @test */
    public function it_logs_out_the_user()
    {
        // Arrange: Create and authenticate a user
        $user = User::factory()->create();

        // Act: Log in the user
        $this->actingAs($user);

        // Act: Send a POST request to the logout route
        $response = $this->post(route('logout'));

        // Assert: Check that the user is logged out
        $this->assertGuest(); // Ensure the user is logged out
        $response->assertRedirect('/');
    }
}
