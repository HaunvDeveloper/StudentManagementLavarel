<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Thêm import này nếu chưa có

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Sử dụng Auth facade thay vì helper
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }
        return redirect('/'); // Hoặc trang lỗi
    }
}
