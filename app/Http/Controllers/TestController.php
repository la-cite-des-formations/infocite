<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function sendTestEvent()
    {
        event(new TestEvent('Ceci est un test de WebSockets!'));

        return response()->json(['status' => 'Événement émis']);
    }
}
