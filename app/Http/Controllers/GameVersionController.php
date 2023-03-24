<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;

class GameVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $request->validate([
            'game_files' => 'mimes:zip|required'
        ]);

        try {
            $game = Game::query()->where('slug', $slug)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $gameFiles = $request->file('game_files');
        $gameFiles->storeAs("games/$slug", $gameFiles->getClientOriginalName());

        $zip = new ZipArchive();
        $filename = Storage::path("games/$slug/{$gameFiles->getClientOriginalName()}");
        if ($zip->open($filename) == TRUE) {
            $zip->extractTo(Storage::path("games/$slug"));
            $zip->close();
        }

        $versionWithoutZip = Str::replace('.zip', '', $gameFiles->getClientOriginalName());
        if (Storage::exists("games/$slug/$versionWithoutZip/thumbnail.png")) {
            $game->update([
                'optional_thumbnail' => "/games/$slug/$versionWithoutZip/thumbnail.png"
            ]);
        }

        $gameVersion = GameVersion::create([
            'game_id' => $game->id,
            'path_to_game_files' => "/games/$slug/$versionWithoutZip"
        ]);

        return response()->json([
            'message' => 'success',
            'created_at' => $gameVersion->created_at
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GameVersion  $gameVersion
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug, $version)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
            $gameVersion = GameVersion::query()->where('game_id', $game->id)->where('id', $version)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        return response()->json([
            'path' => public_path('storage' . $gameVersion->path_to_game_files)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GameVersion  $gameVersion
     * @return \Illuminate\Http\Response
     */
    public function edit(GameVersion $gameVersion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GameVersion  $gameVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GameVersion $gameVersion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GameVersion  $gameVersion
     * @return \Illuminate\Http\Response
     */
    public function destroy(GameVersion $gameVersion)
    {
        //
    }
}
