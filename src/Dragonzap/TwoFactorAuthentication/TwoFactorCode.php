<?php

namespace Dragonzap\TwoFactorAuthentication;

use Auth;
use Dragonzap\TwoFactorAuthentication\Exceptions\NoUserException;
use Dragonzap\TwoFactorAuthentication\Notifications\TwoFactorCodeNotification;
class TwoFactorCode
{
    protected $code;
    protected $time;
    public function __construct($code, $time)
    {
        $this->code = $code;
        $this->time = $time;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function isExpired()
    {
        return $this->time->diffInMinutes(now()) >= 5;
    }
    public function isValid()
    {
        return !$this->isExpired();
    }
    public function send()
    {
        // Send the code to the user
       $user = Auth::user();
       if (!$user)
       {
            throw new NoUserException('No user logged in');
       }
       $user->notify(new TwoFactorCodeNotification($this));
    }

}

