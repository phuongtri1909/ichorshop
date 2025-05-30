<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSecretKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = config('app.api_secret_key');
        
        if (!$secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Secret Key không được cấu hình'
            ], 500);
        }
        
        if ($request->header('X-API-KEY') !== $secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key không hợp lệ'
            ], 401);
        }
        
        return $next($request);
    }
}