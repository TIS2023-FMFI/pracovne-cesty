<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            'Afganistan', 'Albánsko', 'Alžírsko', 'Andorra', 'Angola', 'Antigua a Barbuda', 'Argentína',
            'Arménsko', 'Austrália', 'Azerbajdžan', 'Bahamy', 'Bahrajn', 'Bangladéš', 'Barbados',
            'Belgicko', 'Belize', 'Benin', 'Bhután', 'Bielorusko', 'Bolívia', 'Bosna a Hercegovina', 'Botswana',
            'Brazília', 'Brunej', 'Bulharsko', 'Burkina', 'Burundi', 'Cyprus', 'Čad', 'Česko', 'Čierna Hora',
            'Čile', 'Čína', 'Dánsko', 'Dominika', 'Dominikánska republika', 'Džibutsko', 'Egypt', 'Ekvádor',
            'Eritrea', 'Estónsko', 'Etiópia', 'Fidži', 'Filipíny', 'Fínsko', 'Francúzsko', 'Gabon', 'Gambia',
            'Ghana', 'Grécko', 'Grenada', 'Gruzínsko', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti',
            'Holandsko', 'Honduras', 'Chorvátsko', 'India', 'Indonézia', 'Irán', 'Irak', 'Írsko', 'Island', 'Izrael',
            'Jamajka', 'Japonsko', 'Jemen', 'Jordánsko', 'Južná Afrika', 'Južný Sudán', 'Kambodža', 'Kamerun',
            'Kanada', 'Kapverdy', 'Katar', 'Kazachstan', 'Keňa', 'Kirgizsko', 'Kiribati', 'Kolumbia', 'Komory',
            'Kongo', 'Konžská demokratická republika', 'Kórejská ľudovodemokratická republika',
            'Kórejská republika', 'Kostarika', 'Kuba', 'Kuvajt', 'Laos', 'Lesotho', 'Libanon', 'Libéria',
            'Líbya', 'Lichtenštajnsko', 'Litva', 'Lotyšsko', 'Luxembursko', 'Severné Macedónsko', 'Madagaskar',
            'Maďarsko', 'Malajzia', 'Malawi', 'Maldivy', 'Mali', 'Malta', 'Maroko', 'Marshallove ostrovy',
            'Maurícius', 'Mauritánia', 'Mexiko', 'Mikronézia', 'Mjanmarsko', 'Moldavsko', 'Monako',
            'Mongolsko', 'Mozambik', 'Namíbia', 'Nauru', 'Nemecko', 'Nepál', 'Niger', 'Nigéria', 'Nikaragua',
            'Nórsko', 'Nový Zéland', 'Omán', 'Pakistan', 'Palau', 'Panama', 'Papua-Nová Guinea', 'Paraguaj',
            'Peru', 'Pobrežie Slonoviny', 'Poľsko', 'Portugalsko', 'Rakúsko', 'Rovníková Guinea', 'Rumunsko',
            'Rusko', 'Rwanda', 'Salvádor', 'Samoa', 'San Maríno', 'Saudská Arábia', 'Senegal', 'Seychely',
            'Sierra Leone', 'Singapur', 'Slovensko', 'Slovinsko', 'Somálsko', 'Spojené arabské emiráty',
            'Spojené kráľovstvo', 'Spojené štáty', 'Srbsko', 'Srí Lanka', 'Stredoafrická republika', 'Sudán',
            'Surinam', 'Svätá Lucia', 'Svätý Krištof a Nevis', 'Svätý Tomáš a Princov ostrov',
            'Svätý Vincent a Grenadíny', 'Svazijsko', 'Sýria', 'Šalamúnove ostrovy', 'Španielsko',
            'Švajčiarsko', 'Švédsko', 'Tadžikistan', 'Taliansko', 'Tanzánia', 'Thajsko', 'Togo', 'Tonga',
            'Trinidad a Tobago', 'Tunisko', 'Turecko', 'Turkménsko', 'Tuvalu', 'Uganda', 'Ukrajina', 'Uruguaj',
            'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Východný Timor', 'Zambia', 'Zimbabwe'
        ];

        foreach ($countries as $country) {
            Country::create(['name' => $country]);
        }
    }
}
