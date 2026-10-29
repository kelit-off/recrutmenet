<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class DiscordAuthController extends Controller
{
    public function redirect(): SymfonyRedirect
    {
        return Socialite::driver('discord')->scopes(['identify', 'email'])->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Connexion Discord annulée.');
        }

        try {
            $discordUser = Socialite::driver('discord')->user();
        } catch (InvalidStateException $e) {
            Log::warning('Discord OAuth : état invalide', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with('error', 'La connexion a expiré, veuillez réessayer.');
        }

        $user = User::updateOrCreate(
            ['discord_id' => (string) $discordUser->getId()],
            [
                'name' => $discordUser->getName() ?: $discordUser->getNickname(),
                'email' => $discordUser->getEmail(),
                'discord_username' => $discordUser->getNickname(),
                'avatar' => $discordUser->getAvatar(),
            ],
        );

        if ($user->wasRecentlyCreated) {
            $this->assignInitialRoles($user);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $default = $user->canAccessPanel(filament()->getDefaultPanel()) ? '/admin' : route('home');

        return redirect()->intended($default);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function assignInitialRoles(User $user): void
    {
        $roles = [Role::Member->value];

        if ($user->isAdminFromEnv()) {
            $roles[] = Role::Admin->value;
            $roles[] = Role::Hr->value;
        }

        $user->syncRoles($roles);

        activity('user')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['roles' => $roles])
            ->log('Compte créé via Discord — rôles : '.implode(', ', $roles));
    }
}
