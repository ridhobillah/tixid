@extends('templates.app')

@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <div class="container my-5">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('staff.schedules.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Data</button>
        </div>
        <h3 class="my-3">Data Jadwal Tayang</h3>
        <table class="table table-bordered" id="schedulesTable">
            <tr class="text-center">
                <th>#</th>
                <th>Bioskop</th>
                <th>Film</th>
                <th>Harga</th>
                <th>Jam Tayang</th>
                <th>Aksi</th>
            </tr>
            @foreach ($schedules as $key => $schedule)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    {{-- ambil detail data relasi dari with() $item['namarelasi'] ['data'] --}}
                    <td>{{ $schedule['cinema']['name'] }}</td>
                    <td>{{ $schedule['movie']['title'] }}</td>
                    <td class="text-center">Rp. {{ number_format($schedule['price'], 0, ',', ',') }}</td>
                    <td class="text-center">
                        {{-- karena harus array, akses dengan looping --}}
                        <ul class="list-unstyled">
                            @foreach ($schedule['hours'] as $hours)
                                {{-- bentuknya ['11.00', 12.00] ga perlu key untuk akses --}}
                                <li>{{ $hours }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center d-flex justify-content-center gap-2">
                        <a href="{{ route('staff.schedules.edit', $schedule['id']) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('staff.schedules.delete', $schedule['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        <!-- modal -->
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Jadwal</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- agar membungkus sampai modal modal-footer ke button --}}
                    <form method="POST" action="{{ route('staff.schedules.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="cinema_id" class="col-form-label">Bioskop</label>
                                <select name="cinema_id" id="cinema_id"
                                    class="form-select @error('cinema_id') is-invalid @enderror">
                                    <option disabled hidden selected>Pilih Bioskop</option>
                                    @foreach ($cinemas as $cinema)
                                        <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('cinema_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="movie_id" class="col-form-label">Film</label>
                                <select name="movie_id" id="movie_id"
                                    class="form-select @error('movie_id') is-invalid @enderror">
                                    <option disabled hidden selected>Pilih Film</option>
                                    @foreach ($movies as $movie)
                                        <option value="{{ $movie['id'] }}">{{ $movie['title'] }}</option>
                                    @endforeach
                                </select>
                                @error('movie_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="hours" class="form-label">Jam Tayang</label>
                                {{-- karena hours array, err validasi diambil dengan :  --}}
                                {{-- $errors->has() : jika dari err validasi ada err item hours --}}
                                @if ($errors->has('hours.*'))
                                    <br>
                                    <small class="text-danger">{{ $errors->first('hours.*') }}</small>
                                @endif
                                <input type="time"
                                    class="form-control @if ($errors->has('hours.*')) is-invalid @endif" id="hours"
                                    name="hours[]">
                                {{-- sediakan tempat konten tambah dari JS --}}
                                <div id="additionalInput"></div>
                                <span class="text-primary mt-2" style="cursor: pointer;" onclick="addInput()">+ Tambah
                                    Jam</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function addInput() {
            let content =
                `<input type="time" name="hours[]" class="form-control my-2">`;
            //tempat content akan ditambahkan
            let wrap = document.querySelector('#additionalInput');
            //karena nanti akan selalu bertambah, agar yang sebelumnya tidak hilang, maka gunakan : +=
            wrap.innerHTML += content;
        }

        $(function(){
            $('#schedulesTable').DataTable({
                processing: true,
                //data untuk datatable diproses secara serverside (controller)
                serverSide: true,
                //routing menuju fungsi yang memproses data untuk datatable
                ajax: "{{ route('staff.schedules.datatables') }}",
                //urutan column (td), pastikan urutan sesuai th
                //data: 'nama' -> nama diambil dari rawColumn jika addColumn, atau field dari model fillable
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'cinema', name: 'cinema', },
                    { data: 'movie', name: 'movie', },
                    { data: 'price', name: 'price', },
                    { data: 'hours', name: 'hours', },
                    { data: 'action', name: 'action'},
                ]
            });
        });
    </script>
    {{-- penegcekan php kalau ada error validasi apapun --}}
    @if ($errors->any())
        <script>
            let modalAdd = document.querySelector('#modalAdd');
            //munculkan modal dengan JS
            new bootstrap.Modal(modalAdd).show();
        </script>
    @endif
@endpush
