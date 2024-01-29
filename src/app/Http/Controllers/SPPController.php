<?php

namespace App\Http\Controllers;

use App\Models\SppSymbol;
use App\Enums\SppStatus;
use Illuminate\Http\Request;

class SPPController extends Controller
{

    /**
     * Returning view with spp symbols form
     * Sending all active spp symbols
     */
    public function manage() {
        return view('spp.manage', ['spp_symbols' => SppSymbol::where('status', SppStatus::ACTIVE)->pluck('spp_symbol', 'id')]);
    }

    /**
     * Same as store() in BusinessTrip
     * Validating data
     * Saving new spp symbol in DB
     */
    public function store(Request $request) {
        $validatedData = $request->validate([
            'fund' => 'required|string',
            'spp_symbol' => 'required|string|unique:spp_symbols,spp_symbol',
            'functional_region' => 'required|string',
            'account' => 'required|string',
            'financial_centre' => 'required|string',
            'grantee' => 'required|string|max:200',
            ]);

        SppSymbol::create($validatedData);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol pridaný.');

    }

    /**
     * Input parameter of type Request and the spp symbol
     * Updating the spp symbols state to deactivated
     */
//    public function deactivate(Request $request, SppSymbol $spp) {
    public function deactivate(Request $request) {
        $validatedData = $request->validate(['spp' => 'exists:spp_symbols,id']);
        SppSymbol::find($validatedData['spp'])->update(['status' => SppStatus::DEACTIVATED]);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol deaktivovaný.');
    }
}
