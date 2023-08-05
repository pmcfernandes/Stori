<?php
defined('ABSPATH') or die('No direct script access allowed');

define('JWT_SECRET', 'GQDstcKsx0NHjPOuXOYg5MbeJ1XT0uFiwDVvVBrk');

class JWT
{
    /**
     * Create JWT token
     *
     * @param $str
     * @param $salt
     * @return string
     */
    public static function encode($data, $expires = 60 * 1000)
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
         
        $header = json_encode($header);
        $header = base64_encode($header);

        $issuedAt = time();
        $server_name = $_SERVER['HTTP_HOST'];

        $payload = array_merge([ 
            'iss' => $server_name,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            "exp" => $issuedAt + $expires,
        ], $data);
        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $signature = hash_hmac('sha256',"$header.$payload", JWT_SECRET, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    /**
     * Check if JWT is valid
     *
     * @param [type] $token
     * @param string $secret
     * @return boolean
     */
    public static function isValid($token) {
        $part = explode(".", $token);
        $header = $part[0];
        $payload = $part[1];
        $signature = $part[2];

        $valid = hash_hmac('sha256', "$header.$payload", JWT_SECRET, true);        
        $valid = base64_encode($valid);
        
        if ($signature == $valid) {
            return true;
        } else {
            return false;
        }        
    }

    /**
     * Decode JWT token
     *
     * @param $certPath
     * @return mixed
     */
    public static function decode($token)
    {
        if (JWT::isValid($token)) {
            $ts = time();
            $jwt = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+', explode('.', $token)[1]))));
            
            if ($ts >= $jwt->nbf && $ts <= $jwt->exp) {
                return $jwt;
            }
        }

        return array();
    }

    /**
     * Get JWT token from HTTP request
     *
     * @return void
     */
    public static function getAuthenticationBearerToken() {
        $headers = getallheaders();

        foreach ($headers as $name => $value) {
            if ('Authorization' == $name) {
                if ($value !== '') {
                    if (preg_match('/Bearer\s(\S+)/', $value, $matches)) {
                        return $matches[1];
                    }
                }
            }
        }

        return '';
    }
}
