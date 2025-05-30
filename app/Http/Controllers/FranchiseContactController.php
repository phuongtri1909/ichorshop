<?php

namespace App\Http\Controllers;

use App\Models\FranchiseContact;
use Illuminate\Http\Request;

class FranchiseContactController extends Controller
{
    /**
     * Hiển thị danh sách liên hệ nhượng quyền
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Xây dựng query dựa vào các tham số filter
        $query = FranchiseContact::query();
        
        // Filter theo mã
        if ($request->has('contact_id') && !empty($request->contact_id)) {
            $query->where('id', 'like', '%' . $request->contact_id . '%');
        }
        
        // Filter theo tên hoặc SĐT khách hàng
        if ($request->has('customer') && !empty($request->customer)) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->customer . '%')
                  ->orWhere('last_name', 'like', '%' . $request->customer . '%')
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

        // Filter theo gói nhượng quyền
        if ($request->has('franchise_code') && !empty($request->franchise_code)) {
            $query->where('franchise_code', $request->franchise_code);
        }
        
        // Sắp xếp
        $query->orderBy('created_at', 'desc');
        
        // Lấy dữ liệu phân trang
        $contacts = $query->paginate(10);
        
        // Tải thông tin gói nhượng quyền
        $contacts->load('franchise');
        
        return view('admin.pages.franchiseContact.index', compact('contacts'));
    }

    /**
     * Hiển thị chi tiết một liên hệ nhượng quyền
     *
     * @param  \App\Models\FranchiseContact  $franchiseContact
     * @return \Illuminate\View\View
     */
    public function show(FranchiseContact $franchiseContact)
    {
        // Load thông tin gói nhượng quyền
        $franchiseContact->load('franchise');
        
        return view('admin.pages.franchiseContact.show', compact('franchiseContact'));
    }

    /**
     * Xóa một liên hệ nhượng quyền
     *
     * @param  \App\Models\FranchiseContact  $franchiseContact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(FranchiseContact $franchiseContact)
    {
        $franchiseContact->delete();
        
        return redirect()->route('admin.franchise-contacts.index')
            ->with('success', 'Liên hệ nhượng quyền đã được xóa thành công!');
    }

    /**
     * Cập nhật trạng thái liên hệ nhượng quyền
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FranchiseContact  $franchiseContact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, FranchiseContact $franchiseContact)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,cancelled',
        ]);
        
        $franchiseContact->status = $request->status;
        $franchiseContact->save();
        
        return redirect()->back()->with('success', 'Trạng thái đã được cập nhật thành công!');
    }
}