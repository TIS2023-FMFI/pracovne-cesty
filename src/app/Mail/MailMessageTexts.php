<?php

namespace App\Mail;

use App\Models\BusinessTrip;

class MailMessageTexts {
    public static function getDefaultMessageTextAdmin(BusinessTrip $trip): string {
        $sofiaID = $trip->sofia_id ?? '0000';
        $message = 'ID pracovnej cesty: ' . $sofiaID . PHP_EOL
        . 'Meno a priezvisko cestujúceho: ' . $trip->user->fullName() . PHP_EOL
        . 'naplánovaná na: ' . $trip->datetime_start . PHP_EOL
        . 's miestom konania: ' . $trip->place;
        return $message;
    }

    public static function getDefaultMessageTextUser(BusinessTrip $trip): string {
        $sofiaID = $trip->sofia_id ?? '0000';
        $message = 'Chceme vás informovať, že vaša pracovná cesta s ID ' .  $sofiaID
        . ' naplánovaná na ' . $trip->datetime_start
        . ' s miestom konania ' . $trip->place;
        return $message;
    }
}

?>