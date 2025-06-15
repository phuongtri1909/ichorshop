<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Response;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscriptions = NewsletterSubscription::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.pages.newsletter.index', compact('subscriptions'));
    }

    /**
     * Xóa một email đã đăng ký newsletter
     */
    public function destroy(NewsletterSubscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.newsletter.index')
            ->with('success', 'Đã xóa email đăng ký bản tin thành công!');
    }

    /**
     * Xuất danh sách email ra file CSV
     */
    public function export()
    {
        $subscriptions = NewsletterSubscription::orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="newsletter_subscriptions_' . Carbon::now()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($subscriptions) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, ['Email', 'Ngày đăng ký']);

            // Data
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->email,
                    $subscription->created_at->format('d/m/Y H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
