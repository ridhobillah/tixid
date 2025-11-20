@extends('templates.app')

@section('content')
    <div class="mt-5 w-75 d-block m-auto">
        @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <nav data-mdb-navbar-init class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.promos.index') }}">Promo</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.promos.index') }}">Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Tambah</a></li>
                    </ol>
                </nav>
            </div>
        </nav>
    </div>

    <div class="card w-75 mx-auto my-3 p-4">
        <h5 class="text-center my-3">Buat Data promo</h5>
        <form action="{{ route('staff.promos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control @error('promo_code') is-invalid @enderror" id="Promo_code"
                    name="promo_code">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Diskon</label>
                <input class="form-control @error('location') is-invalid @enderror" id="discount" name="discount"
                    cols="30" rows="5"></input>
                @error('discount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Diskon</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">-- Pilih Tipe Diskon --</option>
                    <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Persen (%)</option>
                    <option value="rupiah" {{ old('type') == 'rupiah' ? 'selected' : '' }}>Rupiah (Rp)</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Buat</button>
        </form>
    </div>
@endsection
