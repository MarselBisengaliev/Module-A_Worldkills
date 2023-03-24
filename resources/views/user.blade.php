@extends('main')

@section('content')
    <section class="user">
        <div class="container">
            <h1>Block User - {{ $user->username }}</h1>
            <hr>
            <form action="{{ route('block-user', ['username' => $user->username]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" id="reason"></textarea>
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
