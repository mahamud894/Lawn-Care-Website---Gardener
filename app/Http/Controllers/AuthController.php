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
    $data['phone'] = $request->phone; // 👈 এই লাইনটি ডাটা রিসিভ করছে
    $data['password'] = bcrypt($request->password); // পাসওয়ার্ড সিকিউর হ্যাশ করা হলো

    // ড্রপডাউন থেকে রোল নেওয়া হচ্ছে (০ অথবা ১)
    $data['isAdmin'] = $request->role;

    // সাধারণ ইউজার হলে সরাসরি একটিভ (১), আর অ্যাডমিন হলে পেন্ডিং (০)
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
    // প্রথমে ইমেইল দিয়ে ইউজার ডাটা চেক করা হচ্ছে
    $user = User::where('email', $request->email)->first();

    // এখানে ব্লক চেক লজিকটি বসানো হলো
    if($user && $user->isBlocked == 1) {
        Alert::error('Account Blocked', 'Your account has been suspended by Admin.');
        return redirect()->back();
    }

    if($user) {
        // যদি ইউজারটি অ্যাডমিন রোল নিয়ে সাইনআপ করে থাকে এবং এখনো মেইন অ্যাডমিন অ্যাপ্রুভ না করে থাকে (০ থাকে)
        if($user->isAdmin == 1 && $user->isAdminApproved == 0) {
            Alert::error('Access Denied', 'Your Admin account is pending approval from Main Admin.');
            return redirect()->back();
        }
    }

    // সব ঠিক থাকলে সাধারণ লগইন প্রসেস ট্রাই করবে
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
