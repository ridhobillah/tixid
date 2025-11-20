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
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Excel (.xslx)</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5>Data Bioskop</h5>
        <table class="table my-3 table-bordered" id="promosTable">
            <tr>
                <th></th>
                <th class="text-center">Kode Promo</th>
                <th class="text-center">Diskon</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
            </tr>
            @foreach ($promos as $key => $promo)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $promo->promo_code }}</td>
                    <td>
                        @if ($promo['type'] == 'rupiah')
                            <span>Rp. {{ number_format($promo['discount'], 0, ',', ',') }}</span>
                        @else
                            <span>{{ $promo['discount'] }} %</span>
                        @endif
                    </td>

                    <td>{{ $promo->type }}</td>

                    <td>
                        @if ($promo['actived'] == 1)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </td>

                    <td class="d-flex justify-content-center gap-2">
                        <a href="{{ route('staff.promos.edit', $promo['id']) }}" class="btn btn-info">Edit</a>
                        <form action="{{ route('staff.promos.delete', $promo['id']) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                        {{-- jika actived true, munculkan opsi untuk non-aktif film --}}
                        @if ($promo['actived'] == 1)
                            <form action="{{ route('staff.promos.nonactived', $promo['id']) }}" method="post">

                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-warning">Non-Aktif</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#promosTable').DataTable({
                processing: true,
                //data untuk datatable diproses secara serverside (controller)
                serverSide: true,
                //routing menuju fungsi yang memproses data untuk datatable
                ajax: "{{ route('staff.promos.datatables') }}",
                //urutan column (td), pastikan urutan sesuai th
                //data: 'nama' -> nama diambil dari rawColumn jika addColumn, atau field dari model fillable
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'promo_code',
                        name: 'promo_code'
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'actived',
                        name: 'actived',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
