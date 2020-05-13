<?php

namespace IgniterLabs\SmsNotify\Classes;

use Igniter\Flame\Traits\Singleton;
use IgniterLabs\SmsNotify\Exceptions\OTPException;
use Illuminate\Support\Facades\Request;
use Main\Facades\Auth;
use System\Traits\SessionMaker;

class OTPManager
{
    use Singleton;
    use SessionMaker;

    protected $sessionKey = 'igniterlabs.smsnotify.otpverify';

    public function beforeUserAuthenticate($credentials)
    {
        $user = Auth::getByCredentials($credentials);
//        if (!$user = Auth::getByCredentials($credentials))
//            throw new ApplicationException(lang('igniter.user::default.login.alert_invalid_login'));

        $codeSent = FALSE;
        if ($this->shouldSendCode($user))
            $codeSent = $this->sendCode($user->telephone);

        if ($this->verifyCode())
            return;

        throw OTPException::create($user, $codeSent);
    }

    protected function shouldSendCode($user)
    {
        if (!strlen($user->telephone))
            return FALSE;

        if ($this->hasSession('code'))
            return FALSE;

        return TRUE;
    }

    protected function sendCode($telephone)
    {
    }

    protected function verifyCode()
    {
        if (!$this->hasSession('code'))
            return FALSE;

        $verifyCode = Request::input('code');
        if (!strlen($verifyCode))
            return FALSE;
    }
}