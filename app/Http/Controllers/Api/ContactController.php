<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function Contact(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|enum:pending,contacted,cancelled',
        ],
        [
            'full_name.required' => 'Họ và tên không được để trống',
            'full_name.string' => 'Họ và tên không hợp lệ',
            'full_name.max' => 'Họ và tên không được quá 255 ký tự',
            'phone.string' => 'Số điện thoại không hợp lệ',
            'phone.max' => 'Số điện thoại không được quá 255 ký tự',
            'phone.required' => 'Số điện thoại không được để trống',
            'note.string' => 'Ghi chú không hợp lệ',
            'status.enum' => 'Trạng thái không hợp lệ',
        ]);

        $contact = Contact::create($request->all());

        $receiveMails = env('MAIL_RECEIVE_CONTACT');
        $receiveMails = explode(',', $receiveMails);


        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông tin liên hệ thành công',
            'data' => $contact,
        ], 201);
    }
}
