<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameVersion extends Model
{
    use HasFactory;
    protected $fillable = [
        'game_id',
        'path_to_game_files',
    ];

    public function game() {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function scores() {
        return $this->hasMany(GameScore::class, 'game_version_id', 'id');
    }
}
