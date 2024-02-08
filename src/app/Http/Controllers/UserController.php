<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\SimpleMail;
use App\Models\InvitationLink;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Http\Requests\UserRegistrationRequest;


class UserController extends Controller
{
    /**
     * Display the user registration form.
     *
     * @param string $email Email address of the user trying to register
     * @return View
     */
    public static function create(string $email): View
    {
        return view('users.register', ['email' => $email]);
    }

    /**
     * Process the data submitted from the registration form and create a new user.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(UserRegistrationRequest  $request): RedirectResponse
    {
        // Verify that submitted email is signed by a token
        $request->validate(['email' => 'required|string|email|max:127|unique:users']);
        $link = InvitationLink::where('email', $request->email)->first();

        if (!$link || !InvitationLink::isValid($link->token)) {
            return redirect()->back();
        }

        $validUserTypes = implode(',', [UserType::EXTERN->value, UserType::STUDENT->value]);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255',
            'user_types' => 'required|in:' . $validUserTypes
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $link->email,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        $user->user_type = UserType::from($request->user_types);
        $user->save();

        // Invalidate the token used for this registration
        $link->used = true;
        $link->save();

        return redirect()->route('homepage');
    }

    /**
     * Log the user out of the application.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('homepage');
    }

    /**
     * Authenticate the user.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();

        if ($user) {
            if (in_array($user->user_type, [UserType::EMPLOYEE, UserType::PHD_STUDENT], true)) {
                SynchronizationController::syncSingleUser($user->id);
            }
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('homepage');
            }
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Generate and send an invitation link to multiple external employees.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function invite(Request $request): RedirectResponse
    {
        $inputEmails = explode(';', $request->input('email'));
        $cleanedEmails = array_map('trim', $inputEmails);

        $existingEmails = array_merge(
            User::whereIn('email', $cleanedEmails)->pluck('email')->toArray(),
            InvitationLink::whereIn('email', $cleanedEmails)->pluck('email')->toArray()
        );

        $rejectedEmails = [];
        $invitedEmails = [];

        foreach ($cleanedEmails as $email) {
            if (in_array($email, $existingEmails, true)) {
                $rejectedEmails[] = $email;
                continue;
            }
            $token = bin2hex(random_bytes(20));
            $expiresAt = Carbon::now()->addDays(7);

            InvitationLink::create([
                'email' => $email,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            $url = route('user.register', ['token' => $token]);
            $messageText = "Pre registráciu kliknite na tento odkaz: " . $url;

            Mail::to($email)->send(new SimpleMail($messageText, $email, 'emails.registration_externist'));
            $invitedEmails[] = $email;
        }

        $message = "";

        if (!empty($invitedEmails)) {
            $invitedEmailsList = implode(', ', $invitedEmails);
            $message .= "Pozvánky boli úspešne odoslané na tieto adresy: $invitedEmailsList";
        }

        if (!empty($rejectedEmails)) {
            $rejectedEmailsList = implode(', ', $rejectedEmails);
            $message .= "\nPozvánka nebola odoslaná na tieto e-maily, pretože už sú v systéme: $rejectedEmailsList";
        }

        return back()->with('message', $message);
    }
}
