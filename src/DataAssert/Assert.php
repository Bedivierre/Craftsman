<?php


namespace Bedivierre\Craftsman\Appraise;


class Assert
{

    public static function isValidEmail(string $email){
        $v = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $v;
    }
    public static function isValidUrl(string $url){
        $v = filter_var($url, FILTER_VALIDATE_URL);
        return $v;
    }
    public static function isValidDomain(string $domain)
    {
        $v = filter_var($domain, FILTER_VALIDATE_DOMAIN);
        return $v;
    }
    public static function isValidIp(string $ip)
    {
        $v = filter_var($ip, FILTER_VALIDATE_IP);
        return $v;
    }
    public static function isValidRuPhone(string $phone)
    {
        $ph = preg_replace('~\D~', '', $phone);
        $m = [];
        if(preg_match('/^(?:(?:\+?7|8)?(\d{10}))$/', $ph, $m)) {
            return $m[1];
        }
        return false;
    }

}