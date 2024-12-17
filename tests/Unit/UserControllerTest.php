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
        // Arrange
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Act
        $response = $this->post(route('login'), ['username' => $user->username, 'password' => 'password']);

        // Assert
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Welcome back!');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_fails_to_log_in_with_invalid_credentials()
    {
        // Act
        $response = $this->post(route('login'), ['username' => 'nonexistent', 'password' => 'invalid']);

        // Assert
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function it_logs_out_the_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $this->actingAs($user)->post(route('logout'));

        // Assert
        $this->assertGuest();
        $response = $this->post(route('logout'));
        $response->assertRedirect('/');
    }
}
