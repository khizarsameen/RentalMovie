<?php

namespace App\Http\Controllers;

use App\CustomMethods\LoggedUserFacade;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    


    public function home(){
        return view('home');

    }
}
