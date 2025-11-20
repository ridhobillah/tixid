@extends('templates.app')

@section('content')
    <div class="card w-50 d-block mx-auto p-4 mt-5">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('tickets.export_pdf', $ticket['id']) }}" class="btn btn-secondary">Unduh (.pdf)</a>
        </div>
        <div class="card-body d-flex justify-content-center flex-wrap">
            @foreach ($ticket['row_of_seats'] as $kursi)
                <div class="me-3">
                    <b>{{ $ticket['schedule']['cinema']['name'] }}</b>
                    <hr>
                    <b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    <p>Tanggal : {{ \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('d F, Y') }}</p>
                    <p>Waktu : {{ \Carbon\Carbon::parse($ticket['hours'])->format('H:i') }}</p>
                    <p>Kursi : {{ $kursi }}</p>
                    <p>Harga Tiket : Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
