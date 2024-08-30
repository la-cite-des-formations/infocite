<?php

use App\CustomFacades\AP;
use App\Http\Controllers\Star\StarDBController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => FALSE]);

Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');

Route::redirect('/', '/une')->name('home');
Route::redirect('/admin', '/dashboard');

// Route::get('/', function() {
//     $url = 'laravel.com';

//     return HTTP::get("https://www.google.com/s2/favicons?domain={$url}");
// })->name('get.favicon');


$admin = Route::namespace('Admin')
    ->prefix('admin')
    ->name('admin.');

foreach(AP::getModels() as $model) {
    $admin
        ->middleware("can:manage-{$model}")
        ->get($model, 'AdminController@index')
        ->name("{$model}.index");
}

Route::get('dashboard', 'DashboardController@index')
    ->middleware('can:access-dashboard')
    ->name('dashboard');

Route::get('dashboard/{sub_dashboard}', 'DashboardController@index')
    ->middleware('can:access-dashboard,sub_dashboard')
    ->where(['sub_dashboard' => AP::SUBDASHBOARD_REGEX])
    ->name('dashboard.sub-dashboard');

Route::post('upload', 'ViewController@upload')->name('upload');






// STAR ////////////////
Route::group(['prefix' => 'star'], function () {
    Route::get('/', 'Star\StarViewController@index');
    Route::get('/educatif.mediation', 'Star\StarViewController@mediation');
    Route::get('/educatif.mediation/bilan', 'Star\StarViewController@mediationBilan');
    Route::get('/educatif.mesures-educatives', 'Star\StarViewController@mesureed');
    Route::get('/educatif.assiduite', 'Star\StarViewController@assiduite');
    Route::get('/educatif.ash', 'Star\StarViewController@ash');

    // Ajouter d'autres routes ici...
});
// $star = Route::namespace('STAR')
//     ->prefix('STAR')
//     ->name('STAR.');

// $star->get('mesure-educ', 'star/controller')->name('mesure-educ');


Route::get('/star/blend-db-studient', [StarDBController::class, 'BlendDBStudients']);
Route::get('/star/blend-db-degree', [StarDBController::class, 'BlendDBDegrees']);
Route::get('/star/blend-db-training', [StarDBController::class, 'BlendDBTrainings']);
Route::get('/star/blend-db-sector', [StarDBController::class, 'BlendDBSectors']);

// END STAR ///////////



// ^
  //\
 //!\\ l'ordre des routes suivantes est important.
//___\\

Route::get('personal-apps/create', 'ViewController@createPersonalApp')
    ->where(['rubric' => AP::RUBRIC_REGEX])
    ->name('personal-apps.create');

Route::get('personal-apps/{app_id}/edit', 'ViewController@editPersonalApp')
    ->where(['rubric' => AP::RUBRIC_REGEX, 'app_id' => AP::ID_REGEX])
    ->name('personal-apps.edit');

Route::get('{rubric}/create', 'ViewController@createPost')
    ->where(['rubric' => AP::RUBRIC_REGEX])
    ->name('post.create');

Route::get('{rubric}/{post_id}', 'ViewController@readPost')
    ->where(['rubric' => AP::RUBRIC_REGEX, 'post_id' => AP::ID_REGEX])
    ->name('post.index');

Route::get('{rubric}/{post_id}/edit', 'ViewController@editPost')
    ->where(['rubric' => AP::RUBRIC_REGEX, 'post_id' => AP::ID_REGEX])
    ->name('post.edit');

Route::get('{rubric}', 'ViewController@index')
    ->where(['rubric' => AP::RUBRIC_REGEX])
    ->name('rubric.index');