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


