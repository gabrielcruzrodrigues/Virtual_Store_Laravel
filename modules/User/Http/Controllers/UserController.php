<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Contracts\UserServiceContract;
use Modules\User\Exceptions\UserInventoryException;
use Modules\User\Http\Requests\LoginFormRequest;
use Modules\User\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
     public function __construct(
          private readonly UserServiceContract $userService
     ) {}

     public function store(UserFormRequest $request): JsonResponse
     {
          $data = $request->validated();

          $this->userService->create($data);

          return response()->json([
               'success' => true,
               'message' => "The user created with success"
          ], 201);
     }

     public function login(LoginFormRequest $request): JsonResponse
     {
          $data = $request->validated();

          if (!Auth::attempt($data)) {
               throw UserInventoryException::unauthorized();
          }

          $user = Auth::user();

          $token = $this->userService->login($user);

          return response()->json([
               'success' => true,
               'token' => $token
          ]);
     }

     public function getAllUsers(Request $request)
     {

          if (!Auth::check()) {
               throw UserInventoryException::unauthorized();
          }

          $users = $this->userService->getAll();

          return response()->json([
               'sucess' => true,
               'users' => $users
          ]);
     }
}
