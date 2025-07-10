<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LoginController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $title = 'login';
        return view('admin.auth.login', compact('title'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $authenticated = Auth::attempt($request->only('email', 'password'));

        if (!$authenticated) {
            return back()->with('login_error', "Invalid user credentials");
        }

        return redirect()->route('dashboard');
    }
}
