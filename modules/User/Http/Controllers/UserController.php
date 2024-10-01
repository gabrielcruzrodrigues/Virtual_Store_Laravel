<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\Models\User;

class UserController extends Controller
{
     public function index()
     {
          $users = User::all();
          return response()->json(['teste' => $users]);
     }
}