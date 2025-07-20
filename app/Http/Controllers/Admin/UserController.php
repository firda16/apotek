<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Validation\ValidatesRequests;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ValidatesRequests;
    // halaman users
    public function index(Request $request)
    {
        
        $users = User::get();
        return view('admin.users.index', compact(
             'users'
        ));        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        $users = User::get();      
        return view('admin.users.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:100',
            'email'=>'required|email',
            'role'=>'required',
            'password'=>'required|confirmed|max:200',
            'avatar'=>'nullable|file|image|mimes:jpg,jpeg,gif,png',
        ]);
        $imageName = null;
        if ($request->hasFile('avatar')) {
            $imageName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('storage/users'), $imageName);
        }
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'avatar' => $imageName,
            'password' => Hash::make($request->password),
        ]);        
        $notifiation = notify('user created successfully');
        return redirect()->route('users.index')->with($notifiation);
    }

   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
    return view('admin.users.edit', compact('user'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'=>'required|max:100',
            'email'=>'required|email',
            'role'=>'required',
            'password'=>'nullable|confirmed|max:200',
            'avatar'=>'nullable|file|image|mimes:jpg,jpeg,gif,png',
        ]);
        $imageName = $user->avatar;
        $password = $user->password;
        if($request->hasFile('avatar')){
            $imageName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('storage/users'), $imageName);
        }
        if(!empty($request->password) && ($user->password != $request->password)){
            $password = Hash::make($request->password);
        }
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'avatar' => $imageName,
            'password' => $password,
        ]);                
        $notification = notify('user update berhasil ');
        return redirect()->route('users.index')->with($notification);
    }

    public function profile(){
        // $title = 'user profile';
        $users = Auth::user();
        $roles = User::getRoleOptions(); 
        return view('admin.users.profile',compact(
            'users','roles'
        ));
    }

    public function updateProfile(Request $request,User $user){
        $this->validate($request,[
            'name' => 'required|min:5|max:200',
            'email' => 'required|email',
            'username' => 'nullable|min:3|max:200',
            'avatar' => 'nullable|file|image|mimes:jpg,jpeg,png,gif'
        ]);
        $imageName = $user->avatar;
        if($request->hasFile('avatar')){
            $imageName = time().'.'.$request->avatar->extension();
            $request->avatar->move(public_path('storage/users'), $imageName);
        }
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'avatar' => $imageName,
        ]);
        $notification = notify('profil berhasil diupdate');
        return redirect()->route('profile')->with($notification);
    }

    /**
     * Update current user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, User $user)
    {
        $this->validate($request, [
            'current_password'=>'required',
            'password'=>'required|max:200|confirmed',
        ]);
        $verify_password = password_verify($request->current_password, $user->password);
        if ($verify_password) {
            $user->update(['password'=>Hash::make($request->password)]);
            $notification = notify('User password updated successfully!!!');
            $logout = Auth::logout();
            return back()->with($notification, $logout);
        } elseif(!$verify_password) {
            $notification = notify("Incorrect Old Password!!!",'danger');
            return back()->with($notification);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
{
    User::findOrFail($id)->delete();
    return back()->with('success', 'User deleted successfully');
}

}
