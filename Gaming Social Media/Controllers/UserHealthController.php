<?php

namespace App\Http\Controllers;

use App\UserHealth;
use App\UserQA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserHealthController extends Controller
{
    public function default()
    {
        $userId = request('id');

        $qa = UserQA::where('user_id' , $userId)->where('question', 3)->get()->first();
        $this->store($userId, $qa->answer);

    }

    public function update(Request $request)
    {
        $health = UserHealth::where('user_id', request('id'))->get()->first();
        $health->hp = request('hp');
        $health->speed = request('speed');
        $health->strength = request('strength');
        $health->defense = request('defense');
        $health->state = request('state');
        $health->save();

        return response(['message' => 'success', 200]);
    }

    public function show(Request $request)
    {
        $user = UserHealth::where('user_id' , request('id'))->get()->first();

        return response($user, 200);
    }

    public function store($userId, $answer)
    {
        switch ($answer) {
            case 1:
                $data = [
                    'hp' => 7,
                    'strength' => 4,
                    'defense' => 5,
                    'speed' => 4,
                ];
            break;
            case 2:
                $data = [
                    'hp' => 7,
                    'strength' => 6,
                    'defense' => 4,
                    'speed' => 4,
                ];
            break;
            case 3:
                $data = [
                    'hp' => 8,
                    'strength' => 5,
                    'defense' => 3,
                    'speed' => 5,
                ];
            break;
            case 4:
                $data = [
                    'hp' => 6,
                    'strength' => 4,
                    'defense' => 6,
                    'speed' => 4,
                ];
            break;
            case 5:
                $data = [
                    'hp' => 6,
                    'strength' => 5,
                    'defense' => 7,
                    'speed' => 3,
                ];
            break;
        }

        $data['state'] = 'Neutral';
        $data['user_id'] = $userId;

        UserHealth::updateOrCreate(['user_id' => $userId ],$data);
    }
}
