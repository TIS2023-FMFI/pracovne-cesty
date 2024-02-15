@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Pracovné cesty KAI</title>
    <link rel="icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/d99df24710.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">


</head>

<body class="d-flex flex-column min-vh-100">
    <header class="header">
        <img class="hidden-image" src="{{asset('images/dai-header.jpg')}}" alt="cover">
        <div class="header-top-panel">
            <div class="container d-flex justify-content-end py-2 align-items-end">
                @auth
                    <span class="text-white mx-2">Prihlásený ako: <b>{{Auth::user()->first_name.' '.Auth::user()->last_name}}</b></span>
                    <form method="POST" action="{{ route('user.logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-danger">
                            <i class="text-white fa-solid fa-right-to-bracket"></i>
                        </button>
                    </form>
                @else
                    <form class="form-inline" method="POST" action="{{ route('user.login') }}">
                        @csrf
                        <div class="form-group mx-2">
                            <input
                                class="form-control form-control-sm"
                                placeholder="Prihlasovacie meno"
                                name="username"
                                id="username"
                                required/>
                        </div>

                        <div class="form-group mx-2">
                            <input
                                class="form-control form-control-sm"
                                placeholder="Heslo"
                                type="password"
                                name="password"
                                id="password"
                                required/>
                        </div>

                        <div class="form-group ml-2 mr-3">
                            <button class="btn btn-sm btn-danger">
                                <i class="text-white fa-solid fa-right-to-bracket"></i>
                            </button>
                        </div>
                    </form>
                    <a
                        class="text-white link"
                        href=""
                        data-toggle="modal"
                        data-target="#forgot-password">Zabudnuté heslo?</a>
                    <x-modals.forgot-password/>
                @endauth
            </div>
        </div>

        <div class="container py-3">
            <a href="{{ route('homepage') }}" class="text-decoration-none">
                <h1 class="text-white text-uppercase header-h1 d-inline-flex">Pracovné cesty</h1>
                @if(Auth::check() && Auth::user()->hasRole('admin'))
                    <span class="badge badge-pill badge-danger">Administrátor</span>
                @endif
                <h2 class="text-white text-uppercase">Katedra aplikovanej informatiky</h2>
            </a>
        </div>

    </header>

    <main class="container my-3 mb-5">
        {{$slot}}
    </main>

    <footer class="mt-auto bg-dark">
        <div class="container">
            <p class="my-3 text-white">Projekt tímu z Tvorby informačných systémov pre Katedru aplikovanej informatiky FMFI, 2023</p>
        </div>
    </footer>

    <x-flash-message/>
</body>

</html>
