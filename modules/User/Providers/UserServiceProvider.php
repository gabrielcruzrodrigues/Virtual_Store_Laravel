<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Contracts\UserServiceContract;
use Modules\User\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
     public function boot(): void
     {
          $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
          $this->loadRoutesFrom(__DIR__ . '/../Routes/Web.php');

          $this->app->bind(UserServiceContract::class, UserService::class);
     }
}