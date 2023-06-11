<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Collection;

trait WithAlert
{
    private function sendAlert($alertData) {
        foreach($alertData as $key => $value) {
            session()->flash($key, $value);
        }
    }

    private function isEmpty($attr, $message = "") {
        if (empty($this->$attr)) {
            $this->sendAlert([
                'alertClass' => 'warning',
                'message' => (new Collection([$message, "Recommencer SVP"]))->implode('. '),
            ]);

            return TRUE;
        }
        return FALSE;
    }
}
