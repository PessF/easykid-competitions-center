<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::back()
            ->with('status', 'profile-updated')
            ->with('success', 'ข้อมูลโปรไฟล์ของคุณถูกบันทึกเรียบร้อยแล้ว');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showAvatar($id)
    {

        $user = User::findOrFail($id);

        if (!$user->avatar) {
            abort(404);
        }

        $disk = Storage::disk('google_secure');
        $path = $user->avatar;

        if (!$disk->exists($path)) {
            abort(404);
        }

        $file = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response($file, 200)->header('Content-Type', $mimeType);
    }
}
