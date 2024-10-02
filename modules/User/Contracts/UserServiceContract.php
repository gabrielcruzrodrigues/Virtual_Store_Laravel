<?php 

namespace Modules\User\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\User\Http\Requests\LoginFormRequest;
use Modules\User\Http\Requests\UserFormRequest;
use Modules\User\Models\User;

interface UserServiceContract 
{
     public function create(UserFormRequest $request) : void;
     public function login(LoginFormRequest $request) : string;
     public function getAll() : LengthAwarePaginator;
     public function update(UserFormRequest $request) : void;
     public function delete(User $user) : void;
}