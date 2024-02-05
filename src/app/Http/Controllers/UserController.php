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
use App\Models\InvitationLink;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SimpleMail;


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
        };

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $user->user_type = $userType;
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
            if (in_array($user->user_type, [UserType::EMPLOYEE, UserType::PHD_STUDENT])) {
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
     * Generate and send an invitation link to multiple external employees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invite(Request $request) {
        $emails = explode(';', $request->input('email'));

        foreach ($emails as $email) {
            $email = trim($email);
            $token = bin2hex(random_bytes(20));
            $expiresAt = Carbon::now()->addDays(7);

            InvitationLink::create([
                'email' => $email,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            $url = url('/register?token=' . $token);
            $messageText = "Pre registráciu kliknite na tento odkaz: " . $url;

            Mail::to($email)->send(new SimpleMail($messageText, $email, 'emails.registration_externist'));
        }
        return back()->with('success', 'Pozvánky boli úspešne odoslané.');
    }

}
