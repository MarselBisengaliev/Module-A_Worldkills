<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use App\Models\GameVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $response = [
            'scores' => $game->scores->sortByDesc('score')->map(function($score) {
                return [
                    'username' => $score->author->username,
                    'score' => $score->score,
                    'timestamp' => $score->created_at,
                ];
            })
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $slug)
    {
        $score = $request->get('score');
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $gameVersion = GameVersion::query()
            ->where('game_id', $game->id)
            ->orderBy('created_at', 'desc')
            ->first();

        GameScore::create([
            'user_id' => Auth::id(),
            'game_version_id' => $gameVersion->id,
            'score' => $score,
        ]);

        return response()->json([
            'status' => 'success',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GameScore  $gameScore
     * @return \Illuminate\Http\Response
     */
    public function show(GameScore $gameScore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GameScore  $gameScore
     * @return \Illuminate\Http\Response
     */
    public function edit(GameScore $gameScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GameScore  $gameScore
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GameScore $gameScore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GameScore  $gameScore
     * @return \Illuminate\Http\Response
     */
    public function destroy(GameScore $gameScore)
    {
        //
    }
}
