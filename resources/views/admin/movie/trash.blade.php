@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Jadwal Tayang</h3>
        <table class="table table-bordered">
            <tr class="text-center">
                <th>#</th>
                <th>Poster</th>
                <th>judul</th>
                <th>status</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movieTrash as $key => $movie)
                <tr>
                    {{-- ambil detail data relasi dari with() $item['namarelasi'] ['data'] --}}
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <img src="{{ asset('storage/' . $movie['poster']) }}" width="120">
                    </td>

                    <td>{{ $movie['title'] }}</td>
                    <td>
                        @if ($movie['actived'] == 1)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="text-center d-flex justify-content-center gap-2">
                        <form action="{{ route('admin.movies.restore', $movie['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.movies.delete_permanent', $movie['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
