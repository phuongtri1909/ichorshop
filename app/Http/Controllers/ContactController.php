<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Hiển thị danh sách các liên hệ
     */
    public function index(Request $request)
    {
        $query = Contact::query();
        
        // Filter theo ID
        if ($request->has('contact_id') && !empty($request->contact_id)) {
            $query->where('id', $request->contact_id);
        }
        
        // Filter theo khách hàng (tên hoặc số điện thoại)
        if ($request->has('customer') && !empty($request->customer)) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->customer . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer . '%');
            });
        }
        
        // Filter theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter theo ngày
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sắp xếp
        $query->orderBy('created_at', 'desc');
        
        $contacts = $query->paginate(10)->withQueryString();
        
        return view('admin.pages.contact.index', compact('contacts'));
    }

    /**
     * Hiển thị thông tin chi tiết của liên hệ
     */
    public function show(Contact $contact)
    {
        return view('admin.pages.contact.show', compact('contact'));
    }

    /**
     * Cập nhật trạng thái liên hệ
     */
    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,cancelled',
        ]);

        $contact->status = $request->status;
        $contact->save();

        return redirect()->back()->with('success', 'Trạng thái liên hệ đã được cập nhật thành công!');
    }

    /**
     * Xóa liên hệ
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Liên hệ đã được xóa thành công!');
    }
}