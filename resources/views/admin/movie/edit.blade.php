@extends('templates.app')

@section('content')
    <div class="w-75 mx-auto my-5">
        <form action="{{ route('admin.movies.update', $movie['id']) }}" method="post" enctype="multipart/form-data"> {{-- enctype untuk ngambil data gitu --}}
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Judul FIlm</label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title') is-invalid @enderror" value="{{ $movie['title'] }}">
                    @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="duration" class="form-label">Durasi Film</label>
                    <input type="time" name="duration" id="duration"
                        class="form-control @error('duration') is-invalid @enderror" value="{{ $movie['duration'] }}">
                    @error('duration')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="genre" class="form-label">Genre Film</label>
                    <input type="text" name="genre" id="genre" placeholder="Romantic, Comedy"
                        class="form-control @error('genre') is-invalid @enderror" value="{{ $movie['genre']}}">
                    @error('genre')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="director" class="form-label">Sutradara</label>
                    <input type="text" name="director" id="director" placeholder="Romantic, Comedy"
                        class="form-control @error('director') is-invalid @enderror" value={{ $movie['director']}}>
                    @error('director')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="age_rating" class="form-label">Usia Minimal</label>
                    <input type="number" name="age_rating" id="age_rating"
                        class="form-control @error('age_rating') is-invalid @enderror" value="{{ $movie['age_rating'] }}">
                    @error('age_rating')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="poster" class="form-label">Poster</label>
                    <img src="{{ asset('storage/' . $movie['poster']) }}" width="120" class="d-block mx-auto my-2">
                    <input type="file" name="poster" id="poster"
                        class="form-control @error('poster') is-invalid @enderror">
                    @error('poster')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label for="description" class="form-label">Sinopsis</label>
                    <textarea name="description" id="description" rows="10"
                        class="form-control @error('description') is-invalid @enderror">{{ $movie['description'] }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    </div>
@endsection
