<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use App\Models\Bookmark;
use App\Models\Banned_ip;
use App\Models\UserReading;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function show($id)
    {
        $authUser = auth()->user();
        $user = User::findOrFail($id);

        // Check permissions
        if ($authUser->role === 'mod') {
            if ($user->role === 'admin' || $user->role === 'mode') {
                abort(403, 'Unauthorized action.');
            }
        }

        // Only show active users
        if ($user->active !== 'active') {
            abort(404);
        }

        // Get financial statistics
        $stats = [
            'total_deposits' => $user->total_deposits,
            'total_spent' => $user->total_chapter_spending + $user->total_story_spending,
            'balance' => $user->coins,
            'author_revenue' => $user->role === 'author' ? $user->author_revenue : 0,
        ];

        // Get deposits with pagination
        $deposits = $user->deposits()
            ->with('bank')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'deposits_page');

        // Get chapter purchases with pagination
        $chapterPurchases = $user->chapterPurchases()
            ->with(['chapter.story'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'chapter_page');

        // Get story purchases with pagination
        $storyPurchases = $user->storyPurchases()
            ->with(['story'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'story_page');

        // Get bookmarks with pagination
        $bookmarks = $user->bookmarks()
            ->with(['story', 'lastChapter'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'bookmarks_page');

        // Get coin transactions with pagination
        $coinTransactions = $user->coinTransactions()
            ->with('admin')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'coin_page');

        // Count totals for tabs
        $counts = [
            'deposits' => $user->deposits()->count(),
            'chapter_purchases' => $user->chapterPurchases()->count(),
            'story_purchases' => $user->storyPurchases()->count(),
            'bookmarks' => $user->bookmarks()->count(),
            'coin_transactions' => $user->coinTransactions()->count(),
        ];

        return view('admin.pages.users.show', compact(
            'user',
            'stats',
            'deposits',
            'chapterPurchases',
            'storyPurchases',
            'bookmarks',
            'coinTransactions',
            'counts'
        ));
    }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $user = User::findOrFail($id);


        if ($request->has('delete_avatar') && $authUser->role === 'admin') {
            // Check if target user is admin or mod
            if (in_array($user->role, ['admin', 'mod'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa ảnh đại diện của Admin/Mod'
                ], 403);
            }

            // Delete avatar using Storage facade instead of File facade
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã xóa ảnh đại diện'
            ]);
        }

        // Special case for admin@gmail.com (super admin)
        // Get super admin emails from env
        $superAdminEmails = explode(',', env('SUPER_ADMIN_EMAILS', 'admin@gmail.com'));
        $isSuperAdmin = in_array($authUser->email, $superAdminEmails);

        if ($request->has('role')) {
            // Prevent changing super admin's role
            if (in_array($user->email, $superAdminEmails)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể thay đổi quyền của Super Admin'
                ], 403);
            }

            // Only super admin can change admin roles
            if ($user->role === 'admin' && !$isSuperAdmin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền thực hiện'
                ], 403);
            }

            $request->validate([
                'role' => 'required|in:user,admin,author'
            ], [
                'role.required' => 'Trường role không được để trống',
                'role.in' => 'Giá trị không hợp lệ'
            ]);

            $user->role = $request->role;
        }

        // Check permissions
        if ($authUser->role === 'mod') {
            if ($user->role === 'admin' || $user->id === $authUser->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền thực hiện'
                ], 403);
            }
        }

        // Handle ban toggles
        $banTypes = ['login', 'comment', 'rate', 'read'];
        foreach ($banTypes as $type) {
            $field = "ban_$type";
            if ($request->has($field)) {
                $user->$field = $request->boolean($field);
            }
        }

        try {
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }


    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = User::query();

        $stats = [
            'total' => User::where('active', 'active')->count(),
            'admin' => User::where('active', 'active')->where('role', 'admin')->count(),
            'mod' => User::where('active', 'active')->where('role', 'mod')->count(),
            'user' => User::where('active', 'active')->where('role', 'user')->count(),
            'author' => User::where('active', 'active')->where('role', 'author')->count(),
        ];

        if ($authUser->role === 'mod') {
            $query->where('role', '!=', 'admin')->where('role', '!=', 'mod');
        }


        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // IP filter
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->where('active', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pages.users.index', compact('users', 'stats'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_reset_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi mật khẩu']],
                ], 422);
            }

            if ($request->has('password') && $request->has('password_confirmation')) {
                try {
                    $request->validate([
                        'password' => 'required|min:6|confirmed',
                    ], [
                        'password.required' => 'Hãy nhập mật khẩu mới',
                        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                        'password.confirmed' => 'Mật khẩu xác nhận không khớp',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->password = bcrypt($request->password);
                $user->key_reset_password = null;

                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật mật khẩu thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = $this->generateRandomOTP();
        if ($user->reset_password_at != null) {
            $resetPasswordAt = Carbon::parse($user->reset_password_at);
            if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_reset_password = bcrypt($otp);
        $user->reset_password_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'password'));
        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
    }

    public function updateBankAccount(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_change_bank)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi thông tin ngân hàng']],
                ], 422);
            }

            if ($request->has('bank_id') && $request->has('account_number') && $request->has('account_name')) {
                try {
                    $request->validate([
                        'bank_id' => 'required|exists:banks,id',
                        'account_number' => 'required',
                        'account_name' => 'required',
                    ], [
                        'bank_id.required' => 'Hãy chọn ngân hàng',
                        'bank_id.exists' => 'Ngân hàng không tồn tại',
                        'account_number.required' => 'Hãy nhập số tài khoản',
                        'account_name.required' => 'Hãy nhập tên chủ tài khoản',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->key_change_bank = null;

                $data = [
                    'bank_id' => $request->input('bank_id'),
                    'account_number' => $request->input('account_number'),
                    'account_name' => $request->input('account_name'),
                ];

                $bank_account = $user->bankAccount()->updateOrCreate(
                    ['user_id' => $user->id], // Điều kiện để kiểm tra sự tồn tại
                    $data // Dữ liệu cần cập nhật hoặc tạo mới
                );

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật thông tin ngân hàng thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = $this->generateRandomOTP();
        if ($user->change_bank_at != null) {
            $changeBankAt = Carbon::parse($user->change_bank_at);
            if (!$changeBankAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_change_bank = bcrypt($otp);
        $user->change_bank_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'Banking'));

        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
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

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg hoặc gif',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);

            $user = Auth::user();
            DB::beginTransaction();

            try {
                // Store old avatar paths for deletion
                $oldAvatar = $user->avatar;
                $oldAvatarThumbnail = $user->avatar_thumbnail;

                // Process and save new avatar
                $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));

                // Update user avatar path
                $user->avatar = $avatarPaths['original'];
                $user->save();

                DB::commit();

                // Delete old avatars after successful update
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }
                if ($oldAvatarThumbnail) {
                    Storage::disk('public')->delete($oldAvatarThumbnail);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật avatar thành công',
                    'avatar' => $avatarPaths['original'],
                    'avatar_url' => Storage::url($avatarPaths['original']),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                // Delete new avatar if it was uploaded
                if (isset($avatarPaths)) {
                    Storage::disk('public')->delete([
                        $avatarPaths['original'],
                    ]);
                }

                \Log::error('Avatar update error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
    }

    public function updateNameOrPhone(Request $request)
    {

        if ($request->has('name')) {
            try {
                $request->validate([
                    'name' => 'required|string|min:3|max:255',
                ], [
                    'name.required' => 'Hãy nhập tên',
                    'name.string' => 'Tên phải là chuỗi',
                    'name.min' => 'Tên phải có ít nhất 3 ký tự',
                    'name.max' => 'Tên không được vượt quá 255 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.my.account')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->name = $request->name;
                $user->save();
                return redirect()->route('user.my.account')->with('success', 'Cập nhật tên thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.my.account')->with('error', 'Cập nhật tên thất bại');
            }
        } elseif ($request->has('phone')) {

            try {
                $request->validate([
                    'phone' => 'required|string|min:10|max:10',
                ], [
                    'phone.required' => 'Hãy nhập số điện thoại',
                    'phone.string' => 'Số điện thoại phải là chuỗi',
                    'phone.min' => 'Số điện thoại phải có ít nhất 10 ký tự',
                    'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.my.account')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->phone = $request->phone;
                $user->save();
                return redirect()->route('user.my.account')->with('success', 'Cập nhật số điện thoại thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.my.account')->with('error', 'Cập nhật số điện thoại thất bại');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }
    }


    public function loadMoreData(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $type = $request->type;
        $page = $request->page;

        switch ($type) {
            case 'deposits':
                $data = $user->deposits()
                    ->with('bank')
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'deposits_page', $page);
                break;
            case 'story-purchases':
                $data = $user->storyPurchases()
                    ->with(['story'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'story_page', $page);
                break;
            case 'chapter-purchases':
                $data = $user->chapterPurchases()
                    ->with(['chapter.story'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'chapter_page', $page);
                break;
            case 'bookmarks':
                $data = $user->bookmarks()
                    ->with(['story', 'lastChapter'])
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'bookmarks_page', $page);
                break;
            case 'coin-transactions':
                $data = $user->coinTransactions()
                    ->with('admin')
                    ->orderByDesc('created_at')
                    ->paginate(5, ['*'], 'coin_page', $page);
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json([
            'html' => view("admin.pages.users.partials.{$type}-table", [
                'data' => $data,
                'user' => $user
            ])->render(),
            'pagination' => $data->links()->toHtml(),
            'has_more' => $data->hasMorePages()
        ]);
    }
}
