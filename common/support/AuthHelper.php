<?php
/**
 * http访问校验签名
 * User: silenceper
 * Date: 16/7/28
 * Time: 下午5:32
 */

namespace common\support;


class AuthHelper
{
    const SLAT = "zZ84a13eYIXSfz1NEk9zhwGk5kRJ7HaP";

    /**
     * @param array $params
     * @return mixed
     */
    public static function genSign(array $params)
    {
        $newParams = [];
        foreach ($params as $key => $val) {
            if ($key == "sign") {
                continue;
            }
            $newParams[$key] = urlencode($val);
        }
        ksort($newParams);
        return md5(http_build_query($newParams) . self::SLAT);
    }

    /**
     * 使用secret生成sign
     * @param array $params
     * @param $secret
     * @return mixed
     */
    public static function genSignWithSecret(array $params, $secret)
    {
        $newParams = [];
        foreach ($params as $key => $val) {
            if ($key == "sign") {
                continue;
            }
            $newParams[$key] = urlencode($val);
        }
        ksort($newParams);
        return md5(http_build_query($newParams) . $secret);
    }
}