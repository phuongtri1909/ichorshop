<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 4);
        
        $faqs = Faq::orderBy('order')
            ->skip($offset)
            ->take($limit)
            ->get();
            
        return response()->json([
            'faqs' => $faqs,
            'count' => $faqs->count()
        ]);
    }
}