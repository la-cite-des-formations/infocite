<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Contrôleur gérant l'authentification des utilisateurs.
 * Gère à la fois l'authentification classique de Laravel et l'authentification via Google (Socialite).
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirige l'utilisateur vers la page d'authentification de Google.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Gère le retour de l'authentification Google (Callback).
     * Récupère l'e-mail de l'utilisateur Google et tente de trouver un compte correspondant.
     * Si trouvé, connecte l'utilisateur et redirige vers l'accueil.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback()
    {
        // récupère l'utilisateur connecté via son compte google valide
        $user = User::where('google_account', Socialite::driver('google')->user()->email)->first();

        if ($user) {
            // authentification et redirection vers la page d'accueil
            Auth::login($user);
            return redirect()->route('home');
        }

        // retour à la page d'authentification en cas d'échec
        return view('auth.login');
    }
}
