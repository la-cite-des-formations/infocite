<?php

use App\CustomFacades\AP;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Route::get('/search', 'ViewController@search')
//     ->name('post.search');

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


  //\
 //!\\ l'ordre des routes suivantes est important.
//===\\

Route::get('{rubric}/personal-apps/create', 'ViewController@createPersonalApp')
    ->where(['rubric' => AP::RUBRIC_REGEX])
    ->name('personal-apps.create');

Route::get('{rubric}/personal-apps/{app_id}/edit', 'ViewController@editPersonalApp')
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


