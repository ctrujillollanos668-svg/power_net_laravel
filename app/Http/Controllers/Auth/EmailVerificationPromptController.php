<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            if ($user && (int)$user->role_id === 1) {
                return redirect()->intended(route('dashboard', absolute: false));
            }
            if (!empty(session()->get('cart', []))) {
                return redirect()->route('carrito.index');
            }
            return redirect()->intended(route('tienda.inicio', absolute: false));
        }

        return view('auth.verify-email');
    }
}
