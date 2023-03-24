<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use App\Models\GameVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $allowedSortBy = ['title', 'popular' ,'uploaddate'];
        $allowedSortDir = ['asc', 'desc'];
        
        $sortBy = $request->query("sortBy", 'title');
        $sortDir = $request->query("sortDir", 'asc');
        $page = $request->query("page", 0);
        $size = $request->query("size", 10);
        
        if(!in_array($sortBy, $allowedSortBy) || !in_array($sortDir, $allowedSortDir)) {
        return;
        }
        
        $games = Game::query();
        
        if($sortBy == "popular") {
            $games = $games->withCount("scores")->orderBy("scores_count", $sortDir);
        }
        else if($sortBy == "uploaddate") {
            $games = $games->join('game_versions', 'games.id', '=', 'game_versions.game_id')
                        ->select('games.*')
                        ->orderBy('game_versions.created_at', $sortDir);
        }
        else {
            $games = $games->orderBy($sortBy, $sortDir);
        }
        
        $games = $games->paginate($size, ['*'], 'page', $page);


        $response = [];
        foreach ($games as $game) {
            $gameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($gameVersion) {
                $response[] = [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description,
                    'thumbnail' => $game->optional_thumbnail,
                    'uploadTimestamp' => $gameVersion->created_at,
                    'author' => $game->author->username,
                    'scoreCount' => count($game->scores)
                ];
            }
        }

        return response()->json([
            'page' => $page,
            'size' => $size,
            'totalElements' => 15,
            'content' => $response
        ]);
    }

    public function store(Request $request)
    {

        $validator = $request->validate([
            'title' => 'required|min:3|max:60',
            'description' => 'required||min:0|max:200'
        ]);

        $slug = Str::slug($validator['title']);
        $slugIsExists = Game::query()->where('slug', $slug)->first();
        if ($slugIsExists) {
            return response()->json([
                'status' => 'invalid',
                'slug' => 'Game title already exists'
            ]);
        }

        $game = Game::create([
            'title' => $validator['title'],
            'description' => $validator['description'],
            'slug' => $slug,
            'optional_thumbnail' => null,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'slug' => $game->slug
        ], 201);
    }

        /**
     * Display the specified resource.
     *
     */
    public function show(string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $lastGameVersion = $game->versions()->orderBy('created_at', 'desc')->first();

        return response()->json([
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => $game->optional_thumbnail,
            'uploadTimestamp' => $lastGameVersion->created_at,
            'author' => $game->author->username,
            'scoreCount' => count($game->scores),
            'gamePath' => $lastGameVersion->path_to_game_files
        ], 200);
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GameVersion  $gameVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $slug)
    {
        try {
            Game::query()->where('user_id', auth()->id())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $game->update([
            'title' => $request->get('title'),
            'description' => $request->get('description')
        ]);

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function destroy(string $slug)
    {
        try {
            Game::query()->where('user_id', auth()->id())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        foreach ($game->versions as $version) {
            $version->scores()->delete();
        }

        $game->versions()->delete();
        $game->delete();

        return response('', 204);
    }
}
