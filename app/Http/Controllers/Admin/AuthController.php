<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|string|min:6',
        ],
        [
            'username.required' => 'Tên đăng nhập không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);
        
        if (Auth::attempt($request->only('username', 'password'))) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Đăng nhập thành công!');
        }
        
        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'username' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
            ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Đăng xuất thành công!');
    }


}
