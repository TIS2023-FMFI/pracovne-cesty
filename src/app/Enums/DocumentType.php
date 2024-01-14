<?php

namespace App\Enums;

enum DocumentType: string
{
    case FOREIGN_TRIP_AFFIDAVIT = 'cestne_vyhlasenie_k_zahranicnej_pc.pdf';
    case COMPENSATION_AGREEMENT = 'dohoda_o_poskytnuti_cestovnych_nahrad.pdf';
    case CONTROL_SHEET = 'kontrolny_list.pdf';
    case PAYMENT_ORDER = 'platobny_prikaz.pdf';
    case DOMESTIC_REPORT = 'sprava_z_tuzemskej_pc.pdf';
    case FOREIGN_REPORT = 'sprava_zo_zahranicnej_pc.pdf';
}
