@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Jadwal Tayang</h3>
        <table class="table table-bordered">
            <tr class="text-center">
                <th>#</th>
                <th>Nama</th>
                <th>location</th>
                <th>Aksi</th>
            </tr>
            @foreach ($cinemaTrash as $key => $cinema)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    {{-- ambil detail data relasi dari with() $item['namarelasi'] ['data'] --}}
                    <td>{{ $cinema['name'] ?? ''}}</td>
                    <td>{{ $cinema['location'] ?? ''}}</td>
                    <td class="text-center d-flex justify-content-center gap-2">
                        <form action="{{ route('admin.cinemas.restore', $cinema['id']) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.cinemas.delete_permanent', $cinema['id']) }}" method="POST">
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
