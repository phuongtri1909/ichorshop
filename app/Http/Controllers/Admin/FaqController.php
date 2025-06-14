<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faq::query();

        // Tìm kiếm theo câu hỏi
        if ($request->has('question') && !empty($request->question)) {
            $query->where('question', 'like', '%' . $request->question . '%');
        }

        // Lọc theo thứ tự
        if ($request->has('order') && !empty($request->order)) {
            $query->where('order', $request->order);
        }

        // Lọc theo ngày tạo
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $faqs = $query->orderBy('order', 'asc')->paginate(10);
        
        return view('admin.pages.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0'
        ],
        [
            'question.required' => 'Câu hỏi là bắt buộc.',
            'answer.required' => 'Trả lời là bắt buộc.',
            'order.integer' => 'Thứ tự phải là một số nguyên.',
            'order.min' => 'Thứ tự không được nhỏ hơn 0.'
        ]);

        // Nếu không có thứ tự, lấy thứ tự cao nhất + 1
        if (!$request->filled('order')) {
            $maxOrder = Faq::max('order') ?? 0;
            $request->merge(['order' => $maxOrder + 1]);
        }

        Faq::create($request->all());

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        return view('admin.pages.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0'
        ],
        [
            'question.required' => 'Câu hỏi là bắt buộc.',
            'answer.required' => 'Trả lời là bắt buộc.',
            'order.integer' => 'Thứ tự phải là một số nguyên.',
            'order.min' => 'Thứ tự không được nhỏ hơn 0.'
        ]);

        $faq->update($request->all());

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ đã được xóa thành công!');
    }
}
