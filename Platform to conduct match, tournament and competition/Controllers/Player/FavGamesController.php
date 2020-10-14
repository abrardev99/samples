<?php

namespace App\Http\Controllers\Player;

use App\Game;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavGamesController extends Controller
{
    public function index(){
        return view('player.favgame');
    }
}
