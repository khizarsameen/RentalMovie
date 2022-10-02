<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\CustomMethods\LoggedUser;

class LoggedUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('loggeduser',function(){
            return new LoggedUser();
        });
    }
}
