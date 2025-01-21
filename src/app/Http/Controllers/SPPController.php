<?php

namespace App\Http\Controllers;

use App\Models\SppSymbol;
use App\Enums\SppStatus;
use Illuminate\Http\Request;
use \Illuminate\Http\RedirectResponse;


class SPPController extends Controller
{

    /**
     * Returning view with spp symbols form
     * Sending all active spp symbols
     */
    public function manage() {
        return view('spp.manage', ['spp_symbols' =>
            SppSymbol::where('status', SppStatus::ACTIVE)
                ->pluck('spp_symbol', 'id'),
                'deactivated_symbols' =>
                SppSymbol::where('status', SppStatus::DEACTIVATED)
                    ->pluck('spp_symbol', 'id')]);
    }

    /**
     * Same as store() in BusinessTrip
     * Validating data
     * Saving new spp symbol in DB
     */
    public function store(Request $request): RedirectResponse
    {
        $customMessages = [
            'required' => 'Pole :attribute je povinné.',
            'string' => 'Pole :attribute musí byť reťazec.',
            'max' => 'Pole :attribute môže mať maximálne :max znakov.',
            'unique' => 'Pole :attribute už existuje.',
            'exists' => 'Vybrané pole :attribute neexistuje.',
        ];
        $customAttributes = [
            'spp_symbol' => 'symbol ŠPP',
            'functional_region' => 'funkčná oblasť',
            'account' => 'účet',
            'financial_centre' => 'finančné centrum',
            'grantee' => 'príjemca',
            'spp' => 'ŠPP prvok',
            'agency' => 'Agentúra',
            'acronym' => 'Akronym projektu',
        ];
        $validatedData = $request->validate([
            'spp_symbol' => 'required|string|max:30|unique:spp_symbols,spp_symbol',
            'functional_region' => 'required|string|max:10',
            'financial_centre' => 'required|string|max:10',
            'grantee' => 'required|string|max:100',
            'agency' => 'required|string|max:100',
            'acronym' => 'required|string|max:10',
            ],$customMessages, $customAttributes);

        SppSymbol::create($validatedData);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol pridaný.');

    }

    /**
     * Edit Existing SPP Symbol (Fill in Form with Details)
     */
    public function edit($id)
    {
        // Retrieve the selected SPP Symbol
        $sppSymbol = SppSymbol::findOrFail($id);

        return view('spp.manage', [
            'spp_symbols' => SppSymbol::where('status', SppStatus::ACTIVE)
                ->pluck('spp_symbol', 'id'),
            'deactivated_symbols' => SppSymbol::where('status', SppStatus::DEACTIVATED)
                            ->pluck('spp_symbol', 'id'),
            'editing_spp' => $sppSymbol // Pass selected SPP Symbol for editing
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $customMessages = [
            'required' => 'Pole :attribute je povinné.',
            'string' => 'Pole :attribute musí byť reťazec.',
            'max' => 'Pole :attribute môže mať maximálne :max znakov.',
            'unique' => 'Pole :attribute už existuje.',
        ];
        $customAttributes = [
            'spp_symbol' => 'symbol ŠPP',
            'functional_region' => 'funkčná oblasť',
            'financial_centre' => 'finančné centrum',
            'grantee' => 'príjemca',
            'agency' => 'Agentúra',
            'acronym' => 'Akronym projektu',
        ];

        $validatedData = $request->validate([
            'spp_symbol' => 'required|string|max:30|unique:spp_symbols,spp_symbol,' . $id,
            'functional_region' => 'required|string|max:10',
            'financial_centre' => 'required|string|max:10',
            'grantee' => 'required|string|max:100',
            'agency' => 'required|string|max:100',
            'acronym' => 'required|string|max:10',
        ], $customMessages, $customAttributes);

        // Retrieve and update the SPP symbol
        $sppSymbol = SppSymbol::findOrFail($id);
        $sppSymbol->update($validatedData);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol úspešne aktualizovaný.');
    }



    /**
     * Input parameter of type Request and the spp symbol
     * Updating the spp symbols state to deactivated
     */
    public function deactivate(Request $request): RedirectResponse
    {
        $validatedData = $request->validate(['spp' => 'required|exists:spp_symbols,id']);
        SppSymbol::find($validatedData['spp'])->update(['status' => SppStatus::DEACTIVATED]);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol deaktivovaný.');
    }

    /**
     * Input parameter of type Request and the spp symbol
     * Updating the spp symbols state to activated
     */
    public function activate(Request $request): RedirectResponse
    {
        $validatedData = $request->validate(['spp' => 'required|exists:spp_symbols,id']);
        SppSymbol::find($validatedData['spp'])->update(['status' => SppStatus::ACTIVE]);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol aktivovaný.');
    }
}
