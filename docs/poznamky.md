# Poznámky k inštalácii a známe problémy

Zistené pri rozbehávaní lokálneho prostredia (11. 11. 2026). Týka sa postupu
inštalácie v `README.md` a jeho použiteľnosti v produkcii.

## Postup z README v produkcii

Postup funguje len na existujúcom serveri, kde je už pripravená fakultná
infraštruktúra (databáza Pritomnosť, reverse proxy, TeX). Na novom serveri
by bez úprav nefungoval.

## Chyby v kóde (opraviť cez TDD)

| # | Problém | Miesto | Dôsledok |
|---|---|---|---|
| K1 | **Opravené** (alias je už pevný). Pôvodne: hlavná DB sa musí volať `cesty`: alias stĺpca sa skladá z názvu DB (`{$cestyDatabase}_user_id`), ale číta sa napevno `cesty_user_id`. | `app/Http/Controllers/SynchronizationController.php` (`syncSingleUser`) | Pri inom názve DB nefunguje prihlásenie / synchronizácia používateľov. |
| K2 | `SppSymbolSeeder` priraďuje symboly používateľovi s ID 1 (`$defaultUser` nie je definovaný). | `database/seeders/SppSymbolSeeder.php` | `php artisan db:seed` na čistej DB spadne na cudzom kľúči `spp_symbols_grantee_foreign`. |
| K3 | Produkčná URL je napevno v kóde (`http://localhost:8097` → `https://kempelen.dai.fmph.uniba.sk/cesty`). | `app/Http/Controllers/UserController.php:150`, `resources/views/spp/manage.blade.php:62,66` | Na inej adrese sú nesprávne odkazy v e-mailoch a v správe SPP. Malo by stačiť `APP_URL`. |
| K4 | **Opravené** (nahradené `tests/Feature/HomepageTest`). Pôvodne: `tests/Feature/ExampleTest` očakáva 200 na `/`, aplikácia presmeruje na `/home` (302). | `tests/Feature/ExampleTest.php` | Test padá; je to pozostatok z Laravel šablóny. |

## Nepresnosti v README

R1–R6 sú v README opravené (úloha 0.6); tabuľka ostáva ako záznam.

| # | Problém |
|---|---|
| R1 | Chýba závislosť na LaTeX-e: export PDF ide cez `laratex` → `/usr/bin/pdflatex` (`config/laratex.php`). Treba TeX Live s balíkmi zo šablón (`tabularx`, `lmodern`, `multirow`, `xcolor`, `geometry`, `setspace`, `float`, `graphicx`, `amssymb`). |
| R2 | README vyžaduje `pdftk-java`, ale v kóde aplikácie sa nepoužíva (overiť, či ho nepotrebuje niečo mimo `app/`). |
| R3 | `DB_CONNECTION` a `PRITOMNOST_DB_CONNECTION` nemajú efekt – spojenia sú v `config/database.php` napevno `cesty` a `dochadzka`. |
| R4 | Prvý krok je `git pull origin main` namiesto `git clone`. |
| R5 | Nespomína, že DB Pritomnosť musí byť na tom istom MySQL serveri ako hlavná DB (synchronizácia ich spája v jednom SQL dotaze) a mať kompatibilnú koláciu (`utf8mb4_unicode_ci`). |
| R6 | Nespomína požiadavku na názov DB `cesty` (pozri K1) ani poradie pri prvej inštalácii (admin pred seedom, pozri K2). |
