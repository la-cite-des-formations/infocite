<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class StarYpareoController extends Controller
{
    static protected function APIcall($action)
    {

        $url = "https://citeformations.ymag.cloud/index.php" . $action;
        $token_api = env('TOKEN_YPAREO');
        $header_token = "X-Auth-Token";

        //Sur serveur
        $response = Http::withHeaders([$header_token => $token_api])->get($url);

        //on test si la reponse et mauvaise
        if ($response->successful() ==  false) {

            if ($response->serverError()) {

                return redirect()->route('Welcome')->with('flash_message', "Une erreur s'est produite côté serveur sur une requête api; veuillez réessayer")
                    ->with('flash_type', 'alert-danger')->send();
            } elseif ($response->clientError()) {

                return redirect()->route('welcome')->with('flash_message', "Une erreur s'est produite de notre côté sur une requête api")
                    ->with('flash_type', 'alert-danger')->send();
            } else {
                $response->throw();
                return redirect()->route('welcome')->with('flash_message', "Une erreur s'est produite sur une requête api")
                    ->with('flash_type', 'alert-danger')->send();
            }
        }

        // on decode le format json
        $data = json_decode($response, true);
        return $data;
    }

    static public function getTraining()
    {
        $url = "/r/v1/formations";
        $api_data_formations = StarYpareoController::APIcall($url);
        return  $api_data_formations;
    }

    static public function getDegreesPrep()
    {
        $url = "/r/v1/diplomes-prepares";
        $api_data_studients = StarYpareoController::APIcall($url);
        return  $api_data_studients;
    }
}
