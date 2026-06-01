<?php

namespace App\Http\Controllers;

/**
 * Contrôleur gérant l'affichage du tableau de bord.
 */
class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'application.
     * Permet de spécifier quel onglet ou section du dashboard afficher.
     *
     * @param string $dashboard Identifiant de la section du dashboard (défaut: 'main').
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($dashboard = 'main')
    {
        return view('admin.dashboard', ['dashboard' => $dashboard]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
