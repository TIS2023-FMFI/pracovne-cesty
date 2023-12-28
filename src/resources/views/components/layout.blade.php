<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <title>Evidencia pracovných ciest</title>
    </head>

    <body>
        <header style="background-image: url(https://pritomnost.dai.fmph.uniba.sk/image/dai-header80.jpg)">
            <ul>
                @auth
                    <li>
                        <span>
                          Prihlásený ako {{auth()->user()->first_name}}
                        </span>
                    </li>
                    <li>
                        <form method="POST" action="/logout">
                            @csrf
                            <button> <i></i> Odhlásiť sa </button>
                        </form>
                    </li>
                @else
                    <li>
                        <form method="POST" action="/users/authenticate">
                            @csrf
                            <button type="submit">
                                <i></i> Prihlásiť sa
                            </button>
                        </form>
                    </li>
                @endauth
            </ul>

            <div class="container py-3">
                <h1 class="text-white fw-bold text-uppercase">Pracovné cesty</h1>
                <h2 class="text-white fw-bold text-uppercase">Katedra aplikovanej informatiky</h2>
            </div>
        </header>

        <main class="container my-5">
            {{$slot}}
        </main>


        <footer>
            <p>Projekt tímu z Tvorby informačných systémov pre Katedru aplikovanej informatiky FMFI, 2023</p>
        </footer>
    </body>

</html>
