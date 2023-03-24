@extends('main')

@section('content')
<section class="game">
    <div class="container">
        <h1>Game- {{ $game->title }}</h1>
        @if ($game->isDeleted)
            <div class="alert alert-warning mt-2">Game is deleted</div>
            @else
            <hr>
            <form action="{{ route('delete-game', ['slug' => $game->slug]) }}" method="GET">
                @csrf
                <button type="submit" class="btn btn-primary">Delete</button>
            </form>
            <a class="btn btn-primary my-5" href="{{ route('reset-highscores', ['slug' => $game->slug]) }}">Reset HighScores</a>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger mt-2" role="alert">
            <ul class="list-group">
                @foreach ($errors->all() as $error)
                    <li class="list-group-item">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mt-2" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if (session('message'))
        <div class="alert alert-success mt-2" role="alert">
            {{ session('message') }}
        </div>
    @endif
    </div>
</section>
@endsection
