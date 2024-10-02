<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\User\Contracts\UserServiceContract;
use Modules\User\Exceptions\UserInventoryException;
use Modules\User\Http\Requests\LoginFormRequest;
use Modules\User\Http\Requests\UserFormRequest;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
     public function create(UserFormRequest $request) : void
     {
          DB::beginTransaction();

          try 
          {
               $data = $request->all();
               $data['password'] = Hash::make($request->input('password'));

               User::create($data);
               DB::commit();
          }
          catch(Exception $ex)
          {
               DB::rollBack();
               Log::error("Un error ocurred when tryning create user! - ex: {$ex->getMessage()}");
               throw UserInventoryException::createError();

          }
     }

     public function login(LoginFormRequest $request) : string
     {    
          $credentials = $request->only(['email', 'password']);
          if (!Auth::attempt($credentials))
          {
               throw UserInventoryException::unauthorized();
          }
          return $request->user()->createToken('api-token')->plainTextToken;
     }

     public function getAll() : LengthAwarePaginator 
     {
          return User::orderBy('id', 'ASC')->paginate(10);
     }

     public function update(UserFormRequest $request) : void
     {

     }

     public function delete(User $user) : void
     {

     }
}