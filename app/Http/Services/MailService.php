<?php
namespace App\Http\Services;

use App\Mail\RegisterConfirm;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendRegisterConfirmation($user, $token)
    {
        $tokenUrl = config('app.CLIENT_URL') . '/confirm?token=' . $token;
        $expireHours = config('app.expire_register_token_hours', 24);

        Mail::to($user->email)->queue(new RegisterConfirm($user, $tokenUrl, $expireHours));
    }

    public function sendPasswordReset($user, $token)
    {
        $tokenUrl = config('app.CLIENT_URL') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        $expireHours = config('app.expire_reset_token_hours', 1);

        Mail::to($user->email)->queue(new ResetPassword($user, $tokenUrl, $expireHours));
    }
    
}