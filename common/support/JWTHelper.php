<?php
/**
 * Created by PhpStorm.
 * User: zhongyb
 * Date: 2016/7/12
 * Time: 15:41
 */

namespace common\support;


class JWTHelper
{

    /**
     * When checking nbf, iat or expiration times,
     * we want to provide some extra leeway time to
     * account for clock skew.
     */
    public static $leeway = 0;

    /**
     * Allow the current timestamp to be specified.
     * Useful for fixing a value within unit testing.
     *
     * Will default to PHP time() value if null.
     */
    public static $timestamp = null;

    public static $supported_algs = array(
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'RS256' => array('openssl', 'SHA256'),
    );

    /**
     * Converts and signs a PHP object or array into a JWT string.
     *
     * @param object|array  $payload    PHP object or array
     * @param string        $secret        The secret key.
     *                                  If the algorithm used is asymmetric, this is the private key
     * @param string        $alg        The signing algorithm.
     *                                  Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     *
     * @return string A signed JWT
     *
     * @uses jsonEncode
     * @uses urlsafeB64Encode
     */
    public static function encode($payload, $secret, $alg = 'HS256')
    {
        $header = array('typ' => 'JWT', 'alg' => $alg);

        $segments = array();
        $segments[] = static::urlsafeB64Encode(json_encode($header));
        $segments[] = static::urlsafeB64Encode(json_encode($payload));
        $signing_input = implode('.', $segments);

        $signature = static::sign($signing_input, $secret, $alg);
        $segments[] = static::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    public static function sign($msg, $secret, $alg = 'HS256')
    {
        if (empty(static::$supported_algs[$alg])) {
            return false;
        }
        list($function, $algorithm) = static::$supported_algs[$alg];
        switch($function) {
            case 'hash_hmac':
                return hash_hmac($algorithm, $msg, $secret, true);
            case 'openssl':
                $signature = '';
                $success = openssl_sign($msg, $signature, $secret, $algorithm);
                if (!$success) {
                    return false;
                } else {
                    return $signature;
                }
        }

    }

    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}