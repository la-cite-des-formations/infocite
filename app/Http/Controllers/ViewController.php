<?php

namespace App\Http\Controllers;

use App\CustomFacades\AP;
use App\Rubric;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * return the existing rubric of the current route
     *
     * @param  \Illuminate\Http\Request $request
     * @return object|NULL
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
     * return the current view data bag
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $template
     * @param  string|null $mode
     * @return object
     */
    private function getViewBag(Request $request, string $template = 'posts', string $mode = NULL)
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
     * Display a post's listing of the rubric.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        $rubric = $this->getRubric($request);
        if (
            is_object($rubric) && $rubric->posts->count() == 1 &&
            $rubric->posts->first()->released &&
            session('mode', 'view') == 'view'
        ) {
                return redirect()->route('post.index', ['rubric' => $rubric->route(), 'post_id' => $rubric->posts->first()->id]);
        }
        return view("usage.index", ['viewBag' => $this->getViewBag($request)]);
    }

    /**
     * Display a post.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function readPost(Request $request)
    {
        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'post')]);
    }

    /**
     * Display a post creation form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createPost(Request $request)
    {
        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-post', 'creation')]);
    }

    /**
     * Display a post edition form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editPost(Request $request)
    {
        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-post', 'edition')]);
    }

    /**
     * Display a personal app creation form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createPersonalApp(Request $request)
    {
        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-app', 'creation')]);
    }

    /**
     * Display a personal app edition form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editPersonalApp(Request $request)
    {
        return view("usage.index", ['viewBag' => $this->getViewBag($request, 'edit-app', 'edition')]);
    }

    /**
     * Upload a file in /public/storage/uploads
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
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
