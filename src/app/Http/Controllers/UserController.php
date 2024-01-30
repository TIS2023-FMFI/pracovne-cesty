<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the user registration form.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('users.register');
    }

    /**
     * Process the data submitted from the registration form and create a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:127|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255',
            'user_types' => 'required|string',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'user_type' => $validatedData['user_types'],
        ]);
        return redirect()->route('login');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\View\View
     */
    public function logout() {
        Auth::logout();
        return view('homepage');
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function login() {
        return view('dashboard');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back();
    }


    /**
     * Invitation for external employee
     * @return \Illuminate\View\View
     */
    public function invite() {
        return view('components.modals.add-user');
    }
}
