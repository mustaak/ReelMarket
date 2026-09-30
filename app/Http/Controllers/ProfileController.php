<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);
        $oldProfilePicture = $profile->profile_picture;

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile-photos', 'public');
        }

        $user->update(['name' => $validated['name']]);
        $profile->update([
            'bio' => $validated['bio'] ?? null,
            ...isset($validated['profile_picture']) ? ['profile_picture' => $validated['profile_picture']] : [],
        ]);

        if (isset($validated['profile_picture']) && $oldProfilePicture) {
            Storage::disk('public')->delete($oldProfilePicture);
        }

        return redirect()->route('profile')->with('success', 'Profile updated.');
    }
}
