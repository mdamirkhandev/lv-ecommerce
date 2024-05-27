<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function loginProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password
                ],
                $request->get('remember')
            )) {
                $admin = Auth::guard('admin')->user();
                if ($admin->role == 2) {
                    return redirect()->route('admin.home');
                } else {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')
                        ->with('error', 'You are not allowed to access to admin panel  !!')
                        ->withInput($request->only('email'));
                }
            } else {
                return redirect()->route('admin.login')
                    ->with('error', 'Either Email / Password Invalid !!')
                    ->withInput($request->only('email'));
            }
        } else {
            return redirect()
                ->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }
}
