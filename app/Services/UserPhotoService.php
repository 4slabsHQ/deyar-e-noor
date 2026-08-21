<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPhotoService
{
    public function applyFromRequest(Request $request, User $user): void
    {
        if ($request->boolean('remove_photo')) {
            $this->delete($user);
            $user->photo_path = null;
        }

        if ($request->hasFile('photo')) {
            $this->delete($user);
            $user->photo_path = $request->file('photo')->store('users/photos', 'public');
        }
    }

    public function delete(User $user): void
    {
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }
    }
}
