@extends('main')

@section('content')
    <section class="login">
        <div style="max-width: 500px;" class="container">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
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
