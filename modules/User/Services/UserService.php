<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\User\Contracts\UserServiceContract;
use Modules\User\Exceptions\UserInventoryException;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
     public function create(array $request): void
     {
          DB::beginTransaction();

          try {
               $request['password'] = Hash::make($request['password']);

               User::create($request);

               DB::commit();
          } catch (Exception $ex) {
               DB::rollBack();
               Log::error("Un error ocurred when tryning create user! - ex: {$ex->getMessage()}");
               throw UserInventoryException::createError();
          }
     }


     public function login(User $request): string
     {
          return $request->createToken('api-token')->plainTextToken;
     }

     public function getAll(): LengthAwarePaginator
     {
          return User::orderBy('id', 'ASC')->paginate(10);
     }
}
