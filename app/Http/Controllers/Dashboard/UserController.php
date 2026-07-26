<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\UserProfile;

class UserController extends Controller
{
    public function index(UserProfile $user)
    {
        return view('dashboard.user-profile', [
            'current_user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('user');
        }

        $current_user = Auth::user();

        $request->validate([
            'first_name'     => ['required', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:100', 'unique:users,email,' . $current_user->id],
            'cropped_avatar' => ['nullable', 'string'],
        ]);

        $current_user->first_name = $request->first_name;
        $current_user->last_name  = $request->last_name;
        $current_user->email      = $request->email;
        $current_user->bio        = $request->bio;

        // Upload cropped avatar
        if ($request->filled('cropped_avatar')) {

            $image = $request->cropped_avatar;

            // Remove Base64 prefix
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageData = base64_decode($image);

            // Delete old avatar
            if (
                $current_user->avatar &&
                $current_user->avatar !== 'default.png' &&
                File::exists(public_path('images/avatars/' . $current_user->avatar))
            ) {
                File::delete(public_path('images/avatars/' . $current_user->avatar));
            }

            // Generate filename
            $imageName = md5(time() . $current_user->id) . '.png';

            // Save image
            File::put(
                public_path('images/avatars/' . $imageName),
                $imageData
            );

            $current_user->avatar = $imageName;
        }

        $current_user->save();

        return redirect('dashboard/user')
            ->with('success', 'User data updated successfully');
    }

    // Delete avatar
    public function deleteavatar($id, $fileName)
    {
        $current_user = Auth::user();

        $current_user->avatar = 'default.png';
        $current_user->save();

        if (
            $fileName !== 'default.png' &&
            File::exists(public_path('images/avatars/' . $fileName))
        ) {
            File::delete(public_path('images/avatars/' . $fileName));
        }

        return response()->json([
            'success' => true
        ]);
    }
}