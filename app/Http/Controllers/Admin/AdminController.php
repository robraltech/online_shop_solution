<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

                $admin=Auth::guard('admin')->user();
                if($admin->role==2){
                    return redirect()->route('admin.dashboard');
                }
                else{
                    $admin=Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorize for admin panel');
                }
            } else {
                return redirect()->route('admin.login')->with('error', 'Invalid email or password')->withInput($request->only('email'));
            }
        } else {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

  
}
