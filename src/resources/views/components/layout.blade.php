@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://kit.fontawesome.com/d99df24710.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <title>Evidencia pracovných ciest</title>
</head>

<body>
<header class="header">
    <div class="header-top-panel">
        <div class="container d-flex justify-content-end py-2">
            @auth
                <span>
                          Prihlásený ako {{Auth::user()->first_name.' '.Auth::user()->last_name}}
                        </span>
                <form method="POST" action="/logout">
                    @csrf
                    <button><i></i> Odhlásiť sa</button>
                </form>
            @else
                <form method="POST" action="/user">
                    @csrf
                    <label for="username" class="text-white">Prihlasovacie meno: </label>
                    <input name="username" id="username"/>
                    <label for="password" class="text-white">Heslo: </label>
                    <input name="password" id="password" type="password"/>
                    <button type="submit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </form>
            @endauth
        </div>
    </div>
    <div class="container py-3">
        <a href="\" class="text-decoration-none">
            <h1 class="text-white text-uppercase header-h1 d-inline-flex">Pracovné cesty</h1>
            @if(Auth::check() && Auth::user()->hasRole('admin'))
                <span class="badge badge-pill badge-danger">Administrátor</span>
            @endif
            <h2 class="text-white text-uppercase">Katedra aplikovanej informatiky</h2>
        </a>
    </div>

</header>

<main class="container my-3">
    {{$slot}}
</main>


<footer>
    <p>Projekt tímu z Tvorby informačných systémov pre Katedru aplikovanej informatiky FMFI, 2023</p>
</footer>

<x-flash-message/>
</body>

</html>
