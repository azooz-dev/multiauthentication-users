<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.login');
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
                'email' => 'The provided credentials do not match our records.',
                'password' => 'The provided credentials do not match our records.',
            ]);
        }

        return redirect()->route('backend.admin.dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
