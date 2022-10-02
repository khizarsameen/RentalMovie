<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\Input;

class LoginController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);


        $user = User::where('email', $request->email)->first();
        if(!$user){
            return redirect('login')
            ->withErrors(['email' => 'Email not found in our records'])
            ->onlyInput('email');
        }

        if(Hash::check($request->password, $user->password)){
            $request->session()->put('loggedUser', $user->id);
            return redirect()->route('home');

        } else{
            return redirect('login')
            // ->withInput($request->except('password'))
            ->withErrors(['password' => 'Invalid password'])
            ->onlyInput('email');

        }
        


        return redirect('login')->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout() {
        if(session()->has('loggedUser')){
            session()->pull('loggedUser');
            return redirect()->route('login');
        }
    }
}
