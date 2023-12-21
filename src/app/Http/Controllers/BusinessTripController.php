<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusinessTripController extends Controller
{
    
    //Returning view with details from all trips
    public function index() {
        return view();
    }
    
    //Returning view with the trip details
    public function show(BusinessTrip $b) {
        return view();
    }
    
    //Returning view with the form for addding of the new trip
    public function create() {
        return view();
    }
    
    /**
     * Parsing data from the $request in form 
     * Also managing uploaded files from the form
     * Redirecting to the homepage
     * Sending mail with mail component to admin
     */
    public function store(Request $request) {
        
    }
    
    //Returning the view with the trip editing form
    public function edit(BusinessTrip $b) {
        return view();
    }
    
    /**
     * Validation as in create(), should check if user is admin/not
     * Redirecting to the trip editing form
     * Sending mail with mail component to admin 
     */
    public function update(Request $request, BusinessTrip $b) {
        
    }
    
    //Next 3 functions ccould be combined to changeState(Request $request, BuisenessTrip $b)
   
    
    //Updating state of the trip to cancelled
    //Adding cancellation reason 
    public function cancel(BusinessTrip $b) {
        
    }
    
    //Same as update(), updating state of the trip to confirmed 
    public function confirm() {
        
    }
    
    //Same as update, updating state to closed
    public function close() {
        
    }
}
