<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Connexion sans Discord, enregistrée uniquement quand APP_ENV=local (voir routes/web.php).
 */
class DevLoginController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->canAccessPanel(filament()->getDefaultPanel()) ? '/admin' : route('home'));
    }
}
