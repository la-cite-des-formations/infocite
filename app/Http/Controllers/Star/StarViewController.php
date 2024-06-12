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
        $studient = StarYpareoController::getStudient(1405596);
        // dd($studient);
        return view("star.home", ['rubrics' => Rubric::getRubrics('N', 'star'), 'studient' => $studient]);
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
        return view("star.service.educatif.ash", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }

    public function mesureed()
    {
        return view("star.service.educatif.mesureed", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }
    public function assiduite()
    {
        return view("star.service.educatif.assiduite", ['rubrics' => Rubric::getRubrics('N', 'star')]);
    }
}
