<?php

namespace Modules\User\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Modules\User\Exceptions\UserInventoryException;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }


    public function test_it_creates_a_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password@123'
        ];

        $this->userService->create($data);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertTrue(Hash::check('Password@123', $user->password));
    }


    public function test_it_throws_exception_on_user_creation_error()
    {
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->once();

        $this->expectException(UserInventoryException::class);

        $data = [
            'name' => null,
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $this->userService->create($data);
    }


    public function test_it_generates_token_on_login()
    {
        $user = User::factory()->create();
   
        $token = $this->userService->login($user);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }


    public function test_it_retrieves_paginated_users()
    {
        User::factory()->count(15)->create();

        $paginatedUsers = $this->userService->getAll();

        $this->assertCount(10, $paginatedUsers->items());

        $this->assertEquals(2, $paginatedUsers->lastPage());
    }
    public function test_it_fails_login_with_invalid_credentials()
{
    $user = User::factory()->create([
        'password' => Hash::make('Password@123')
    ]);

    $this->assertFalse(Auth::attempt(['email' => $user->email, 'password' => 'wrongpassword']));
}
}
