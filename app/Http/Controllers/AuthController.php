<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


class AuthController extends Controller
{
    public function viewSignup () {
        return view('pages.signup');
    }

    public function signup(Request $request){
    $data['name'] = $request->name;
    $data['email'] = $request->email;
    $data['phone'] = $request->phone;
    $data['password'] = bcrypt($request->password); // password sequer in hash

    // from drop collecting role(0 or 1)
    $data['isAdmin'] = $request->role;

    // for normal user direct (1) or if admin pending (0)
    $data['isAdminApproved'] = $request->role == 1 ? 0 : 1;

    User::create($data);

    if($request->role == 1) {
        Alert::info('Requested', 'Admin account created! Wait for Main Admin approval.');
    } else {
        Alert::success('Success', 'Signup successful! Please login.');
    }

    return redirect()->route('login');
}

public function loginCheck(Request $request){
    // first checking user data with email
    $user = User::where('email', $request->email)->first();

    // block chacking logic (if admin block user)
    if($user && $user->isBlocked == 1) {
        Alert::error('Account Blocked', 'Your account has been suspended by Admin.');
        return redirect()->back();
    }

    if($user) {
        // if user sign up with admin role and if main admin not approve that (0)
        if($user->isAdmin == 1 && $user->isAdminApproved == 0) {
            Alert::error('Access Denied', 'Your Admin account is pending approval from Main Admin.');
            return redirect()->back();
        }
    }

    // if every thing ok try normal login process
    if(Auth::attempt(["email"=>$request->email, "password"=>$request->password])){
        if(Auth::user()->isAdmin == 0){
            Alert::success('Success', 'User Login Successful');
            return redirect()->route('home');
        }else{
            Alert::success('Success', 'Admin Login Successful');
            return redirect()->route('viewAdminPanel');
        }
    }else{
        Alert::error('Error', 'Invalid Credentials!');
        return redirect()->back();
    }
}

    public function logout(){
        Auth::logout();

        Alert::success('Success', 'Logout Successfull');

        return redirect()->route('login');
    }
}
