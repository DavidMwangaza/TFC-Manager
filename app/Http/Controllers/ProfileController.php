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

    // Mise à jour du profil désactivée — seul l'admin peut modifier les informations utilisateur

    // Suppression de compte désactivée
}
