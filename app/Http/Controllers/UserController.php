<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Hiển thị trang đăng nhập.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        
        return view('user.login');
    }

    /**
     * Xử lý đăng nhập.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function handleLogin(Request $request)
    {
        $credentials = $request->only('Username', 'Password');

        // Tìm người dùng theo Username
        $user = User::where('Username', $credentials['Username'])->with('authentication')->first();

        // Kiểm tra người dùng và mật khẩu
        if (!$user || hash('sha256', $credentials['Password']) !== $user->Password) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!'
            ], 401);
        }

        // Đăng nhập người dùng
        Auth::login($user, true);

        session(['Id' => $user->Id]);
        session(['Username' => $user->Username]);
        session(['FullName' => $user->FullName]);
        session(['Email' => $user->Email]);
        session(['DayOfBirth' => $user->DayOfBirth]);
        session(['AuthId' => $user->AuthId]);
        session(['AuthCode' => $user->authentication->Code ?? null]);
        session(['AuthName' => $user->authentication->Name ?? null]);
        return response()->json([
            'success' => true,
            'redirect_url' => route($user->authentication->Code . '.dashboard') // URL sau khi đăng nhập thành công
        ]);
    }



    /**
     * Xử lý đăng xuất.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Session::flush(); // Xóa session
        Auth::logout(); // Đăng xuất user
        return redirect()->route('login');
    }


    public function profile()
    {
        return view('student.profile');
    }

}
