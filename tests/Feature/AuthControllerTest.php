<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123#',
            'password_confirmation' => 'Password123#'
        ];
    }

    #[Test]
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/register', $this->userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'message'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $this->userData['email']
        ]);
    }

    #[Test]
    public function registration_requires_password_confirmation()
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'password' => $this->userData['password'],
            // Falta password_confirmation
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function registration_requires_valid_email()
    {
        $response = $this->postJson('/api/register', [
            ...$this->userData,
            'email' => 'not-an-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt($this->userData['password'])
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $this->userData['password']
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token'
            ]);
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        $response->assertStatus(200);

        $this->assertCount(0, $user->tokens);
    }

    #[Test]
    public function logout_requires_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
