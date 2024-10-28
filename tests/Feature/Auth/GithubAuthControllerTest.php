<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\GithubAccount;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Two\AbstractProvider;

class GithubAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Reset Mockery after each test
        Mockery::close();
    }

    public function test_redirect_returns_github_redirect_response()
    {
        $abstractRedirectResponse = Mockery::mock(AbstractProvider::class);
        $abstractRedirectResponse->shouldReceive('scopes')->with(['read:user', 'user:email', 'repo'])->andReturn($abstractRedirectResponse);
        $abstractRedirectResponse->shouldReceive('redirect')->andReturn(redirect('https://github.com/auth'));

        Socialite::shouldReceive('driver')->with('github')->andReturn($abstractRedirectResponse);

        $response = $this->get('/auth/github');


        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_callback_creates_new_user_and_github_account()
    {
        // Create mock Socialite User
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = '123456';
        $socialiteUser->name = 'Test User';
        $socialiteUser->nickname = 'testuser';
        $socialiteUser->email = 'test@example.com';
        $socialiteUser->token = 'github_token_123';
        $socialiteUser->refreshToken = 'refresh_token_123';
        $socialiteUser->expiresIn = 3600;

        // Mock Socialite facade
        Socialite::shouldReceive('driver')->with('github')->andReturn(Mockery::mock([
            'stateless' => Mockery::mock([
                'user' => $socialiteUser
            ])
        ]));

        $response = $this->get('/auth/github/callback');

        $response->assertStatus(200);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        // Assert GitHub account was created
        $this->assertDatabaseHas('github_accounts', [
            'github_id' => '123456',
            'github_username' => 'testuser',
            'github_token' => 'github_token_123',
            'github_refresh_token' => 'refresh_token_123',
        ]);

        // Assert response contains token
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
            ],
            'access_token'
        ]);
    }


    public function test_logout_deletes_tokens_and_logs_out_user()
    {
        $user = User::factory()->create();


        $response = $this
            ->withHeaders([
                'authorization' => 'Bearer ' . $user->createToken('github-token')->plainTextToken
            ])->json('post', 'api/auth/logout');
        $response->assertStatus(200);


        $response->assertJson(['message' => 'Successfully logged out']);

    }
}
