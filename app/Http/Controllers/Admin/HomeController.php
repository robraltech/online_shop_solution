<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        // $admin=Auth::guard('admin')->user();
        // echo "welcome to admin dashboard".$admin->name.'<a href="'.route('admin.logout').'">Logout</a>';
        return view('admin.dashboard');
    }
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
}
}
