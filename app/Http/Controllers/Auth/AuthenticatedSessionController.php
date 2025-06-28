<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Notifications\LoginApprovalRequested;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        //prevois data
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));

        // for login approval
    //     $credentials = $request->only('email', 'password');

    // if(Auth::attempt($credentials)) {
    //     $user = Auth::user();

    //     // Logout immediately after successful auth
    //     Auth::logout();

    //     // Mark login not yet approved
    //     $user->is_login_approved = false;
    //     $user->save();

    //     // Store pending login session
    //     $request->session()->put('pending_user_id', $user->id);

    //     // Notify admin (via email or database)
    //     \Notification::route('mail', 'admin@example.com') // or notify admin role users
    //         ->notify(new LoginApprovalRequested($user));

    //     return redirect()->route('login.pending');
    // }

    // return back()->withErrors([
    //     'email' => 'The provided credentials do not match our records.',
    // ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }



    // new methods for login approval

    public function pending()
{
    return Inertia::render('Auth/LoginPending');
}

public function checkApproval(Request $request)
{
    $userId = session('pending_user_id');

    $user = User::find($userId);

    if ($user && $user->is_login_approved) {
        Auth::login($user);
        $request->session()->forget('pending_user_id');
        return response()->json(['approved' => true]);
    }

    return response()->json(['approved' => false]);
}

}
