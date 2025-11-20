<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TIXID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    {{-- cdn jequery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
</head>


<body>


    <nav class="navbar navbar-expand-lg bg-body-tertiary">

        <div class="container">

            <a class="navbar-brand me-2" href="https://mdbgo.com/">
                <img src="https://play-lh.googleusercontent.com/tLaJ1U__i5vdc4TJFjsVVmu2e-K0zVtiOOhMZiqnS6BCvhpxG8rCC0hzeQuUKCtJ5g"
                    height="16" alt="MDB Logo" loading="lazy" style="margin-top: -1px;" />
            </a>


            <button data-mdb-collapse-init class="navbar-toggler" type="button" data-mdb-target="#navbarButtonsExample"
                aria-controls="navbarButtonsExample" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>


            <div class="collapse navbar-collapse" id="navbarButtonsExample">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if (Auth::check() && Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('home')}}">Dasboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#"
                                id="navbarDropdownMenuLink" role="button" aria-expanded="false">
                                Data Master
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.cinemas.index') }}">Data bisokop</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.movies.index') }}">Data film</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">Data pengguna</a>
                                </li>
                            </ul>
                        </li>
                    @elseif(Auth::check() && Auth::user()->role == 'staf')

                        <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.dashboard')}}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.schedules.index')}}">Jadwal Tiket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('staff.promos.index')}}">Promo</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cinemas.list') }}">Bioskop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tickets.index') }}">Tiket</a>
                        </li>
                    @endif
                </ul>


                <div class="d-flex align-items-center">
                    @if (Auth::check())
                        {{-- ngecek udah login atau belum --}}
                        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                    @else
                        <a href="{{ route('login') }}" data-mdb-ripple-init type="button"
                            class="btn btn-link px-3 me-2">
                            Login
                            </button>
                            <a href="{{ route('sign_up') }}" data-mdb-ripple-init type="button"
                                class="btn btn-primary me-3">
                                Sign up
                            </a>
                    @endif
                </div>
            </div>

        </div>

    </nav>

    {{-- @if (Session::get('accesDenied'))
        <div class="alert alert-danger alert-top-right">{{ Session::get('accesDenied') }}</div>
        <button type="button" class="btn-close" data-bs-dimiss="alert" aria-label="Close"></button>

    @endif --}}
@if (Session::get('accessDenied'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ Session::get('accessDenied') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    @yield('content')


    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>

    {{-- chartjs --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    @stack('script') {{-- konten dinamis javascript --}}

</body>

</html>
