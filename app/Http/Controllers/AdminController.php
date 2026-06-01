<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Contrôleur gérant les fonctionnalités d'administration du portail.
 * Permet d'accéder aux différents gestionnaires de ressources (utilisateurs, groupes, etc.).
 */
class AdminController extends Controller
{
    /**
     * Affiche la vue d'administration pour un modèle de ressource spécifique.
     * Vérifie les droits d'accès via les Gates avant d'afficher le composant de gestion.
     *
     * @param  \Illuminate\Http\Request  $request Requête contenant le segment du modèle.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $models = $request->segment(2);
        return (Gate::denies("manage-{$models}")) ?
            redirect()->route('dashboard') :
            view("admin.index", ['component' => "admin.{$models}-manager"]);
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
