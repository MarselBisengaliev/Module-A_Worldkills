<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'game_version_id',
        'score'
    ];

    public function author() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gameVersion() {
        return $this->belongsTo(GameVersion::class, 'game_version_id', 'id');
    }
}
