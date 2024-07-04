<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Mail\adminForgetPassword;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        return view('backend.admin.auth.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.auth.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {

            return redirect()->route('admin.login')->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }

    public function forget_password() {
        return view('backend.admin.auth.forget_password');
    }

    public function forget_password_submit(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return redirect()->back()->withErrors([
                'email' => 'These credentials do not match our records.'
            ]);
        }

        $admin->remember_token = Hash::make(rand(99, 100));

        $admin->save();

        Mail::to($admin->email)->send(new adminForgetPassword('Admin Forget Password', $admin->remember_token, $admin->email));

        return redirect()->back()->with(['message' => 'We have emailed your password reset link.']);
    }

    public function reset_password($token, $email) {
        return view('backend.admin.auth.reset_password', ['token' => $token, 'email' => $email]);
    }

    public function reset_password_submit(Request $request) {
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin->remember_token) {
            return redirect()->back()->withErrors([
                'email' => 'The password reset link is invalid or has expired.'
            ]);
        }

        $admin->password = Hash::make($request->password);

        $admin->remember_token = "";
        $admin->save();

        return redirect()->route('admin.login');
    }
}
