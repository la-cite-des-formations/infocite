<?php

namespace App\Http\Controllers;

use App\CustomFacades\AP;
use App\Models\Rubric;
use App\Models\Interaction;
use Illuminate\Http\Request;

/**
 * Contrôleur principal gérant l'affichage des rubriques, des messages et des applications.
 * Assure également le suivi des connexions utilisateurs et la préparation des données pour les vues.
 */
class ViewController extends Controller
{
    /**
     * Enregistre une interaction de type 'connexion' si l'utilisateur ne l'a pas encore fait aujourd'hui.
     * Cette méthode est appelée à chaque chargement de vue majeure.
     *
     * @return void
     */
    private function verifyConnectionRecord()
    {
        // vérification de l'enregistrement de la connexion
        $currentUser = auth()->user();

        if (!$currentUser->connected_today) {
            // enregistrement de la connexion journalière pour l'utilisateur courant
            Interaction::create([
                'user_id' => $currentUser->id,
                'type' => 'connection',
                'occurred_at' => today()
            ]);
        }
    }

    /**
     * Identifie et retourne la rubrique (Rubric) correspondant à la route actuelle.
     * Gère les rubriques parentes et enfants via les segments de l'URL.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\Rubric|null
     */
    private function getRubric(Request $request) {
        $rubricStr = $request->route()->parameter('rubric');
        $rubricSegments = explode(AP::RUBRIC_SEPARATOR, $rubricStr);
        $rubric = Rubric::firstWhere('segment', $rubricSegments[0]);

        if (count($rubricSegments) > 1) {
            $rubric = $rubric->childs->firstWhere('segment', $rubricSegments[1]);
        }

        return $rubric;
    }

    /**
     * Prépare le "sac de données" (ViewBag) nécessaire au rendu de la vue 'usage.index'.
     * Regroupe les rubriques de navigation, la rubrique courante, les IDs de ressources et le mode.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $template Nom du template de contenu à charger (ex: 'posts', 'post').
     * @param  string|null $mode Mode d'affichage/action (ex: 'creation', 'edition').
     * @return object
     */
    private function getViewBag(Request $request, string $template = 'posts', ? string $mode = NULL)
    {
        $route = $request->route();
        $rubric = $this->getRubric($request);
        $post_id = $route->parameter('post_id');
        $app_id = $route->parameter('app_id');

        return (object) [
            'navRubrics' => Rubric::getRubrics('N'),
            'footerRubrics' => Rubric::getRubrics('F'),
            'rubric' => $rubric,
            'rubricSegment' => is_object($rubric) ? $rubric->segment : '',
            'currentRoute' => $request->getRequestUri(),
            'template' => is_object($rubric) ? ($rubric->view ?: $template) : $template,
            'post_id' => $post_id,
            'app_id' => $app_id,
            'mode' => $mode,
        ];
    }

    /**
     * Affiche la liste des messages d'une rubrique.
     * Redirige automatiquement vers le message s'il n'y en a qu'un seul de publié.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
     public function index(Request $request)
    {
        $this->verifyConnectionRecord();

        $rubric = $this->getRubric($request);

        if (
            session('mode', 'view') == 'view' &&
            is_object($rubric) &&
            $rubric->posts->count() == 1 &&
            ($post = $rubric->posts->first())->released
        ) {
            return redirect()->route('post.index', ['rubric' => $rubric->route(), 'post_id' => $post->id]);
        }

        return view("usage.index", ['viewBag' => $this->getViewBag($request)]);
    }

    /**
     * Affiche un message spécifique (Post).
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function readPost(Request $request)
    {
        $this->verifyConnectionRecord();

        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'post')]);
    }

    /**
     * Affiche le formulaire de création d'un message.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createPost(Request $request)
    {
        $this->verifyConnectionRecord();

        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-post', 'creation')]);
    }

    /**
     * Affiche le formulaire de modification d'un message.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editPost(Request $request)
    {
        $this->verifyConnectionRecord();

        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-post', 'edition')]);
    }

    /**
     * Affiche le formulaire de création d'une application personnelle.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createPersonalApp(Request $request)
    {
        $this->verifyConnectionRecord();

        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-app', 'creation')]);
    }

    /**
     * Affiche le formulaire de modification d'une application personnelle.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editPersonalApp(Request $request)
    {
        $this->verifyConnectionRecord();

        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-app', 'edition')]);
    }

    /**
     * Gère l'upload de fichiers vers le dossier /public/storage/uploads.
     * Retourne le chemin JSON du fichier pour intégration (ex: dans un éditeur WYSIWYG).
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $fileName = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('uploads', $fileName, 'public');

        return response()->json(['location'=>"/storage/$path"]);

        /*$imgpath = request()->file('file')->store('uploads', 'public');
        return response()->json(['location' => "/storage/$imgpath"]);*/
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
