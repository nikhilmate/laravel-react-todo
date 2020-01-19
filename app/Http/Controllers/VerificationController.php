<?php

// namespace App\Http\Controllers\Auth;
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            // throw new AuthorizationException;
            return response()->json([
                    "status" => "400",
                    "error" => 'Invalid Details',
                ], 400);
        }
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                    "status" => "400",
                    "error" => 'Invalid Details',
                ], 400);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                    "status" => "400",
                    "success" => 'Already Verified',
                ], 400);
        }
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return response()->json([
                    "status" => "200",
                    "success" => 'Verified',
                ], 200);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        try {
            $user = Auth::user();
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "status" => "500",
                    "error" => $th
                ], 500);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                    "status" => "400",
                    "success" => 'Already Verified',
                ], 400);
        }
        $user->sendEmailVerificationNotification();
        return response()->json([
                    "status" => "200",
                    "success" => 'Resent',
                ], 200);
    }
}
