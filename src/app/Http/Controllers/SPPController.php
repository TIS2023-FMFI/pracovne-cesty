<?php

namespace App\Http\Controllers;

use App\Models\SppSymbol;
use App\Enums\SppStatus;
use Illuminate\Http\Request;
use \Illuminate\Http\RedirectResponse;
use App\Http\Requests\SppSymbolRequest;


class SPPController extends Controller
{

    /**
     * Returning view with spp symbols form
     * Sending all active spp symbols
     */
    public function manage() {
        return view('spp.manage', ['spp_symbols' =>
            SppSymbol::where('status', SppStatus::ACTIVE)
                ->pluck('spp_symbol', 'id')]);
    }

    /**
     * Same as store() in BusinessTrip
     * Validating data
     * Saving new spp symbol in DB
     */
    public function store(SppSymbolRequest $request): RedirectResponse
    {
        $validatedData = $request->validate();

        SppSymbol::create($validatedData);

        return redirect()->route('spp.manage')->with('message', 'ŠPP prvok bol pridaný.');

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
}
