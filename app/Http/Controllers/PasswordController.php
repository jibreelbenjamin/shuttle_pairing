<?php

namespace App\Http\Controllers;

use App\Models\AppPassword;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function showForm()
    {
        return view('password.form');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $appPassword = AppPassword::first();

        if ($appPassword && $request->password === $appPassword->password) {
            session(['app_authenticated' => true]);
            return redirect()->route('tournament.index');
        }

        return back()->withErrors(['password' => 'Password salah!']);
    }

    public function logout()
    {
        session()->forget('app_authenticated');
        return redirect()->route('password.form');
    }
}
