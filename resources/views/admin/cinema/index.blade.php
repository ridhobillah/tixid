@extends('templates.app')

@section('content')

    @if (Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show alert-top-right" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container mt-3 p-2">
         @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <div class="d-flex justify-content-end mb-3 mt-4">
              <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-secondary me-2">Data sampah</a>
            <a href="{{ route('admin.cinemas.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.cinemas.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5>Data Bioskop</h5>
        <table class="table my-3 table-bordered" id="cinemasTable">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Bioskop</th>
                <th class="text-center">Lokasi</th>
                <th class="text-center">Aksi</th>
            </tr>
            {{-- mengubah array multidimensi menjadi array asosiatif --}}
            @foreach ($cinemas   as $key => $cinema)
                <tr>
                    <td class="text-center">{{ $key + 1}}</td>
                    <td>{{ $cinema->name }}</td>
                    <td>{{ $cinema->location}}</td>
                    <td class="d-flex justify-content-center gap-2">
                        <a href="{{ route('admin.cinemas.edit', $cinema->id)}}" class="btn btn-info">Edit</a>
                        <form action="{{ route('admin.cinemas.delete', $cinema->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

@endsection

@push('script')
    <script>
        $(function () {
            $('#cinemasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.cinemas.datatables') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name'},
                    { data: 'location', name: 'location'},
                    { data: 'action', name: 'action'},

                ]
            });
        });
    </script>

@endpush
