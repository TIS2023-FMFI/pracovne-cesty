# Pracovné cesty

Tento systém slúži na evidenciu pracovných ciest pre Katedru aplikovanej informatiky FMFI UK BA.
Systém bol vyvinutý v jazyku PHP vo frameworku Laravel 10.0.
Detaily k aplikácii je možné nájsť v `docs/`.

Projekt vznikol v rámci predmetu Tvorba informačných systémov na FMFI UK BA v akademickom roku 2023/2024.

## Inštalácia a konfigurácia
Pre inštaláciu z tohto repozitára postupujte, prosím, podľa nasledujúcich inštrukcií.

Aplikácia potrebuje:
- PHP 8.1 alebo novšie s rozšíreniami `pdo_mysql`, `mbstring`, `intl`, `gd`, `zip`, `bcmath` a Composer 2;
- MySQL 8 s dvoma databázami **na tom istom serveri**: vlastnou a databázou systému Pritomnosť.
  Prihlasovanie ich spája v jednom dotaze, preto musí mať používateľ vlastnej databázy právo čítať
  tabuľku `users` a zapisovať do tabuľky `absence` v databáze Pritomnosti a obe databázy musia
  používať rovnaké porovnávanie znakov (`utf8mb4_unicode_ci`);
- `pdflatex` v `/usr/bin/pdflatex` (cesta sa nastavuje v `src/config/laratex.php`) – ním sa tlačia
  dokumenty. Z TeX Live treba balíky `tabularx`, `array`, `multirow`, `xcolor`, `geometry`,
  `setspace`, `float`, `graphicx`, `amssymb`, `lmodern` a slovenské znaky (`fontenc` T1, `inputenc`);
  na Debiane ich prinesú `texlive-latex-base`, `texlive-latex-recommended`, `texlive-latex-extra`
  a `lmodern`;
- plánovač úloh (cron), ktorý každú minútu spustí `php artisan schedule:run` – posiela pripomienky
  správ z ciest a maže neplatné pozvánky.

Pôvodne uvádzaný balík `pdftk-java` súčasný kód nepoužíva.

Pri prvej inštalácii si stiahnite projekt:
```sh
git clone https://github.com/TIS2023-FMFI/pracovne-cesty.git
cd pracovne-cesty
```
Pri aktualizácii existujúcej inštalácie stačí `git pull origin main`.

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
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# DB connection for Pritomnost (the same MySQL server as above)
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

Premenné `DB_CONNECTION` a `PRITOMNOST_DB_CONNECTION` zo vzorového `.env` sa nepoužívajú: obe spojenia
sú typu MySQL a sú pevne pomenované v `src/config/database.php` (`cesty` a `dochadzka`).

Ďalej je potrebné nainštalovať závislosti:
```sh
composer install --optimize-autoloader --no-dev
```

Pokiaľ databáza ešte nebola naplnená, je potrebné vygenerovať kľúč aplikácie a spustiť databázové migrácie:
```sh
php artisan key:generate
php artisan migrate
php artisan db:seed
```
Na úplne prázdnej databáze `db:seed` zlyhá na vzorových ŠPP prvkoch, kým neexistuje používateľ s ID 1
(pozri `docs/poznamky.md`, bod K2). Dovtedy najprv vytvorte prvého používateľa – postup je nižšie
v časti o lokálnom vývoji.

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
DB_DATABASE=cesty
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
