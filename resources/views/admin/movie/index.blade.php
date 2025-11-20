@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.trash') }}" class="btn btn-secondary me-2">Data sampah</a>
            <a href="{{ route('admin.movies.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah data</a>
        </div>
        <h5 class="mt-5">Data Film</h5>
        <table class="table table-bordered" id="moviesTable">
            <tr>
                <th>#</th>

                <th>Poster</th>
                <th>Judul film</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movies as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <img src="{{ asset('storage/' . $item['poster']) }}" width="1204">
                    </td>

                    <td>{{ $item['title'] }}</td>
                    <td>
                        @if ($item['actived'] == 1)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </td>

                    <td class="d-flex justify-content-center">
                        <button class="btn btn-secondary me-2" onclick="showModal({{ $item }})">Detail</button>
                        <a href="{{ route('admin.movies.edit', $item['id']) }}" class="btn btn-primary me-2">Edit</a>

                        <form action="{{ route('admin.movies.delete', $item['id'])}}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger me-2">Hapus</button>
                        </form>
                        @if ($item['actived'])
                           <form action="{{ route('admin.movies.nonaktif', $item['id']) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-warning me-2">Non Aktif</button>
                           </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        <!-- Modal -->
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Film</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



{{-- mengisi stack dari template --}}
@push('script')
    <script>
        function showModal(item) {
            // console.log(item);
            // menguhubungksn fungdi php asset, digabungkan dengan data yang diambil js (item)
            let image = "{{ asset('storage/') }}" + "/" + item.poster;
            // bactrip (``) : membuat string yang bisa di enter
            let content = `
            <div class="d-block mx-auto my-2">
                <img src="${image}" width="120">
            </div>
            <ol>
                <li>Judul : ${item.title}</li>
                <li>Durasi : ${item.duration}</li>
                <li>Genre : ${item.genre}</li>
                <li>Sutradara : ${item.director}</li>
                <li>Usia Minimal : <span class="badge badge-danger"> ${item.age_rating} + </span></li>
                <li>Sinopsis : ${item.description}</li>
            </ol>`;
            // memanggil variabel pada tanda `` pake ${}
            // memanggil element HTML yang akan disimpan konten diatas -> document.querySelector
            // innerHTML -> mengisi konten html
            document.querySelector("#modalDetailBody").innerHTML = content;
            // memunculkan modal
            new bootstrap.Modal(document.querySelector("#modalDetail")).show();
        }

        $(function () {
            $('#moviesTable').DataTable({
                processing: true,
                // data untuk data table di proses secara serverside (controller)
                serverSide: true,
                //routing menuju fungsi yg memproses data untuk DataTable
                ajax: "{{ route('admin.movies.datatables') }}",
                // urutan column td pastikan urutan seusai th
                //data 'nama' -> nama diambil dari rawColumn jika addColumn, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'poster_img', name: 'poster_img', orderable: false, searchable: false},
                    { data: 'title', name: 'title'},
                    { data: 'actived_badge', name: 'actived_badge', orderable: false, searchable: false},
                    { data: 'action', name: 'action'},

                ]
            });
        });
    </script>
@endpush

