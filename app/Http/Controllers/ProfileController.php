<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('department');

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Mettre à jour le profil (avatar, bio).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'biographie' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();
        $user->biographie = $request->biographie;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour avec succès.');
    }
}
