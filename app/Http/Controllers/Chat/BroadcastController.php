<?php

namespace App\Http\Controllers\Chat;

use App\Events\Chat\BroadcastEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BroadcastController extends Controller {
    public function index() {
        return view("chat.broadcast");
    }

    public function postMessage() {
        event(
            new BroadcastEvent(
                request()->input("message")
            )
        );
    }
}
