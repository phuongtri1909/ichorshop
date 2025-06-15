<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validate email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_subscriptions,email',
        ], [
            'email.required' => 'please enter your email address.',
            'email.email' => 'please enter a valid email address.',
            'email.max' => 'please enter an email address no longer than 255 characters.',
            'email.unique' => 'this email address is already subscribed.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        // Lưu email vào database
        $subscription = NewsletterSubscription::create([
            'email' => $request->email
        ]);

        // Trả về kết quả thành công
        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to the newsletter!'
        ]);
    }
}
