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

    // Trip editing is finalized
    case COMPLETED = 2;

    // A secretariat does an accounting for the trip

    // Trip is closed as processed
    case CLOSED = 3;

    // In case of a cancellation prior to the trip happening

    // The traveller has requested a cancellation of the trip
    case CANCELLATION_REQUEST = 4;

    // Trip cancellation is approved by a secretariat
    case CANCELLED = 5;

    public function inSlovak(): string
    {
        return match ($this) {
            self::NEW => 'Nová',
            self::CONFIRMED => 'Potvrdená',
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
                                Teraz je možné aktualizovať niektoré z dávnejšie uvedených údajov. Ak nechcete vykonať žiadne zmeny, údaje iba potvrďte.
                                Vykonajte tak až po absolvovaní cesty, keď si budete istý presnosťou týchto údajov. Zároveň doplňte údaje o výdavkoch a výsledkoch cesty.',

            self::COMPLETED => 'Cesta je v stave Ukončená.
                                Sú pri nej zaevidované všetky potrebné údaje.
                                Čaká sa na kontrolu od administrátora.',

            self::CLOSED => 'Cesta je v stave Spracovaná.
                             Administrátor skontroloval a zaevidoval všetky údaje.',

            self::CANCELLATION_REQUEST => 'Pre cestu bola odoslaná žiadosť o stornovanie.
                                           Čaká sa na jej potvrdenie administrátorom.',

            self::CANCELLED => 'Cesta je v stave Stornovaná. Nedajú sa v nej vykonávať žiadne ďalšie zmeny.',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::NEW, self::CONFIRMED, self::COMPLETED, self::CANCELLATION_REQUEST => false,
            self::CLOSED, self::CANCELLED => true,
        };
    }


    public function icon() : string
    {
        return match ($this) {
            TripState::NEW => 'sun',
            TripState::CONFIRMED => 'file-circle-question',
            TripState::COMPLETED => 'file-circle-check',
            TripState::CLOSED => 'circle-check',
            TripState::CANCELLATION_REQUEST => 'file-circle-xmark',
            TripState::CANCELLED => 'circle-xmark',
        };
    }

    public function iconColor(): string
    {
        return match ($this) {
            TripState::NEW => '#ffb000',
            TripState::CONFIRMED => '#00baff',
            TripState::COMPLETED => '#00a000',
            TripState::CLOSED => '#575757',
            TripState::CANCELLATION_REQUEST => '#FF0000',
            TripState::CANCELLED => '#d9390c',
        };
    }

    public function hasTravellerReturned(): bool
    {
        return match ($this) {
            self::NEW, self::CANCELLATION_REQUEST, self::CANCELLED => false,
            self::CONFIRMED, self::COMPLETED, self::CLOSED => true,
        };
    }
}
