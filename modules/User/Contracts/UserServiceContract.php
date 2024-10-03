<?php 

namespace Modules\User\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\User\Models\User;

interface UserServiceContract 
{
     public function create(array $request) : void;
     public function login(User $request) : string;
     public function getAll() : LengthAwarePaginator;
}