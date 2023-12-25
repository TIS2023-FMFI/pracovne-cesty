<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SPPController extends Controller
{

    /**
     * Returning view with spp symbols form
     * Sending all active spp symbols
     */
    public function manage() {
        return view();
    }

    /**
     * Same as store() in BusinessTrip
     *Validating data
     * Saving new spp symbol in DB
     */
    public function store(Request $request) {

    }

    /**
     * Input parameter of type Request and the spp symbol
     * Updating the spp symbols state to deactivated
     */
    public function deactivate(Request $request, Spp $spp) {

    }
}
