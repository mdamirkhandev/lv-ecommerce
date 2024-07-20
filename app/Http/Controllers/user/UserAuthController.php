<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function index()
    {
        return view('user.login');
    }
    public function loginProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            // if (session()->has('url.intended')) {
            //     return redirect(session()->get('url.intended'));
            // }
            return redirect()->route('user.index');
        }
        flash('Either Email / Password Invalid !!', 'error');
        return back()->withInput($request->only('email'));
    }
}
