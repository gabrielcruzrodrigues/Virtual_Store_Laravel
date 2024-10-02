<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Contracts\UserServiceContract;
use Modules\User\Http\Requests\LoginFormRequest;
use Modules\User\Http\Requests\UserFormRequest;
use Modules\User\Models\User;

class UserController extends Controller
{
     public function __construct(
          private readonly UserServiceContract $userService
     ){ }

     public function index() : JsonResponse
     {
          $users = $this->userService->getAll();
          return response()->json([
               'success' => true,
               'data' => $users
          ]);
     }

     public function store(UserFormRequest $request) : JsonResponse
     {
          $this->userService->create($request);

          return response()->json([
               'success' => true,
               'message' => "The user created with success"
          ], 201);
     }

     public function login(LoginFormRequest $request) : JsonResponse
     {
          $token = $this->userService->login($request);
          
          return response()->json([
               'success' => true,
               'token' => $token
          ]);
     }
}