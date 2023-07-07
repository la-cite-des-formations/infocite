<?php

namespace App\Http\Controllers;

use App\CustomFacades\AP;
use App\Rubric;
use Illuminate\Http\Request;

class ViewController extends Controller
{
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
        $rubricStr = $request->route()->parameter('rubric');
        $rubricSegments = explode(AP::RUBRIC_SEPARATOR, $rubricStr);

        $rubric = Rubric::firstWhere('segment', $rubricSegments[0]);

        if (count($rubricSegments) > 1) {
            $rubric = $rubric->childs()->firstWhere('segment', $rubricSegments[1]);
        }

        $post_id = $request->route()->parameter('post_id');
        $app_id = $request->route()->parameter('app_id');

        return (object) [
            'navRubrics' => Rubric::getRubrics('N'),
            'footerRubrics' => Rubric::getRubrics('F'),
            'rubric' => $rubric,
            'rubricSegment' => $rubric->segment,
            'currentRoute' => $request->getRequestUri(),
            'backRoute' => '/'.$rubricStr.($post_id ? "/{$post_id}" : ''),
            'template' => $rubric->view ?: $template,
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
     * Display a post search form.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // public function search(Request $request)
    // {
    //     $resultat = request()->input('resultat');
    //         dd($resultat);
    //     return view("usage.index", ['viewBag' => $this->getViewBag($request, 'search-post', 'search')]);
    // }

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
