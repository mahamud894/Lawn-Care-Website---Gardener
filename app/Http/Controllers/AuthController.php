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
        // dd($request->name);
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['password'] = $request->password;

        User::create($data);

        // dd("Signup success!");
        return redirect()->route('login');
    }

    public function loginCheck(Request $request){
        // dd($request->all());
        if(Auth::attempt(["email"=>$request->email, "password"=>$request->password])){
            if(Auth::user()->isAdmin == 0){
                Alert::success('Success', 'User Login Successfull');

                return redirect()->route('home');
            }else{
                Alert::success('Success', 'Admin Login Successfull');
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
