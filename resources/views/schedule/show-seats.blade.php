@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 15% !important">
        <div class="card-body">
            <div>
                <b>{{ $schedule['cinema']['name'] }}</b>
                <br>
                <b>{{ now()->format('d F, Y') }} || {{ $hour }}</b>
            </div>
            <div class="alert my-2 alert-secondary">
                <i class="fa-solid fa-info text-danger me-3"></i>Anak berusia 2 tahun keatas wajib membeli tiket.
            </div>
            <div class="d-flex justify-content-center">
                <div class="row w-75">
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #112646;"></div>Kursi tersedia
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #eaeaea;"></div>Kursi terjual
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: blue;"></div>Kursi dipilih
                    </div>
                </div>
            </div>
            @php
                $row = range('A', 'H');
                $col = range(1, 18);
            @endphp

            @foreach ($row as $baris)
                <div class="d-flex justify-content-center">
                    @foreach ($col as $nomorKursi)
                        @if ($nomorKursi == 7)
                            <div style="width: 55px"></div>
                        @endif
                        @php
                            $seat = $baris . '-' . $nomorKursi;
                        @endphp

                        {{-- in array() mencari item di dalam array versi php --}}
                        @if (in_array($seat, $seatsFormat))
                          <div style="background: #eaeaea; color: black; text-align: center; padding-top: 10px; width: 45px; height: 45px; border-radius: 10px; margin: 10px 3px; cursor: pointer;">
                                {{ $baris }} {{ $nomorKursi }}
                            </div>
                        @else
                            <div style="background: #112646; color: white; text-align: center; padding-top: 10px; width: 45px; height: 45px; border-radius: 10px; margin: 10px 3px; cursor: pointer;"
                                onclick="selectedSeats('{{ $schedule->price }}', '{{ $baris }}', '{{ $nomorKursi }}', this)">
                                {{ $baris }} {{ $nomorKursi }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed-bottom w-100 bg-light text-center pt-4">
        <b class="text-center">LAYAR BIOSKOP</b>
        <div class="row mt-4" style="border: 1px solid #eaeaea">
            <div class="col-6 p-4 text-center" style="border: 1px solid #eaeaea">
                <h5>Total harga</h5>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 p-4 text-center" style="border: 1px solid #eaeaea">
                <h5>Tempat duduk</h5>
                <h5 id="seats">Belum di pilih</h5>
            </div>
        </div>

        {{-- input hidden yang di sembunyikan hanya untuk menyimpan nilai yg di perlukan js untuk proses tambah data tiket --}}
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="hours" id="hours" value="{{ $hour }}">

        <div class="text-center p-2 w-100" style="cursor: pointer" id="btnOrder"><b>RINGKASAN ORDER</b></div>
    </div>
@endsection

@push('script')
    <script>
        //menyimpan data kursi yang Dipilih
        let seats = [];
        let totalPrice = 0;

        function selectedSeats(price, baris, nomorKursi, element) {
            //buat A-1
            let seat = baris + "-" + nomorKursi;
            //cek apakah kursi ini dipilih sebelumnya, cek dari apakah ada di array seats atau engga jika ada kembalikan index nya (indexOf)
            let indexSeat = seats.indexOf(seat);
            //jika tidak ada berarti kursi baru dipilih. kalau nggak ada index nya -1
            if (indexSeat == -1) {
                //kalau gaada kasi warna biru terang dan simpan data kursi ke array diatas
                element.style.background = 'blue';
                seats.push(seat);
            } else {
                //jika ada, berarti ini klik kedua kali di kursi tersebut, kembalikan warna ke biru tua dan hapus item dari array
                element.style.background = '#112646';
                seats.splice(indexSeat, 1);
            }


            let totalPriceElement = document.querySelector("#totalPrice");
            let seatsElement = document.querySelector("#seats");
            totalPrice = price * (seats.length);
            totalPriceElement.innerText = "Rp. " + totalPrice;
            seatsElement.innerText = seats.join(", ")



            let btnOrder = document.querySelector("#btnOrder");

            if (seats.length >= 1) {
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                // buat agar ketika di klik mengarah ke proses createTicket
                btnOrder.onclick = createTicket;

            } else {
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.onclick = null;
            }
        }

        function createTicket() {
            //AJAX (asynchronus javascript and XML) : proses mengambil/menambahkan data dari/ke database. hanya bisa digunakan melalui Jquery ( library yang penulisannya berupa javascript modern, gaya penulisan lebih singkat, $())
            $.ajax({
                url: "{{ route('tickets.store') }}", //route untuk proses data
                method: "POST", //http method sesuai url
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: $("#user_id").val(), //value dari input id="user_id"
                    schedule_id: $("#schedule_id").val(),
                    hours: $("#hours").val(),
                    quantity: seats.length, //jumlah  item array seats
                    total_price: totalPrice,
                    row_of_seats: seats,
                    // fillable : value
                },
                success: function(response) {
                    // console.log(response)
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/order`;

                },
                error: function(message) { //kalau di server ada error mau ngapain
                    alert("Terjadi kesalahan saat membuat data tiket");
                }
            })


        }
    </script>
@endpush
