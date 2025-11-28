<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index(){
        $title = 'register';
        return view('admin.auth.register',compact('title'));
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|max:100',
            'email'=>'required|email',
            'password'=>'required|max:200|confirmed',
        ]);
        
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $user->assignRole('sales-person');
        Auth::attempt($request->only('email','password'));
        return redirect()->route('dashboard');
    }
}
