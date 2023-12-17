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
                          Prihlásený ako {{auth()->user()->name}}
                        </span>
                    </li>
                    <li>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit">
                                <i></i> Odhlásiť sa
                            </button>
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
        </header>

        <main>
            {{$slot}}
        </main>

        <footer>
            <p>Projekt tímu z Tvorby informačných systémov pre Katedru aplikovanej informatiky FMFI, 2023</p>
        </footer>
    </body>

</html>

{{--    <div>--}}
{{--        <x-simple-input name="name" type="text" label="Meno:"/>--}}
{{--        <x-simple-input name="surname" type="text" label="Priezvisko:" />--}}
{{--        <x-simple-input name="date1" type="date" label="Dátum:"/>--}}
{{--        <x-simple-input name="time1" type="time" label="Čas:"/>--}}
{{--        <x-simple-input name="refundation" type="checkbox" label="Mám záujem o refundáciu"/>--}}
{{--        <x-dropdown-input name="transportation" label="Dopravný prostriedok" :values="$transportations" selected="train"/>--}}
{{--    </div>--}}


{{--<div>--}}
{{--    <div style="width: 1512px; height: 49px; left: 0; top: 0; position:absolute; background: rgba(0, 0, 0, 0.50)"></div>--}}
{{--    <div style="width: 567px; height: 50px; left: 146px; top: 73px; position: absolute; color: white; font-size: 30px; font-family: Ubuntu; font-weight: 700; text-transform: uppercase; word-wrap: break-word">Pracovné cesty</div>--}}
{{--    <div style="width: 601px; height: 21px; left: 146px; top: 112px; position: absolute; color: white; font-size: 20px; font-family: Ubuntu; font-weight: 400; text-transform: uppercase; word-wrap: break-word">Katedra aplikovanej informatiky</div>--}}
{{--    <div style="height: 23px; left: 1338px; top: 13px; position: absolute">--}}
{{--        @auth--}}
{{--            <p>Prihlásený ako ABC</p>--}}
{{--        @endauth--}}

{{--        @guest--}}
{{--            <x-simple-input name="name" type="text" label="Meno:"/>--}}
{{--        @endguest--}}
{{--    </div>--}}
{{--</div>--}}
