<?php

namespace Dragonzap\TwoFactorAuthentication\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OTPHP\TOTP;

class TwoFactorTotp extends Model
{
    use HasFactory;
    protected $table = 'dragonzap_twofactor_totp';

    // Has many users
    public function user()
    {
        $user_class = config('auth.providers.users.model');
        return $this->belongsTo($user_class, 'user_id');
    }

    public function scopeForUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeConfirmedOnly($query)
    {
        return $query->where('confirmed', true);
    }


    public function getQrCodeUrl()
    {
        $otp = TOTP::createFromSecret($this->secret_key);
        $otp->setLabel(config('dragonzap_2factor.totp.issuer'));
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );
        return $grCodeUri;
    }

    public function verify($code)
    {
        $totp = TOTP::createFromSecret($this->secret_key);
        if ($totp->verify($code))
        {
            $this->last_used = now();
            $this->confirmed = true;
            $this->save();
            return true;
        }

        return false;
    }

    public static function generateTotp($user, $friendly_name = NULL)
    {
        if (!$friendly_name)
        {
            $friendly_name = 'Authenticator ' . date('Y-m-d');
        }
        $secret_key = TOTP::generate()->getSecret();
        $two_factor_totp = new TwoFactorTotp();
        $two_factor_totp->user_id = $user->id;
        $two_factor_totp->secret_key = $secret_key;
        $two_factor_totp->friendly_name = $friendly_name;
        $two_factor_totp->save();
        return $two_factor_totp;
    }
}
