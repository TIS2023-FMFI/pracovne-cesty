<?php

namespace App\Enums;

enum DocumentType: int
{
    case FOREIGN_TRIP_AFFIDAVIT = 0;
    case COMPENSATION_AGREEMENT = 1;
    case CONTROL_SHEET = 2;
    case PAYMENT_ORDER = 3;
    case DOMESTIC_REPORT = 4;
    case FOREIGN_REPORT = 5;

    public function fileName(): string
    {
        return match ($this) {
            self::FOREIGN_TRIP_AFFIDAVIT => 'cestne_vyhlasenie_k_zahranicnej_pc',
            self::COMPENSATION_AGREEMENT => 'dohoda_o_poskytnuti_cestovnych_nahrad',
            self::CONTROL_SHEET => 'kontrolny_list',
            self::PAYMENT_ORDER => 'platobny_prikaz',
            self::DOMESTIC_REPORT => 'sprava_z_tuzemskej_pc',
            self::FOREIGN_REPORT => 'sprava_zo_zahranicnej_pc',
        };
    }
}
