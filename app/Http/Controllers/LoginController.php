<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create() {
        return view('auth.login');
    }

    public function authenticate(Request $request){
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

        $result = Auth::attempt($credentials);
        if ($result) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'name' => 'Данного пользователя не существует или введеные неверные данные ',
        ])->onlyInput('name');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
