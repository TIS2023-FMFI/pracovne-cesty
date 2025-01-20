@php
    use App\Models\User;
    use App\Enums\UserStatus;

    $isAdmin = Auth::user()->hasRole('admin');
@endphp
<x-layout>
    <x-content-box title="Životný cyklus pracovnej cesty">
        <ol>
            <li>Najskôr zamestnanec (doktorand, externista, študent) vyplní údaje o plánovanej ceste a ich potvrdením odošle žiadosť na ďalšie spracovanie pracovnej cesty pani sekretárke (je notifikovaná e-mailom).</li>
            <strong>STAV CESTY: NOVÁ</strong>
            <li>Následne sekretárka zadá cestu do systému SOFIA/SAP, kde cesta dostane fakultný identifikátor (ten pridá k ceste aj do systému Cesty), skontroluje prípadne upraví údaje a potvrdí ich platnosť, čo vygeneruje mail cestujúcemu, že má prísť podpísať cestovný príkaz a cesta v systéme je pripravená na to, aby bola zadaná správa o pracovnej ceste po jej skončení. Potvrdenie cesty pani sekretárkou zároveň vytvorí záznam o neprítomnosi v daných dňoch s neschválenou pracovnou cestou v systéme Prítomnosť na pracovisku a vygeneruje mail vedúcej katedry, aby cestu schválila.</li>
            <strong>STAV CESTY: POTVRDENÁ</strong>
            <li>Vedúca katedry v systéme Prítomnosť na pracovisku schváli neptrítomnosť cestujúceho v dňoch/hodinách cesty. V prípade, že cestu neschváli, v systéme Cesty ju bude treba stornovať ručne (tým sa aj zmaže zo systému Prítomnosť).</li>
            <strong>STAV CESTY: POTVRDENÁ</strong>
            <li>Cestujúci po skončení cesty zadá údaje o ceste (deň po návrate mu príde upozornenie, aby to urobil) do systému Cesty - výdavky, dopravný prostriedok, môže upraviť aj dátumy a časy. Ak cestujúci nezadá správu do určitého termínu, je upozornený ešte raz, aby údaje zadal. Pani sekretárka je notifikovaná mailom o tom, že údaje boli zadané.</li>
            <strong>STAV CESTY: UKONČENÁ</strong>
            <li>Následne pani sekretárka vytlačí správu, vyúčtuje cestu v systéme SAP a potvrdí, že cestu vyúčtovala, čo vygeneruje mail cestujúcemu, aby prišiel na sekretariát podpísať celé vyúčtovanie cesty.</li>
            <strong>STAV CESTY: SPRACOVANÁ</strong>
            <li>Vo vynímočných prípadoch vie sekretárka ešte zmeniť údaje o skončenej ceste.</li>
        </ol>
          <p></p>
          <p>V stavoch <strong>NOVÁ</strong> alebo <strong>POTVRDENÁ</strong> môže cestujúci požiadať o stornovanie cesty (cesta prejde do stavu <strong>ŽIADOSŤ O STORNO</strong>). Pani sekretárka vie cestu stornovať v stave <strong>NOVÁ</strong> alebo na žiadosť cestujúceho, ktorú urobil v stave <strong>NOVÁ</strong> alebo <strong>POTVRDENÁ</strong>. Stornovaná cesta sa zmaže z Prítomnosti na pracovisku a v systéme cesty zostane evidovaná v stave <strong>STORNOVANÁ</strong>.</p>
    </x-content-box>

    @if($isAdmin)
    <x-content-box title="Inštrukcie pre Administrátora">
        <x-content-section title="Práca s ŠPP prvkami">
            <il>
                <li>Po kliknutí na tlačidlo "<strong>ŠPP prvky</strong>" sa vám zobrazí stránaka na prácu s ŠPP prvkami.</li>
                <li>Prvý formulár slúži na deaktiváciu existujúcich prvkov. Vyberiete prvok, ktorý chcete deaktivovať a stlačíte tlačidlo "<strong>Deaktivovať</strong>".</li>
                <li>Druhý formulár je na vytváranie nových prvkov a editáciu existujúcich. V ponuke "<strong>Výber ŠPP prvku</strong>" si zvolíte prvok, ktorý chcete editovať. Do formulára sa načítajú údaje tohto prvku a môžete ich editovať. Keď ste s úpravami spokojný/á, stlačíte tlačidlo "<strong>Uložiť zmeny</strong>".</li>
                <li>V prípade, že ste v ponuke "<strong>Výber ŠPP prvku</strong>" zvolili možnosť "<strong>Nový prvok</strong>" formulár ostane prázdny. Vyplňte ho a stačte "<strong>Pridať ŠPP prvok</strong>" na uloženie nového prvku.</li>
            </il>
        </x-content-section>
        <x-content-section title="Deaktivovanie používateľov">
            <il>
                <li>V časti "<strong>Prehľad</strong>" vidíte zoznam používateľov. V zátvorke vidíte, či máte zobrazených aktívných alebo neaktívnych používateľov.</li>
                <li>Medzi neaktívnymi a aktívnymi používateľmi sa prepínate klikaním na tlačidlo "<strong>Neaktívni používatelia</strong>"/"<strong>Aktívni používatelia</strong>" (Popis na tlačidle sa mení podľa toho, na ktorý typ používateľov vás tlačidlo prepne).</li>
                <li>Ak kliknete na meno niektorého <strong>aktívneho</strong> používateľa, pod zoznam používateľov sa zjaví tlačidlo "<strong>Deaktivovať používateľa MENO PRIEZVISKO</strong>".</li>
                <li>Po kliknutí na toto tlačidlo sa vám zjaví vyskakovacie okno, kde potvrdíte, že si zvoleného používateľa skutočne, želáte deaktivovať.</li>
                <li>Ak kliknete na meno niektorého <strong>neaktívneho</strong> používateľa, pod zoznamom používateľov sa zjaví tlačidlo "<strong>Aktivovať používateľa MENO PRIEZVISKO</strong>".</li>
                <li>Po kliknutí na toto tlačidlo sa vám zjaví vyskakovacie okno, kde potvrdíte, že si zvoleného používateľa skutočne, želáte aktivovať.</li>
            </il>
        </x-content-section>
    </x-content-box>
    @endif
</x-layout>
