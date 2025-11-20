@extends('templates.app')

@section('content')
    <div class="container my-5 card">

        <div class="card-body">
            <i class="fa-solid fa-location-dot me-3">{{ $schedules[0]['cinema']['location'] }}</i>
            <hr>
            @foreach ($schedules as $schedule)
                <div class="my-2">
                    <div class="d-flex">
                        <div style="width: 150px; height: 200px;">
                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" class="w-100 rounded"
                                alt="">
                        </div>
                        <div class="ms-5 mt-4">
                            <h5 class="fw-bold">{{ $schedule['movie']['title'] }}</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><b class="text-seccondary">Genre</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['genre'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seccondary">Durasi</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['duration'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seccondary">Sutradara</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['director'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-seccondary">Rating Usia</b></td>
                                    <td class="ps-3"><span
                                            class="badge bg-danger">{{ $schedule['movie']['age_rating'] }}+</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-end">
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 my-2">
                            @foreach ($schedule['hours'] as $index => $hours)
                                <button class="btn btn-outline-secondary me-2"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">{{ $hours }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
    <div class="w-100 fixed-bottom bg-light text-center py-2" id="wrapBtn">
        <a href="javascript:void(0)" id="btnTiket">BELI TIKET</a>
    </div>
@endsection

@push('script')
    <script>
        let btnBefore = null;

        function selectedHour(scheduleId, hourId, element) {
            if (btnBefore) {
                btnBefore.style.background = '';
                btnBefore.style.color = '';
                btnBefore.style.borderColor = '';
            }

            element.style.background = '#112646';
            element.style.color = 'white';
            element.style.borderColor = '#112646';
            btnBefore = element;

            let wrapBtn = document.querySelector("#wrapBtn");
            let btnTiket = document.querySelector("#btnTiket");
            wrapBtn.style.background = "#112646";
            wrapBtn.classList.remove("bg-light");
            btnTiket.style.color = "white";


            //mengarah kan ke route web php
            let url = "{{ route('schedules.seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}".replace(
                ":scheduleId", scheduleId).replace(":hourId", hourId);
            btnTiket.href = url;
        }
    </script>
@endpush
