<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_before_viewing_or_adding_users(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/users')->assertRedirect(route('login'));
        $this->postForm('/users', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
        ])->assertRedirect(route('login'));
    }

    public function test_users_page_displays_the_user_and_relationships(): void
    {
        $this->seed();
        $this->registerAndLogin();

        $this->get('/')
            ->assertOk()
            ->assertSee('Add a user')
            ->assertSee('Create user')
            ->assertSee('Genses Robinson')
            ->assertSee('I enjoy writing about technology and design.')
            ->assertSee('Getting started with Laravel relationships')
            ->assertSee('Editor');

        $this->get('/users')->assertOk()->assertSee('Genses Robinson');
    }

    public function test_a_user_can_be_created_and_is_shown_in_the_list(): void
    {
        $this->registerAndLogin();

        $this->postForm('/users', [
            'name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ]);

        $this->get('/users')
            ->assertOk()
            ->assertSee('User created successfully.')
            ->assertSee('Morgan Chen')
            ->assertSee('morgan@example.com');
    }

    public function test_user_creation_validates_required_and_unique_fields(): void
    {
        $this->seed();
        $this->registerAndLogin();

        $this->from('/users')->postForm('/users', [
            'name' => '',
            'email' => 'Genses@example.com',
        ])->assertRedirect('/users')
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_duplicate_submissions_do_not_create_duplicate_users(): void
    {
        $this->registerAndLogin();

        $data = [
            'name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ];

        $this->postForm('/users', $data)->assertRedirect(route('users.index'));
        $this->from('/users')->postForm('/users', $data)
            ->assertRedirect('/users')
            ->assertSessionHasErrors('email');

        $this->get('/users')->assertOk();
        $this->assertDatabaseCount('users', 2);
    }

    public function test_registration_stores_a_hashed_password_and_authenticates_the_user(): void
    {
        $this->postForm('/register', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertRedirect(route('users.index'));

        $this->postForm('/register', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'jamie@example.com')->firstOrFail();

        $this->assertNotSame('SecurePassword123!', $user->password);
        $this->assertTrue(Hash::check('SecurePassword123!', $user->password));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_requires_at_least_seven_password_characters(): void
    {
        $this->from('/register')->postForm('/register', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
            'password' => 'Abc12!',
            'password_confirmation' => 'Abc12!',
        ])->assertRedirect('/register')
            ->assertSessionHasErrors('password');

        $this->postForm('/register', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
            'password' => 'Abc123!',
            'password_confirmation' => 'Abc123!',
        ])->assertRedirect(route('users.index'));
    }

    public function test_authenticated_users_are_redirected_away_from_login_and_registration(): void
    {
        $this->registerAndLogin();

        $this->get('/login')->assertRedirect(route('home'));
        $this->get('/register')->assertRedirect(route('home'));
    }

    public function test_three_failed_login_attempts_lock_the_account_temporarily(): void
    {
        $this->registerAndLogin();
        $this->postForm('/logout', [])->assertRedirect(route('login'));

        $credentials = [
            'email' => 'jamie@example.com',
            'password' => 'WrongPassword123!',
        ];

        foreach (range(1, 3) as $attempt) {
            $this->from('/login')->postForm('/login', $credentials)
                ->assertRedirect('/login')
                ->assertSessionHasErrors('email');
        }

        $this->from('/login')->postForm('/login', [
            'email' => 'jamie@example.com',
            'password' => 'SecurePassword123!',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    private function registerAndLogin(): void
    {
        $this->postForm('/register', [
            'name' => 'Jamie Lee',
            'email' => 'jamie@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertRedirect(route('users.index'));
    }

    /**
     * Include the same token in the request and its session for CSRF validation.
     *
     * @param  array<string, string>  $data
     */
    private function postForm(string $uri, array $data): TestResponse
    {
        $token = str_repeat('a', 40);
        $this->withSession(['_token' => $token]);

        return $this->post($uri, array_merge($data, ['_token' => $token]));
    }
}
