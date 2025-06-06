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
use App\Models\Countries;
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



    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Current password is required',
                'password.required' => 'New password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password.confirmed' => 'Password confirmation does not match',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Verify current password
            if (!password_verify($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['current_password' => ['Current password is incorrect']],
                ], 422);
            }

            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating password. Please try again.',
            ], 500);
        }
    }

    public function userProfile()
    {
        $user = Auth::user();

        return view('client.pages.account.profile', compact('user'));
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

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|min:2|max:255',
                'phone' => 'nullable|string|max:20',
            ], [
                'full_name.required' => 'Full name is required',
                'full_name.min' => 'Full name must be at least 2 characters',
                'full_name.max' => 'Full name cannot exceed 255 characters',
                'phone.max' => 'Phone number cannot exceed 20 characters',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->full_name = $request->full_name;
            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating profile. Please try again.',
            ], 500);
        }
    }

    public function orders()
    {
        return view('client.pages.account.orders');
    }

    public function wishlist()
    {
        return view('client.pages.account.wishlist');
    }

    public function addresses()
    {
        return view('client.pages.account.addresses');
    }

    public function addressesList()
    {
        $addresses = Auth::user()->addresses()->with(['city.state.country'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses
        ]);
    }

    public function showAddress($id)
    {
        $address = Auth::user()->addresses()->with(['city.state'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $address
        ]);
    }

    public function storeAddress(Request $request)
    {
        try {
          

            $request->validate([
                'country_code' => 'required|string',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'street' => 'required|string|max:500',
                'postal_code' => 'nullable|string|max:20',
                'label' => 'nullable|string|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Address Validation Error:', $e->errors());
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user has addresses relationship
            if (!method_exists($user, 'addresses')) {
                \Log::error('User model does not have addresses relationship');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User addresses relationship not found'
                ], 500);
            }

            // If this is set as default, remove default from other addresses
            $isDefault = $request->has('is_default') && $request->is_default;
            if ($isDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $addressData = [
                'street' => $request->street,
                'city_id' => $request->city_id,
                'label' => $request->label,
                'is_default' => $isDefault
            ];

            // Only add postal_code if it's provided
            if ($request->postal_code) {
                $addressData['postal_code'] = $request->postal_code;
            }

            $address = $user->addresses()->create($addressData);

            return response()->json([
                'status' => 'success',
                'message' => 'Address added successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {
            \Log::error('Store Address Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Error adding address: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAddress(Request $request, $id)
    {
        try {
            $request->validate([
                'country_code' => 'required|string',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'street' => 'required|string|max:500',
                'postal_code' => 'nullable|string|max:20',
                'label' => 'nullable|string|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // If this is set as default, remove default from other addresses
            if ($request->has('is_default') && $request->is_default) {
                $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            $address->update([
                'street' => $request->street,
                'city_id' => $request->city_id,
                'postal_code' => $request->postal_code,
                'label' => $request->label,
                'is_default' => $request->has('is_default') ? $request->is_default : false
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Address updated successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating address. Please try again.'
            ], 500);
        }
    }

    public function setDefaultAddress($id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Remove default from all addresses
            $user->addresses()->update(['is_default' => false]);

            // Set this address as default
            $address->update(['is_default' => true]);

            return response()->json([
                'status' => 'success',
                'message' => 'Default address updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error setting default address'
            ], 500);
        }
    }

    public function deleteAddress($id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Don't allow deleting the last address if it's default
            if ($address->is_default && $user->addresses()->count() > 1) {
                // Set another address as default
                $user->addresses()->where('id', '!=', $id)->first()->update(['is_default' => true]);
            }

            $address->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting address'
            ], 500);
        }
    }

    public function getCountries()
    {
        try {

            $countries = Countries::get();


            return response()->json([
                'status' => 'success',
                'data' => $countries
            ]);
        } catch (\Exception $e) {
            \Log::error('Get Countries Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading countries: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStates(Request $request)
    {
        try {
            $states = \App\Models\States::where('country_code', $request->country_code)->get();

            return response()->json([
                'status' => 'success',
                'data' => $states
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading states: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCities(Request $request)
    {
        try {
            $cities = \App\Models\Cities::where('state_id', $request->state_id)->get();

            return response()->json([
                'status' => 'success',
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error loading cities: ' . $e->getMessage()
            ], 500);
        }
    }
}
