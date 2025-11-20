@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Grafik Pembelian TIket</h5>
        <div class="row justify-content-center mt-5">
            <div class="col-6">
                <h5>pembelian tiket bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-4">
                <h5>Perbandingan FIlm Aktif dan Non-Aktif</h5>
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        let labelChartBar = null;
        let dataChartBar = null;
        let dataChartPie = null;
        $(function() {
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(response) {
                    labelChartBar = response.labels;
                    dataChartBar = response.data;

                    //panggil si chart nya
                    showChart();
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart tiket');
                }
            });

            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: "GET",
                success: function(response) {
                    dataChartPie = response.data;
                    showChartPie();
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart film!');
                }
            })

        });

        function showChart() {
            const ctx = document.getElementById('chartBar');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelChartBar,
                    datasets: [{
                        label: 'Pembelian Tiket Bulan Ini',
                        data: dataChartBar,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


        }

        function showChartPie() {
            const ctx2 = document.getElementById('chartPie');

            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Film Aktif', 'Film Tidak Aktif'],
                    datasets: [{
                        label: 'My First Dataset',
                        data: dataChartPie,
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                        ],
                        hoverOffset: 4
                    }]
                }

            });

        }
    </script>
@endpush
