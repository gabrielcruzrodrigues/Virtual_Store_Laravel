<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Modules\User\Exceptions\UserInventoryException;
use Throwable;

class Handler extends ExceptionHandler
{
     protected $dontReport = [
          UserInventoryException::class,
     ];

     public function report(Throwable $exception)
     {
          parent::report($exception);
     }

     public function render($request, Throwable $exception)
     {
          if ($exception instanceof UserInventoryException) {
               return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
               ], $exception->getCode() ?: 400);
          }

          return parent::render($request, $exception);
     }
}