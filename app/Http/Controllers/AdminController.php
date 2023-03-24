<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request) {
        $validation = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $admin = Admin::query()
                ->where('password', $validation['password'])
                ->where('password', $validation['username'])
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->withInput($validation)
             ->with('error', 'Username or password is not valid');
        }


        $admin->update([
            'last_login_timestamp' => Date::now()->format('Y-m-d H:i:s')
        ]);
        Auth::guard('admin')->loginUsingId($admin->id, true);
        return redirect()->route('admin')->with('message', 'You logged in successfully');
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin');
    }
}
