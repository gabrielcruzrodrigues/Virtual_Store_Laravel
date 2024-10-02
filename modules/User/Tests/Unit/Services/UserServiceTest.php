<?php

namespace Modules\User\Tests\Unit\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Modules\User\Exceptions\UserInventoryException;
use Modules\User\Http\Requests\LoginFormRequest;
use Modules\User\Http\Requests\UserFormRequest;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Tests\TestCase;

# php artisan test --filter=UserServiceTest
class UserServiceTest extends TestCase
{
     protected $userDatabase;

     protected function setUp(): void
     {
          parent::setUp();

          $this->userDatabase = [
               (object)['id' => 1, 'name' => 'Gabriel', 'email' => 'email@gmail.com', 'password' => '12345678'],
               (object)['id' => 2, 'name' => 'Pedro', 'email' => 'email2@gmail.com', 'password' => '12345678']
          ];
     }

     public function test_getAll()
     {
          $userMock = Mockery::mock('alias:' . User::class);

          $userMock->shouldReceive('orderby')
               ->with('id', 'ASC')
               ->andReturnSelf();

          $userMock->shouldReceive('paginate')
          ->with(10)
          ->andReturn(new LengthAwarePaginator(
               collect($this->userDatabase), // Coleção dos dados simulados
               count($this->userDatabase),   // Total de itens
               10                            // Itens por página
          ));

          $userService = new UserService();
          $result = $userService->getAll();

          $this->assertInstanceOf(LengthAwarePaginator::class, $result);
          $this->assertCount(2, $result);
          $this->assertEquals('Gabriel', $result[0]->name);
     }

     public function test_create_success()
     {
          // Mock de DB
          DB::shouldReceive('beginTransaction')->once();
          DB::shouldReceive('commit')->once();
          DB::shouldReceive('rollBack')->never();

          // Mock de Hash
          Hash::shouldReceive('make')
               ->with('12345678')
               ->andReturn('hashed_password');

          // Mock de User -> ORM
          $userMock = Mockery::mock('alias:' . User::class);
          $userMock->shouldReceive('create')
               ->with(Mockery::subset([
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => 'hashed_password'
               ]))
               ->andReturn((object)['id' => 3, 'name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'hashed_password']);

          // Mock do Request
          $requestMock = Mockery::mock(UserFormRequest::class);
          $requestMock->shouldReceive('all')
               ->andReturn(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => '12345678']);
          $requestMock->shouldReceive('input')
               ->with('password')
               ->andReturn('12345678');

    
          $userService = new UserService();
          /** @var UserFormRequest $requestMock */
          $userService->create($requestMock);

          $this->assertTrue(true);
     }

     public function test_create_createError()
     {
          // Mock de DB
          DB::shouldReceive('beginTransaction')->once();
          DB::shouldReceive('commit')->never();
          DB::shouldReceive('rollBack')->once();

          // Mock de Hash
          Hash::shouldReceive('make')
               ->with('12345678')
               ->andReturn('hashed_password');

          // Mock de User -> ORM
          $userMock = Mockery::mock('alias:' . User::class);
          $userMock->shouldReceive('create')
               ->andThrow(UserInventoryException::createError());

          // Mock do Request
          $requestMock = Mockery::mock(UserFormRequest::class);
          $requestMock->shouldReceive('all')
               ->andReturn(['email' => 'john@example.com', 'password' => '12345678']);
          $requestMock->shouldReceive('input')
               ->with('password')
               ->andReturn('12345678');

          $this->expectException(UserInventoryException::class);
          $this->expectExceptionMessage("Un erro occurred when tryning create a new user");
          $this->expectExceptionCode(400);

          $userService = new UserService();
          /** @var UserFormRequest $requestMock */
          $userService->create($requestMock);
     }

     public function test_login_success()
     {
          // Mock do request
          $requestMock = Mockery::mock(LoginFormRequest::class);
          $requestMock->shouldReceive('only')
               ->with(['email', 'password'])
               ->andReturn(['email' => "teste@gmail.com", 'password' => '12345678']);

          // Mock do User retornando o token de acesso
          $userMock = Mockery::mock('alias:' . User::class);
          $userMock->shouldReceive('createToken')
               ->with('api-token')
               ->andReturn((object)['plainTextToken' => 'dummy_token']);

          // Mock do user() para acessar o createToken
          $requestMock->shouldReceive('user')->andReturn($userMock);

          // Mock do Auth no contexto do usuário com credenciais corretas
          Auth::shouldReceive('attempt')
               ->with(['email' => 'teste@gmail.com', 'password' => '12345678'])
               ->andReturn(true);

          $userService = new UserService();
          /** @var LoginFormRequest $requestMock */
          $token = $userService->login($requestMock);
          
          $this->assertEquals('dummy_token', $token);
          $this->assertIsString($token);
     }

     public function test_login_badCredentials()
     {
          // Mock do request
          $requestMock = Mockery::mock(LoginFormRequest::class);
          $requestMock->shouldReceive('only')
               ->with(['email', 'password'])
               ->andReturn(['email' => "teste@gmail.com", 'password' => '12345678']);

          // Mock do Auth no contexto do usuário com credenciais corretas
          Auth::shouldReceive('attempt')
               ->with(['email' => 'teste@gmail.com', 'password' => '12345678'])
               ->andThrow(UserInventoryException::unauthorized());

          $this->expectException(UserInventoryException::class);
          $this->expectExceptionMessage("user is not authorized");
          $this->expectExceptionCode(401);

          $userService = new UserService();
          /** @var LoginFormRequest $requestMock  */
          $userService->login($requestMock);
     }
}