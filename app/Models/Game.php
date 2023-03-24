<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'optional_thumbnail',
        'slug',
        'user_id',
    ];

    protected $visible = [
        'slug',
        'title',
        'description'
    ];

    public function author() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function versions() {
        return $this->hasMany(GameVersion::class, 'game_id', 'id');
    }

    public function scores() {
        return $this->hasManyThrough(GameScore::class, GameVersion::class);
    }

    public function isDeleted() {
        return $this->hasOne(DeletedGames::class, 'game_id', 'id');
    }
}
