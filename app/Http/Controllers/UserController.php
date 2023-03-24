<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(string $username) {
        try {
            $user = User::query()->where('username', $username)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $response = [
            'username' => $user->username,
            'registeredTimestamp' => $user->registered_timestamp,
            'authoredGames' => $user->authoredGames,
            'highscores' => $user->highscores->map(function($score) {
                $game = GameVersion::query()->where('id', $score->game_version_id)->first()->game;
                return [
                    'game' => $game,
                    'score' => $score->score,
                    'tiestamp' => $score->created_at
                ];
            }),
        ];

        return response()->json($response, 200);
    }
}
