<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cart;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OTPForgotPWMail;
use App\Models\GoogleSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        $googleSettings = GoogleSetting::first();

        if (!$googleSettings) {
            return redirect()->route('login')
                ->with('error', 'Google login is not configured. Please contact support.');
        }

        config([
            'services.google.client_id' => $googleSettings->google_client_id,
            'services.google.client_secret' => $googleSettings->google_client_secret,
            'services.google.redirect' => route($googleSettings->google_redirect)
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $googleSettings = GoogleSetting::first();
            
            if (!$googleSettings) {
                return redirect()->route('login')
                    ->with('error', 'Google login is not configured. Please contact support.');
            }
            
            config([
                'services.google.client_id' => $googleSettings->google_client_id,
                'services.google.client_secret' => $googleSettings->google_client_secret,
                'services.google.redirect' => route($googleSettings->google_redirect)
            ]);

            $oldSessionId = session()->getId();

            // DEBUG: Kiểm tra giỏ hàng guest trước khi đăng nhập
            $guestCart = \App\Models\Cart::where('session_id', $oldSessionId)
                ->where('user_id', null)
                ->first();

            if ($guestCart) {
                // Lưu ID giỏ hàng khách vào session
                session(['guest_cart_id' => $guestCart->id]);
            }
            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                $existingUser->active = true;
                $existingUser->save();
                Auth::login($existingUser);

                $this->directMergeCart($existingUser->id, $oldSessionId);
                return redirect()->route('home');
            } else {
                $user = new User();
                $user->full_name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->password = bcrypt(Str::random(16));
                $user->active = true;
                $user->google_id = $googleUser->getId();

                if ($googleUser->getAvatar()) {
                    try {
                        $avatar = file_get_contents($googleUser->getAvatar());
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);

                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        \Log::error('Error processing Google avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);
                $this->directMergeCart($user->id, $oldSessionId);

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            \Log::error('Google login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Login with Google failed. Please try again.');
        }
    }

    protected function mergeCartsAfterLogin($userId, $oldSessionId)
    {
        try {
            // Tránh đồng bộ nếu không có session ID
            if (!$oldSessionId) {
                return;
            }

            // Tìm giỏ hàng theo session cũ
            $guestCart = Cart::where('session_id', $oldSessionId)
                ->where('user_id', null)
                ->first();

            if (!$guestCart) {
                return;
            }

            // Đảm bảo giỏ hàng khách có sản phẩm
            $guestItemsCount = $guestCart->items()->count();


            if ($guestItemsCount == 0) {

                $guestCart->delete(); // Xóa giỏ hàng trống
                return;
            }

            // Tìm giỏ hàng của user
            $userCart = Cart::firstWhere('user_id', $userId);

            if (!$userCart) {
                // Nếu user chưa có giỏ hàng, gán giỏ hàng session cho user
                $guestCart->update([
                    'user_id' => $userId,
                    'session_id' => session()->getId()
                ]);

                return;
            }

            // Gọi phương thức mergeCarts từ model Cart để đồng bộ
            Cart::mergeCarts($guestCart, $userCart);
        } catch (\Exception $e) {
            \Log::error('Error merging carts', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    protected function directMergeCart($userId, $oldSessionId)
    {
        try {
            $guestCart = \App\Models\Cart::where('session_id', $oldSessionId)
                ->where('user_id', null)
                ->first();

            if (!$guestCart && session()->has('guest_cart_id')) {
                $guestCartId = session('guest_cart_id');
                $guestCart = \App\Models\Cart::find($guestCartId);

                session()->forget('guest_cart_id');
            }

            if (!$guestCart) {
                return;
            }

            $guestItems = $guestCart->items()->get();
            $itemsCount = $guestItems->count();

            if ($itemsCount == 0) {
                $guestCart->delete();
                return;
            }

            // Lấy thông tin chi tiết item
            $itemDetails = $guestItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ];
            })->toArray();

            // Tìm giỏ hàng của user
            $userCart = \App\Models\Cart::where('user_id', $userId)->first();

            // Nếu user không có giỏ hàng, gán giỏ hàng guest cho user
            if (!$userCart) {
                $guestCart->update([
                    'user_id' => $userId,
                    'session_id' => session()->getId()
                ]);
                return;
            }

            // Chuyển từng item từ guest cart sang user cart
            foreach ($guestItems as $item) {
                $existingItem = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();

                if ($existingItem) {
                    $existingItem->quantity += $item->quantity;
                    $existingItem->save();
                } else {
                    $userCart->items()->create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }
            }

            // Cập nhật tổng tiền trong giỏ hàng user
            $userCart->updateTotalPrice();

            // Xóa giỏ hàng guest
            $guestCart->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

        // Process original image
        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "avatars/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        return [
            'original' => "avatars/{$yearMonth}/original/{$fileName}.webp",
        ];
    }

    public function register(Request $request)
    {
        // Step 1: Initial registration with email check
        if (!$request->has('otp')) {
            try {
                $request->validate([
                    'full_name' => 'required|max:255',
                    'email' => 'required|email',
                    'password' => 'required|min:6',
                ], [
                    'full_name.required' => 'Please enter your full name.',
                    'full_name.max' => 'Full name is too long.',
                    'email.required' => 'Please enter your email.',
                    'email.email' => 'The email you entered is invalid.',
                    'password.required' => 'Please enter your password.',
                    'password.min' => 'Password must be at least 6 characters.',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                // Check if user already exists and is active
                $existingUser = User::where('email', $request->email)->first();
                if ($existingUser && $existingUser->active == true) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email already exists, please login or use another email.']],
                    ], 422);
                }

                // Check if OTP was sent recently (within 3 minutes)
                if (
                    $existingUser && $existingUser->key_active != null &&
                    !$existingUser->updated_at->lt(Carbon::now()->subMinutes(1))
                ) {
                    Log::info('OTP already sent', [
                        'email' => $request->email,
                        'updated_at' => $existingUser->updated_at,
                        'current_time' => Carbon::now(),
                    ]);
                    return response()->json([
                        'status' => 'otp_sent',
                        'message' => 'You can reuse the OTP sent earlier, request a new OTP after 1 minute.',
                        'data' => [
                            'full_name' => $request->full_name,
                            'email' => $request->email,
                            'password' => $request->password
                        ]
                    ]);
                }

                // Create or update user
                if (!$existingUser) {
                    $user = new User();
                    $user->email = $request->email;
                } else {
                    $user = $existingUser;
                }

                $user->full_name = $request->full_name;
                $user->password = bcrypt($request->password);
                $user->active = false;

                // Generate and send OTP
                $otp = $this->generateRandomOTP(6);
                $user->key_active = bcrypt($otp);
                $user->save();

                Mail::to($user->email)->send(new OTPMail($otp));

                return response()->json([
                    'status' => 'otp_sent',
                    'message' => 'Registration data saved. Please check your email for the OTP.',
                    'data' => [
                        'full_name' => $request->full_name,
                        'email' => $request->email,
                        'password' => $request->password
                    ]
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during registration. Please try again later.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // Step 2: OTP verification and account activation
        try {
            $request->validate([
                'full_name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'otp' => 'required|string|size:6',
            ], [
                'otp.required' => 'Please enter your OTP.',
                'otp.size' => 'OTP must be 6 digits.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['email' => ['Email not found.']],
                ], 422);
            }

            if (!password_verify($request->otp, $user->key_active)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['The OTP you entered is invalid.']],
                ], 422);
            }

            // Activate user account
            $user->key_active = null;
            $user->full_name = $request->full_name;
            $user->password = bcrypt($request->password);
            $user->active = true;
            $user->save();

            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful! You are now logged in.',
                'url' => route('home'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during OTP verification. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email.',
            'email.email' => 'The email you entered is invalid.',
            'password.required' => 'Please enter your password.',
        ]);

        try {

            $oldSessionId = session()->getId();

            // DEBUG: Kiểm tra giỏ hàng guest trước khi đăng nhập
            $guestCart = \App\Models\Cart::where('session_id', $oldSessionId)
                ->where('user_id', null)
                ->first();

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }

            if ($user->active == false) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }

            if ($guestCart) {
                // Lưu ID giỏ hàng khách vào session
                session(['guest_cart_id' => $guestCart->id]);
            }

            Auth::login($user);

            $newSessionId = session()->getId();

            $this->directMergeCart($user->id, $oldSessionId);

            $user->ip_address = $request->ip();
            $user->save();

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred during login. Please try again later.');
        }
    }

    public function logout(Request $request)
    {
        $userId = null;
        $cartInfo = null;

        // Lưu thông tin giỏ hàng trước khi đăng xuất
        if (Auth::check()) {
            $userId = Auth::id();
            $cart = \App\Models\Cart::firstWhere('user_id', $userId);
            if ($cart) {
                $cartInfo = [
                    'id' => $cart->id,
                    'items_count' => $cart->items()->count()
                ];
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function forgotPassword(Request $request)
    {
        // Step 1: Send OTP to email
        if ($request->has('email') && !$request->has('otp')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                ], [
                    'email.required' => 'Please enter your email.',
                    'email.email' => 'The email you entered is invalid.',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Invalid credentials. Please try again.']],
                    ], 422);
                }

                // Check if OTP was sent recently (within 1 minute)
                if ($user->reset_password_at != null) {
                    $resetPasswordAt = Carbon::parse($user->reset_password_at);
                    if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(1))) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'You can reuse the OTP sent earlier, request a new OTP after 1 minute.',
                        ], 200);
                    }
                }

                $randomOTPForgotPW = $this->generateRandomOTP(6);
                $user->key_reset_password = bcrypt($randomOTPForgotPW);
                $user->reset_password_at = Carbon::now();
                $user->save();

                Mail::to($user->email)->send(new OTPForgotPWMail($randomOTPForgotPW));

                return response()->json([
                    'status' => 'success',
                    'message' => 'An OTP has been sent to your email. Please check your email to continue.',
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during password reset. Please try again later.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // Step 2: Verify OTP
        if ($request->has('email') && $request->has('otp') && !$request->has('password')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'otp' => 'required|string|size:6',
                ], [
                    'otp.required' => 'Please enter your OTP.',
                    'otp.size' => 'OTP must be 6 digits.',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Invalid credentials. Please try again.']],
                    ], 422);
                }

                if (!password_verify($request->otp, $user->key_reset_password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['otp' => ['The OTP you entered is invalid.']],
                    ], 422);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully. Please enter your new password.',
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during OTP verification. Please try again later.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // Step 3: Reset password
        if ($request->has('email') && $request->has('otp') && $request->has('password')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'otp' => 'required|string|size:6',
                    'password' => 'required|min:6',
                ], [
                    'password.required' => 'Please enter your password.',
                    'password.min' => 'Your password must be at least 6 characters long.',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Invalid credentials. Please try again.']],
                    ], 422);
                }

                if (!password_verify($request->otp, $user->key_reset_password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['otp' => ['The OTP you entered is invalid.']],
                    ], 422);
                }

                $user->key_reset_password = null;
                $user->reset_password_at = null;
                $user->password = bcrypt($request->password);
                $user->save();

                Auth::login($user);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Password reset successful! You are now logged in.',
                    'url' => route('home'),
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during password reset. Please try again later.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid request.',
        ], 400);
    }
}
