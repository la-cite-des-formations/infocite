<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Star\StarYpareoController;
use App\Rubric;

class StarViewController extends Controller
{
    public function index()
    {
        $diplome = StarYpareoController::getDegreesPrep();
        dump($diplome);
        return view("star.home", ['rubrics' => Rubric::getRubrics('N', 'star'), 'diplome' => StarYpareoController::getDegreesPrep()]);
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
