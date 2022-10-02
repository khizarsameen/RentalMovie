<?php
namespace App\CustomMethods;
use Illuminate\Support\Facades\Facade;
class LoggedUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'loggeduser';
    }
}