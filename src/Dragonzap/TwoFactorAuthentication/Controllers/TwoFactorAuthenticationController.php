<?php

namespace Dragonzap\TwoFactorAuthentication\Controllers;

class TwoFactorAuthenticationController
{
   public function twoFactorEnterCode()
   {
       return view('dragonzap_2factor::enter_code');
   }
}