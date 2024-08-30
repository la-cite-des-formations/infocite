<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Star\StarYpareoController;
use App\Http\Controllers\Star\StarDBController;
use App\Rubric;

class StarViewController extends Controller
{
    public function index()
    {
        // $starDBController = new StarDBController();
        // $starDBController::BlendDB();
        // $trainings = StarYpareoController::getDegreesPrep();
        // foreach ($trainings as $training) {
        //     if ($training['plusUtilise'] == 0) {
        //         dump($training);
        //     }
        // }

        dump(Rubric::getRubrics('N', 'star'));


        return view("star.home", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }

    public function mediation()
    {
        return view("star.service.educatif.mediation.preview", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }

    public function mediationBilan()
    {
        return view("star.service.educatif.mediation.bilan", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }

    public function ash()
    {
        return view("star.service.educatif.ash.preview", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }

    public function mesureed()
    {
        return view("star.service.educatif.mesuresed.preview", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }
    public function assiduite()
    {
        return view("star.service.educatif.assiduite.preview", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }
}
