# Pracovné cesty

Tento systém slúži na evidenciu pracovných ciest pre Katedru aplikovanej informatiky FMFI UK BA.
Systém bol vyvinutý v jazyku PHP vo frameworku Laravel 10.0.
Detaily k aplikácii je možné nájsť v `docs/`.

Projekt vznikol v rámci predmetu Tvorba informačných systémov na FMFI UK BA v akademickom roku 2023/2024.

## Inštalácia a konfigurácia
Pre inštaláciu z tohto repozitára postupujte, prosím, podľa nasledujúcich inštrukcií.
Aplikácia pre správne fungovanie vyžaduje balík `pdftk-java >= 3.3.0` 
([repozitár](https://gitlab.com/pdftk-java/pdftk)).

V rámci inštalácie je potrebné stiahnuť si najnovšiu verziu projektu:
```sh
git pull origin main
```

Následne aplikáciu treba nakonfigurovať pre dané prostredie pomocou `src/.env`:
```sh
cd src
cp .env.example .env
```
Pre prevádzku v produkcii aplikácia potrebuje mať nastavené tieto parametre:
```dotenv
APP_NAME="Pracovné cesty"
APP_ENV=production
APP_DEBUG=false
APP_URL=

# DB connection for this app
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# DB connection for Pritomnost
PRITOMNOST_DB_CONNECTION=mysql
PRITOMNOST_DB_HOST=
PRITOMNOST_DB_PORT=
PRITOMNOST_DB_DATABASE=
PRITOMNOST_DB_USERNAME=
PRITOMNOST_DB_PASSWORD=

# Mail service configuration
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

Ďalej je potrebné nainštalovať závislosti:
```sh
composer install --optimize-autoloader --no-dev
```

Pokiaľ databáza ešte nebola naplnená, je potrebné spustiť databázové migrácie:
```sh
php artisan migrate
php artisan db:seed
```

Nakoniec je možné v rámci optimalizácie uložiť aktuálnu konfiguráciu do cache:
```sh
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

Všetky požiadavky z webového servera by mali byť smerované na `src/public/index.php`.

Ďalšie detaily ku konfigurácii je možné nájsť v [Laravel dokumentácii](https://laravel.com/docs/10.x/deployment).



## Lokálny vývoj (Laravel Sail)
Pre lokálny vývoj je pripravené prostredie v Dockeri cez [Laravel Sail](https://laravel.com/docs/10.x/sail).
Lokálne nie je potrebné PHP ani Composer, stačí Docker.

```sh
cd src
cp .env.example .env
```

V `src/.env` nastavte:
```dotenv
APP_NAME="Pracovné cesty"
DB_HOST=mysql
DB_DATABASE=cesty          # názov musí byť "cesty" (synchronizácia s Pritomnosťou s ním počíta)
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307       # port MySQL na hostiteľovi, ak je 3306 obsadený

PRITOMNOST_DB_CONNECTION=mysql
PRITOMNOST_DB_HOST=mysql
PRITOMNOST_DB_PORT=3306
PRITOMNOST_DB_DATABASE=dochadzka
PRITOMNOST_DB_USERNAME=sail
PRITOMNOST_DB_PASSWORD=password
```

Inštalácia závislostí, spustenie a príprava databázy:
```sh
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    laravelsail/php83-composer:latest composer install --ignore-platform-reqs
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

`SppSymbolSeeder` vyžaduje existujúceho používateľa s ID 1, preto pred seedom vytvorte admina:
```sh
./vendor/bin/sail artisan tinker --execute='
$u = App\Models\User::create(["first_name"=>"Admin","last_name"=>"Lokálny","personal_id"=>"1",
  "username"=>"admin","email"=>"admin@example.com","password"=>Hash::make("password"),
  "status"=>App\Enums\UserStatus::ACTIVE,"department"=>"KAI"]);
$u->user_type = App\Enums\UserType::ADMIN; $u->save();'
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan tinker --execute='App\Models\User::find(1)->assignRole("admin");'
```

| Služba | Adresa |
|---|---|
| Aplikácia (login `admin` / `password`) | http://localhost/home |
| Mailpit (odoslané e-maily) | http://localhost:8025 |
| MySQL (`sail` / `password`, DB `cesty`, `dochadzka`) | `localhost:3307` |

Testy sa spúšťajú príkazom `./vendor/bin/sail test`. Bežia nad samostatnými databázami `testing`
a `dochadzka_testing` (náhrada Pritomnosti, testy si ju vytvoria samy), takže lokálne údaje nemenia.
Testy pracujúce s databázou dedia z `Tests\DatabaseTestCase`; číselníky a roly majú pripravené,
používateľov a cesty si vyrábajú cez továrne (`User::factory()->employee()`,
`BusinessTrip::factory()->confirmed()`, `PritomnostUser::factory()->matching($user)`).

Poznámky k prostrediu:
- `src/docker/app/Dockerfile` nahrádza štandardný Sail runtime (ten sťahuje PHP z Launchpad PPA).
  Je postavený na oficiálnom `php:8.3` obraze a obsahuje aj `pdflatex` a `pdftk` pre export PDF.
- Databáza Pritomnosť (`dochadzka`) je lokálne len náhradná schéma
  (`src/docker/mysql/pritomnost-schema.sql`) s tabuľkami a stĺpcami, ktoré aplikácia používa.
  Vytvorí sa automaticky pri prvom štarte MySQL s prázdnym volume.
