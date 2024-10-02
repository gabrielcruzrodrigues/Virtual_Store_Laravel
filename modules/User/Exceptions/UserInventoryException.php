<?php

namespace Modules\User\Exceptions;

use Exception;

class UserInventoryException extends Exception
{
     public static function createError() : self
     {
          $message = "Un erro occurred when tryning create a new user";
          return new self(
               message: $message,
               code: 400
          );
     }

     public static function unauthorized() : self
     {
          $message = "user is not authorized";
          return new self (
               message: $message,
               code: 401
          );
     }
}