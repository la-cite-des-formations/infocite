<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetDefaultSessionValues
{
    public function handle(Request $request, Closure $next)
    {
        // On définit la valeur SEULEMENT si elle n'existe pas déjà
        if (!$request->session()->has('displayPosts')) {
            $request->session()->put('displayPosts', 'grid');
        }

        if (!$request->session()->has('lastFilter') && !$request->session()->has('lastSorter')) {
            $request->session()->put('lastFilter', 'allPosts');
        }

        return $next($request);
    }
}
