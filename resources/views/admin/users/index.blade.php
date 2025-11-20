@extends('templates.app')

@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show alert-top-right" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container mt-3">
        @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <div class="d-flex justify-content-end mb-3 mt-4">
            <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5>Data Bioskop</h5>
        <table class="table my-3 table-bordered" id="usersTable">
            <tr>
                <th></th>
                <th class="text-center">Nama</th>
                <th class="text-center">Email</th>
                <th class="text-center">Role</th>
                <th class="text-center">Aksi</th>
            </tr>
            @foreach ($users as $key => $user)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if ($user['role'] == 'admin')
                            <span class="badge bg-success">admin</span>
                        @elseif ($user['role'] == 'staf')
                            <span class="badge bg-primary">staff</span>
                        @else
                            <span class="badge bg-secondary">User</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-center gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info">Edit</a>
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="post">
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
        $(function(){
            $('#usersTable').DataTable({
                processing: true,
                //data untuk datatable diproses secara serverside (controller)
                serverSide: true,
                //routing menuju fungsi yang memproses data untuk datatable
                ajax: "{{ route('admin.users.datatables') }}",
                //urutan column (td), pastikan urutan sesuai th
                //data: 'nama' -> nama diambil dari rawColumn jika addColumn, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name',},
                    { data: 'email', name: 'email',},
                    { data: 'role', name: 'role',},
                    { data: 'action', name: 'action'},
                ]
            });
        });
    </script>
@endpush
