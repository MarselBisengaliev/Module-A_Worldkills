@extends('main')

@section('content')
    @if (session('message'))
        <div class="alert alert-success mt-2 container" role="alert">
            {{ session('message') }}
        </div>
    @endif
    <section class="my-5">
        <div class="container">
            <h1>Admin Panel</h1>
            <hr class="my-5">

            <h2 class="text-center">Admins</h2>
            @if (count($admins))
                <ul class="list-group">
                    @foreach ($admins as $admin)
                        <li class="list-group-item">
                            <h4>Username: {{ $admin->username }}</h4>
                            <p>Created at: {{ $admin->registered_timestamp }}</p>
                            <p>Last login: {{ $admin->last_login_timestamp }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <h4>You are the only one admin</h4>
            @endif

            <hr class="my-5">

            <h2 class="text-center">Users</h2>
            @if (count($users))
                <ul class="list-group">
                    @foreach ($users as $user)
                        <a href="{{ route('user', ['username' => $user->username]) }}" class="list-group-item">
                            <h4>Username: {{ $user->username }}</h4>
                            <p>Created at: {{ $user->registered_timestamp }}</p>
                            <p>Last login: {{ $user->last_login_timestamp }}</p>
                        </a>
                    @endforeach
                </ul>
            @else
                <h4>There are no users on your platform</h4>
            @endif

            <h2 class="text-center">Games</h2>
            <hr>
            <form class="my-5" action="{{ route('search-games') }}" method="GET">
                <input class="form-control" type="search" name="search" placeholder="Search">
            </form>
            @if (count($games))
                <ul class="row-cols-lg-4">
                    @foreach ($games as $game)
                        <div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="{{ asset('storage/' . $game->optional_thumbnail) }}"
                                alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">{{ $game->title }}</h5>
                                <p class="card-text">Description: {{ $game->description }}</p>
                                <p class="card-text">Author: {{ $game->author->username }}</p>
                                @if ($game->isDeleted)
                                    <div class="alert alert-warning mt-2">Game is deleted</div>
                                @endif
                                <a href="{{ route('game', ['slug' => $game->slug]) }}" class="btn btn-primary">Go
                                    somewhere</a>
                            </div>
                        </div>
                    @endforeach
                </ul>
            @else
                <h4>There are no games on your platform</h4>
            @endif

            <h2>Scores</h1>
            <hr>
            @if (count($scores))
            <ul class="list-group">
                @foreach ($scores as $score)
                    <li class="list-group-item">Score: {{ $score->score }}</li>
                    <li class="list-group-item">Author: {{ $score->author->username }}</li>
                    <li class="list-group-item">Game Version: {{ $score->gameVersion->created_at }}</li>
                    <li class="list-group-item">Game: {{ $score->gameVersion->game->title }}</li>
                @endforeach
            </ul>
        @else
            <h4>There are no users on your platform</h4>
        @endif
        </div>
    </section>
@endsection
