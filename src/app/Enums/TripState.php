<?php

namespace App\Enums;

enum TripState: int
{
    // Business trip is submitted
    case NEW = 0;

    // Business trip is confirmed by a secretariat
    case CONFIRMED = 1;

    // The business trip happens

    // Upon returning from the trip,
    // the traveller can update trip details

    // Trip details are updated
    case UPDATED = 2;

    // Trip editing is finalized
    case COMPLETED = 3;

    // A secretariat does an accounting for the trip

    // Trip is closed as processed
    case CLOSED = 4;

    // In case of a cancellation prior to the trip happening

    // The traveller has requested a cancellation of the trip
    case CANCELLATION_REQUEST = 5;

    // Trip cancellation is approved by a secretariat
    case CANCELLED = 6;

    public function inSlovak(): string
    {
        return match ($this) {
            self::NEW => 'Nová',
            self::CONFIRMED => 'Potvrdená',
            self::UPDATED => 'Doplnená',
            self::COMPLETED => 'Ukončená',
            self::CLOSED => 'Spracovaná',
            self::CANCELLATION_REQUEST => 'Žiadosť o stornovanie',
            self::CANCELLED => 'Stornovaná',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NEW => 'Cesta je v stave Nová. To znamená, že zatiaľ nebola potvrdená administrátorom.',

            self::CONFIRMED => 'Cesta je v stave Potvrdená.
                                Administrátor ju potvrdil a zaevidoval, ceste pridelil číselný identifikátor.
                                Teraz je možné aktualizovať niektoré z dávnejšie uvedených údajov.
                                Vykonajte tak až po absolvovaní cesty, keď si budete istý presnosťou nových údajov.',

            self::UPDATED => 'Cesta je v stave Doplnená.
                              Údaje, ktoré bolo po jej absolvovaní možné zmeniť, boli aktualizované alebo potvrdené.
                              Teraz treba doplniť údaje o výdavkoch a výsledkoch cesty.',

            self::COMPLETED => 'Cesta je v stave Ukončená.
                                Sú pri nej zaevidované všetky potrebné údaje.
                                Čaká sa na kontrolu od administrátora.',

            self::CLOSED => 'Cesta je v stave Spracovaná.
                             Už sa v nej nedajú vykonávať žiadne zmeny vycestovaným ani administrátorom.
                             Administrátor skontroloval a zaevidoval všetky údaje.',

            self::CANCELLATION_REQUEST => 'Pre cestu bola odoslaná žiadosť o stornovanie.
                                           Čaká sa na jej potvrdenie administrátorom.',

            self::CANCELLED => 'Cesta je v stave Stornovaná. Nedajú sa v nej vykonávať žiadne ďalšie zmeny.',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::NEW, self::CONFIRMED, self::UPDATED, self::COMPLETED, self::CANCELLATION_REQUEST => false,
            self::CLOSED, self::CANCELLED => true,
        };
    }

    public function icon() : string
    {
        return match ($this) {
            TripState::NEW => 'sun',
            TripState::CONFIRMED => 'file-circle-question',
            TripState::UPDATED => 'file-circle-plus',
            TripState::COMPLETED => 'file-circle-check',
            TripState::CLOSED => 'circle-check',
            TripState::CANCELLATION_REQUEST => 'file-circle-xmark',
            TripState::CANCELLED => 'circle-xmark',
        };
    }
}
