<?php

use App\CustomFacades\AP;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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

// test de Firebase
Route::get('/test-firebase-auth', function () {
    // Instanciation de Firebase avec le fichier de credentials
    $firebase = (new Factory)
        ->withServiceAccount(storage_path('app/firebase_credentials_notifs-ic.json'));

    // Création d'une instance d'authentification
    $auth = $firebase->createAuth();

    // Utilisation d'un identifiant d'utilisateur fictif
    $uid = 'test-user';

    try {
        // Génération d'un token personnalisé pour cet utilisateur
        $customToken = $auth->createCustomToken($uid);
        return "Token OAuth 2 généré pour l'utilisateur '{$uid}': " . $customToken->toString();
    } catch (\Exception $e) {
        // En cas d'erreur, on renvoie le message d'erreur
        return "Erreur lors de la génération du token: " . $e->getMessage();
    }
});

Route::get('/test-send-notification', function () {
    // Instanciation de Firebase avec votre fichier de credentials
    $firebase = (new Factory)
        ->withServiceAccount(storage_path('app/firebase_credentials_notifs-ic.json'));

    $messaging = $firebase->createMessaging();

    // Remplacez cette variable par le token généré dans votre navigateur
    $token = 'dQSNE2o8gq2WjyOUmInqcj:APA91bHhR64qDx3-vGKCLTC7U_y-cSRdZvRhv264-nBvGxVCZtfIwcmkJybPpdOg7E-UpBSZnJWQKoQ3gxaMOwca8ALdLNu-wUyOP17ADzYq81jzt3KvgAQ';

    // Créez le message à envoyer
    $message = CloudMessage::new()
        ->toToken($token)
        ->withData([
            'title' => 'Test Notification',
            'body' => 'Ceci est un test de notification via FCM',
        ]);

    try {
        $messaging->send($message);
        return 'Notification envoyée avec succès !';
    } catch (\Exception $e) {
        return 'Erreur lors de l’envoi de la notification : ' . $e->getMessage();
    }
});

  //\
 //!\\ l'ordre des routes suivantes est important.
//===\\

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
