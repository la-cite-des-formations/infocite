<?php

namespace App\Http\Controllers;

use App\CustomFacades\AP;
use App\Models\Event;
use App\Models\Interaction;
use App\Models\Rubric;
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

        if ($currentUser && !$currentUser->connected_today) {
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
        if (!$rubricStr) {
            return null;
        }

        $rubricSegments = explode(AP::RUBRIC_SEPARATOR, $rubricStr);
        $rubric = Rubric::firstWhere('segment', $rubricSegments[0]);

        if (count($rubricSegments) > 1 && $rubric) {
            $rubric = $rubric->childs->firstWhere('segment', $rubricSegments[1]);
        }

        return $rubric;
    }

    /**
     * Prépare le "sac de données" (ViewBag) nécessaire au rendu de la vue 'usage.index'.
     * Regroupe les rubriques de navigation, la rubrique courante, les IDs de ressources et le mode.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $template Nom du template à utiliser (défaut: 'posts').
     * @param  string|null $mode Mode d'affichage ('creation', 'edition', etc.).
     * @return object
     */
    private function getViewBag(Request $request, string $template = 'posts', ? string $mode = NULL)
    {
        $route = $request->route();
        $rubric = $this->getRubric($request);
        $post_id = $route ? $route->parameter('post_id') : null;
        $app_id = $route ? $route->parameter('app_id') : null;
        $event_id = $route ? $route->parameter('event_id') : null;

        $chosenTemplate = in_array($template, ['edit-post', 'edit-app', 'edit-event'])
            ? $template
            : (is_object($rubric) ? ($rubric->view ?: $template) : $template);

        return (object) [
            'navRubrics' => Rubric::getRubrics('N'),
            'footerRubrics' => Rubric::getRubrics('F'),
            'rubric' => $rubric,
            'rubricSegment' => is_object($rubric) ? $rubric->segment : '',
            'currentRoute' => $request->getRequestUri(),
            'template' => $chosenTemplate,
            'post_id' => $post_id,
            'app_id' => $app_id,
            'event_id' => $event_id,
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
     * Affiche le formulaire de création d'un événement autonome dans l'agenda.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createEvent(Request $request)
    {
        $this->verifyConnectionRecord();
        $this->authorize('create', Event::class);

        $rubric = Rubric::where('segment', 'agenda')->orWhere('name', 'Agenda')->first();

        $viewBag = (object) [
            'navRubrics' => Rubric::getRubrics('N'),
            'footerRubrics' => Rubric::getRubrics('F'),
            'rubric' => $rubric,
            'rubricSegment' => $rubric ? $rubric->segment : 'agenda',
            'currentRoute' => $request->getRequestUri(),
            'template' => 'edit-event',
            'event_id' => null,
            'mode' => 'creation',
        ];

        return view("usage.index", ['viewBag' => $viewBag]);
    }

    /**
     * Affiche le formulaire de modification d'un événement autonome dans l'agenda.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $event_id
     * @return \Illuminate\Http\Response
     */
    public function editEvent(Request $request, $event_id)
    {
        $this->verifyConnectionRecord();

        $event = Event::findOrFail($event_id);
        $this->authorize('update', $event);

        $rubric = Rubric::where('segment', 'agenda')->orWhere('name', 'Agenda')->first();

        $viewBag = (object) [
            'navRubrics' => Rubric::getRubrics('N'),
            'footerRubrics' => Rubric::getRubrics('F'),
            'rubric' => $rubric,
            'rubricSegment' => $rubric ? $rubric->segment : 'agenda',
            'currentRoute' => $request->getRequestUri(),
            'template' => 'edit-event',
            'event_id' => $event_id,
            'mode' => 'edition',
        ];

        return view("usage.index", ['viewBag' => $viewBag]);
    }

    /**
     * Gère l'upload de fichiers (ex: images de l'éditeur TinyMCE).
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $fileName = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('uploads', $fileName, 'public');

        return response()->json(['location' => "/storage/$path"]);
    }
}
