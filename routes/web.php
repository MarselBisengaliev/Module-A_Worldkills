<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Models\Admin;
use App\Models\BlockedUser;
use App\Models\DeletedGames;
use App\Models\Game;
use App\Models\GameScore;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin', function () {
    if (Auth::guard('admin')->check()) {
        $admins = Admin::all() ?? [];
        $users = User::all() ?? [];
        $deletedGames = DeletedGames::all() ?? [];
        $games = Game::all();
        $scores = GameScore::all();
        return view('admin', compact('admins', 'users', 'games', 'scores'));
    }
    return view('login');
})->name('admin');

Route::post('/login', [AdminController::class, 'login'])->name('login');

Route::get('/user/{username}', function($username) {
    try {
        $user = User::query()->where('username', $username)->firstOrFail();
        return view('user', compact('user'));
    } catch (ModelNotFoundException $e) {
        return redirect()->route('user', ['username' => $username])
        ->with('error', 'User has been banned before');
    }
})->name('user');

Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

Route::post('/block-user/{username}', function(Request $request, $username) {
    $request->validate([
        'reason' => 'required'
    ]);
    try {
        $user = User::query()->where('username', $username)->firstOrFail();
        
        $blockedUser = BlockedUser::query()->where('user_id', $user->id)->first();
        if ($blockedUser) {
            return redirect()->route('user', ['username' => $username])
                ->with('error', 'User has been banned before');
        }

        BlockedUser::create([
            'user_id' => $user->id,
            'reason' => $request->get('reason'),
        ]);

        return redirect()->route('user', ['username' => $username])->with('message', 'User blocked successfully');
    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'User has not been found');
    }
})->name('block-user');

Route::get('/game/{slug}', function($slug) {
    try {
        $game = Game::query()->where('slug', $slug)->firstOrFail();
        return view('game' ,compact('game'));
    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'Game has not been found');
    }
})->name('game');

Route::get('/search-games', function(Request $request) {
    $search = $request->query('search');
    
    $games = Game::query()
        ->where('title', 'LIKE', "%{$search}%")
        ->get();
    $admins = Admin::all() ?? [];
    $users = User::all() ?? [];

    return view('admin', compact('admins', 'users', 'games'));
})->name('search-games');

Route::get('/delete-game/{slug}', function($slug) {
    try {
        $game = Game::query()->where('slug', $slug)->firstOrFail();
    } catch (ModelNotFoundException $e) {
        return redirect()->route('adin')->with('error', 'Game has not been found');
    }

    $deletedGame = DeletedGames::query()->where('game_id', $game->id)->first();
    if ($deletedGame) {
        return redirect()->route('admin')->with('error', 'The game has already been deleted');
    }

    DeletedGames::create([
        'game_id' => $game->id
    ]);

    return redirect()->route('admin')->with('message', 'Game deleted successfully');
})->name('delete-game');

Route::get('/reset-highscores/{slug}', function($slug) {
    try {
        $game = Game::query()->where('slug', $slug)->firstOrFail();
    } catch (ModelNotFoundException $e) {
        return redirect()->route('adin')->with('error', 'Game has not been found');
    }

    $game->scores()->delete();
    return redirect()->back()->with('message', 'Scores reset successfully');
})->name('reset-highscores');

