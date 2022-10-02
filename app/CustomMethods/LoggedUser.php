<?php
namespace App\CustomMethods;

use App\Models\User;

class LoggedUser
{
    public function user()
    {
        $id = session()->get('loggedUser');
        $user = User::find($id);
        return $user;
    }
}