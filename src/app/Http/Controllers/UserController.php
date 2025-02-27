<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\SimpleMail;
use App\Models\InvitationLink;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Str;


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
    public function store(Request $request): RedirectResponse
    {
        // Verify that submitted email is signed by a token
        $request->validate(['email' => 'required|string|email|max:127|unique:users']);
        $link = InvitationLink::where('email', $request->email)->first();

        if (!$link || !InvitationLink::isValid($link->token)) {
            return redirect()->back();
        }
        $customMessages = [
            'required' => 'Pole :attribute je povinné.',
            'string' => 'Pole :attribute musí byť reťazec.',
            'max' => 'Pole :attribute môže mať maximálne :max znakov.',
            'email' => 'Pole :attribute musí byť platná e-mailová adresa.',
            'unique' => 'Pole :attribute už bolo zaregistrované.',
            'confirmed' => 'Potvrdenie :attribute sa nezhoduje.',
            'min' => 'Pole :attribute musí obsahovať aspoň :min znakov.',
        ];
        $customAttributes = [
            'first_name' => 'meno',
            'last_name' => 'priezvisko',
            'email' => 'e-mail',
            'password' => 'heslo',
            'username' => 'prihlasovacie meno',
        ];

        $validUserTypes = implode(',', [UserType::EXTERN->value, UserType::STUDENT->value]);
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'username' => 'required|string|max:255|unique:users,username|unique:\App\Models\PritomnostUser,username',
                'password' => 'required|string|max:255',
                'user_types' => 'required|in:' . $validUserTypes
            ],
            $customMessages,
            $customAttributes
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'personal_id' => $request->user_types == UserType::STUDENT->value ? '10790002' : '10790004',
            'email' => $link->email,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        $user->user_type = UserType::from($request->user_types);
        $user->assignRole('traveller');

        $user->save();

        // Invalidate the token used for this registration
        $link->used = true;
        $link->save();

        return redirect()->route('homepage')->with('message', 'Vaša registrácia prebehla úspešne.');
    }

    /**
     * Log the user out of the application.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('homepage')->with('message', 'Boli ste úspešne odhlásená/ný.');
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
        $synced = SynchronizationController::syncSingleUser($credentials['username']);
        $user = User::where('username', $credentials['username'])
                ->where('status', 1)->first();

        if ($user && Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('homepage')->with('message', 'Boli ste úspešne prihlásená/ný.');
        }

        return back()->with('message', 'Zadané meno alebo heslo nie sú správne, alebo je váš účet deaktivovaný.');
    }

    /** Translate URL from local to one that can be accessed from Internet, example:
     *    http://localhost:8097/some/link/to/click -> https://kempelen.dai.fmph.uniba.sk/cesty/some/link/to/click
     *
     * @param a URL to be translated
     * @Return a translated URL
     */
    private function htmlReverseProxy($url)
    {
        return str_replace("http://localhost:8097", "https://kempelen.dai.fmph.uniba.sk/cesty", $url);
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

        $validEmails = [];
        $rejectedEmails = [];

        foreach ($cleanedEmails as $email) {
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'required|string|email|max:127']
            );

            if ($validator->fails()) {
                $rejectedEmails[] = $email;
                continue;
            }

            $validEmails[] = $email;
        }

        $existingEmails = array_merge(
            User::whereIn('email', $validEmails)->pluck('email')->toArray(),
            InvitationLink::whereIn('email', $validEmails)->pluck('email')->toArray()
        );

        $invitedEmails = [];

        foreach ($validEmails as $email) {
            if (in_array($email, $existingEmails, true)) {
                $rejectedEmails[] = $email;
                continue;
            }

            $token = bin2hex(random_bytes(10));
            $expiresAt = Carbon::now()->addDays(7);

            InvitationLink::create([
                'email' => $email,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            $url = route('user.register', ['token' => $token]);
	    $url = $this->htmlReverseProxy($url);
            $messageText = "Pre registráciu kliknite na tento odkaz: " . $url;

            Mail::to($email)->send(new SimpleMail($messageText, $email, 'emails.registration_externist', 'Pracovné cesty - registrácia'));
            $invitedEmails[] = $email;
        }

        $message = "";

        if (!empty($invitedEmails)) {
            $invitedEmailsList = implode(', ', $invitedEmails);
            $message .= "Pozvánky boli úspešne odoslané na tieto adresy: $invitedEmailsList";
        }

        if (!empty($rejectedEmails)) {
            $rejectedEmailsList = implode(', ', $rejectedEmails);
            $message .= "\nPozvánka nebola odoslaná na tieto e-maily,
                         pretože už sú v systéme alebo nemajú správny formát: $rejectedEmailsList";
        }

        return back()->with('message', $message);
    }

    /**
     * Process password reset request and send reset link
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|string|email|max:127']);

        $user = User::where('email', $request->input('email'))->first();
        if ($user && !$user->user_type->isExternal()) {
            return redirect()->route('homepage')
                ->with('warning', 'Vaše heslo nemôže byť zmenené v Pracovných cestách kvôli vášmu existujúcemu účtu v Prítomnosti.');
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Reset password for the given user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email|max:127',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            static function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('homepage')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function activateUser(Request $request): RedirectResponse
    {
        $userId = $request['user'];
        $user = $userId ? User::find($userId) : null;
        if($user == null){
            $message = "Vybraný používteľ neexistuje.";
        }else{
            if(User::activateUserWithId($userId)){
                $message = "Používateľ bol úspešne aktivovaný.";
            }else{
                $message = "Používateľa sa nepodarilo aktivovať.";
            }
        }
        return redirect()->route('homepage',['inactive'=>$request['inactive'], 'sort'=>$request['sort']])->with('message',$message);
    }

    public function deactivateUser(Request $request): RedirectResponse
        {
            $currentUserId = Auth::user()->id;
            $userId = $request['user'];
            $user = $userId ? User::find($userId) : null;
            if($user == null){
                $message = "Vybraný používteľ neexistuje.";
            }else if($userId == $currentUserId){
                $message = "Nemôžete deaktivovať svoj vlastný účet.";
            }else{
                if(User::deactivateUserWithId($userId)){
                    $message = "Používateľ bol úspešne deaktivovaný.";
                }else{
                    $message = "Používateľa sa nepodarilo deaktivovať.";
                }
            }
            return redirect()->route('homepage',['inactive'=>$request['inactive'], 'sort'=>$request['sort']])->with('message',$message);
        }
}
