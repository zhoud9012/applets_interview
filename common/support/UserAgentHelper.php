<?php
/**
 解析浏览器user-agent信息
 */

namespace common\support;

class UserAgentHelper
{

    private static $brower=array(

        'MSIE' => ['pattern'=>'/MSIE\s+([0-9\.]+)/','brower'=>'Internet Explorer'],
        'Firefox' => ['pattern'=>'/Firefox\/([0-9\.]+)/','brower'=>'Firefox'],
        'QQBrowser' => ['pattern'=>'/QQBrowser/','brower'=>'QQBrowser'],
        'QQ' => ['pattern'=>'/QQ\/([0-9.]+)/','brower'=>'QQBrowser'],
        'UCBrowser' => ['pattern'=>'/UCBrowser/','brower'=>'UCBrowser'],
        'MicroMessage' => ['pattern'=>'/MicroMessage/','brower'=>'MicroMessage'],
        'Edge' => ['pattern'=>'/Edge\/([0-9\.]+)/','brower'=>'Edge'],
        'Chrome' =>  ['pattern'=>'/Chrome\/([0-9.]+)/','brower'=>'Chrome'],
        'Opera' => ['pattern'=>'/Opera\/([0-9\.]+)/','brower'=>'Opera'],
        'OPR' => ['pattern'=>'/OPR\/([0-9\.]+)/','brower'=>'Opera'],
        'Safari' => ['pattern'=>'/Safari\/([0-9\.]+)/','brower'=>'Safari'],
        '360SE' => ['pattern'=>'/360SE/','brower'=>'360SE'],
        'Maxthon' => ['pattern'=>'/Maxthon\/([0-9\.]+)/','brower'=>'Maxthon']

    );

    /**
     * 浏览器user-agent解析
     *
     * @param  string $userAgent
     * @param
     * @return array
     */
    public static function parse($userAgent)
    {
        $info=['webBrower'=>''];
        foreach (self::$brower as $val) {
            $match=[];
            $ret=preg_match($val['pattern'],$userAgent,$match);
            if($ret){
                $info['webBrower']=$val['brower'].(isset($match[1])?' '.$match[1]:'');
                break;
            }
        }
        if($info['webBrower']==''){
            $info['webBrower']='Unknown';
        }
        $info['os']=self::_getOs($userAgent);
        return $info;
    }
    private static function _getOs($agent)
    {

        if (preg_match('/iPad\s+/i', $agent)) {
            $os = 'iPad';
        }else if(preg_match('/iPod\s+/i', $agent)) {
            $os = 'iPod';
        }else if(preg_match('/iPhone\s+/i', $agent)) {
            $os = 'IOS';
        }else if(preg_match('/Mac\s+/i', $agent)) {
            $os = 'macintosh';
        }else if(preg_match('/android\s+/i', $agent)) {
            $os= 'Android';
        }else if (preg_match('/windows\s+nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        }else if (preg_match('/windows\s+nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        }else if(preg_match('/windows\s+nt 10.0/i', $agent)) {
            $os = 'Windows 10';
        }else if (preg_match('/windows\s+nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        }else if (preg_match('/windows\s+nt 5/i', $agent)) {
            $os = 'Windows 2000';
        }else if (preg_match('/windows\s+/i', $agent)){
            $os = 'Windows';
        }else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        }else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        }else if (preg_match('/SunOs/i', $agent)) {
            $os = 'SunOS';
        }else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        }else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
            $os = 'Macintosh';
        }else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        }else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        }else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        }else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        }else if(preg_match('/OpenBSD/i', $agent)) {
            $os='OpenBSD';
        }else {
            $os = 'Unknown';
        }
        return $os;
    }
}
