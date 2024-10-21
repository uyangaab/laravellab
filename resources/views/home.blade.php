@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="/posts/create" class="btn btn-secondary">Create Post</a> <!-- Removed extra '>' -->

                    <h3>Your Blog Posts</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td><a href="/posts/{{ $post->id }}/edit" class="btn btn-default">Edit</a></td>
                                <td>
                                    <form class="float-right" action="{{route('posts.destroy', $post->id)}}" method="POST">
                                        @csrf
                                        @method("DELETE")
                                        <input type="submit" class="btn btn-danger" value="Delete">
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($posts->isEmpty())
                        <p>No posts found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
