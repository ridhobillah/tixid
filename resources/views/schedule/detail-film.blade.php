@extends('templates.app')

@section('content')
    <div class="container pt-5">

        <div class="w-75 d-flex justify-content-center m-auto">
            <div style="width: 150px; height: 200px;">
                <img src="{{ asset('storage/' . $movie['poster']) }}" class="w-100 rounded" alt="{{ $movie['title'] }}">
            </div>
            <div class="ms-5 mt-4">
                <h5 class="fw-bold">{{ $movie['title'] }}</h5>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><b class="text-seccondary">Genre</b></td>
                        <td class="ps-3">{{ $movie['genre'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-seccondary">Durasi</b></td>
                        <td class="ps-3">{{ $movie['duration'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-seccondary">Sutradara</b></td>
                        <td class="ps-3">{{ $movie['director'] }}</td>
                    </tr>
                    <tr>
                        <td><b class="text-seccondary">Rating Usia</b></td>
                        <td class="ps-3"><span class="badge bg-danger">{{ $movie['age_rating'] }}+</span></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4 flex-column align-items-center">
            @php
                if (request()->get('price')) {
                    $activeTab = true;

                    if (request()->get('price') == "ASC") {
                        $typePrice = 'DESC';
                    } else {
                        $typePrice = 'ASC';
                    }
                } else {
                    $activeTab = false;

                    $typePrice = 'ASC';
                }
            @endphp
            {{-- tab sinopsis + jadwal --}}
            <ul class="nav nav-underline">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab == false ? 'active' : '' }}" aria-current="page" data-bs-toggle="tab"
                        data-bs-target="#sinopsis-tab-pane">Sinopsis</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab == true ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#jadwal-tab-pane">Jadwal</button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade {{ $activeTab == false ? 'show active' : '' }}" id="sinopsis-tab-pane"
                    role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="mt-3 w-75 d-block mx-auto" style="text-align: justify">
                        {{ $movie['description'] }}
                    </div>
                </div>
                <div class="tab-pane fade {{ $activeTab == true ? 'show active' : '' }}" id="jadwal-tab-pane"
                    role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="dropdown my-3 w-100">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton"
                            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                            Sortir
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="?price={{ $typePrice }}">Harga</a></li>
                            <li><a class="dropdown-item" href="#">Alfabet</a></li>
                        </ul>
                    </div>










                    <div class="w-100 d-flex flex-column">
                        @foreach ($movie['schedules'] as $schedule)
                            <div class="d-flex justify-content-between">
                                <div><i class="fa-solid fa-building"></i> <b>{{ $schedule['cinema']['name'] }}</b>
                                </div>
                                <div>Rp. {{ number_format($schedule['price'], 0, ', ', '.') }}</div>
                            </div>
                            <small class="ms-3">{{ $schedule['cinema']['location'] }}</small>
                            <br>
                            <div class="d-flex flex-wrap">
                                @foreach ($schedule['hours'] as $index => $hours)
                                    <button class="btn btn-outline-secondary me-2" onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">{{ $hours }}</button>
                                @endforeach
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
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
            let url = "{{ route('schedules.seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}".replace(":scheduleId", scheduleId).replace(":hourId", hourId);
            btnTiket.href = url;
        }
    </script>

@endpush
