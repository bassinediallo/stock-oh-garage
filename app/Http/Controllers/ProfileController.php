<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        $recentActivity = StockMovement::where('user_id', $user->id)
            ->with(['product', 'department'])
            ->latest()
            ->take(5)
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'recentActivity' => $recentActivity
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $preferences = $request->input('preferences', []);

        // Validation des préférences
        $validPreferences = array_filter($preferences, function ($value, $key) {
            switch ($key) {
                case 'notify_low_stock':
                case 'notify_movements':
                    return is_bool($value) || in_array($value, [0, 1, '0', '1']);
                case 'default_view':
                    return in_array($value, ['dashboard', 'products', 'movements']);
                default:
                    return false;
            }
        }, ARRAY_FILTER_USE_BOTH);

        $user->preferences = array_merge($user->preferences ?? [], $validPreferences);
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Préférences mises à jour avec succès.');
    }
}
