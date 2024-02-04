<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SynchronizationController;

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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:127|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255',
            'user_types' => ['required', 'string', Rule::in(['externist', 'student'])],
        ]);

        if ($validator->fails()) {
            return redirect('register')
                ->withErrors($validator)
                ->withInput();
        }

        $userType = match($request->user_types) {
            'externist' => UserType::EXTERN,
            'student' => UserType::STUDENT,
            default => throw new \Exception("Invalid user type"),
        };

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $user->user_type = $userType->value;
        $user->save();

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
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */


    public function authenticate(Request $request) {
        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();

        if ($user) {
            if (in_array($user->user_type, [UserType::EMPLOYEE->value, UserType::PHD_STUDENT->value])) {
                SynchronizationController::syncSingleUser($user->id);
            }
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }
        }
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }



    /**
     * Invitation for external employee
     * @return \Illuminate\View\View
     */
    public function invite() {
        return view('components.modals.add-user');
    }
}
