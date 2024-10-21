@extends('layouts.app')

@section('content')
    <h1>Create Post</h1>
    <form class="form-group" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf <!-- CSRF protection token -->

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="body">Body:</label>
            <textarea id="body" name="body" class="form-control" required>{{ old('body') }}</textarea>
        </div>

        <div class="form-group">
            <label for="cover_image">Cover Image:</label>
            <input type="file" id="cover_image" name="cover_image" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
