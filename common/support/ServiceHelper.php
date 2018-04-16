<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigSetting
 *
 * @author zhangxc
 */
namespace common\support;

use common\entities\common\MyscrmOrganizationEntity;
use common\entities\common\PigcmsWxUserEntity;
use common\repositories\common\MyscrmOrganizationRepository;
use common\repositories\config\ConfigSettingRepository;
use Exception;

class ServiceHelper {
    public static function getConfigByKey($key)
    {
        $repository = new ConfigSettingRepository();
        $config = $repository->getByKey($key);
        if (empty($config)) {
            throw new Exception('找不到配置项：' . $key);
        }
        $config = $config->toArray();
        return $config['Value'];
    }

    public static function getAppInfoByToken($token){
        $repository = new MyscrmOrganizationRepository();

        $wxuser = $repository->getAppInfoByToken($token);
        if(empty($wxuser)){
            throw new Exception('找不到公众号：'.$token);
        }

        return array('appid'=>$wxuser->appid, 'appsecret'=>$wxuser->appsecret,'appname'=>$wxuser->wxname,'organizationId'=>$wxuser->OrganizationId);
    }

    /**
     * 根据公众号token查找orgid与orgname
     * @param type $token
     * @return type
     */
    public static function getOrgIdAndName($token){
        $org_id = "";
        $org_name = "";
        $friendly_name = "";
        $user = PigcmsWxUserEntity::findOne(
            ["token" => $token]
        );
        if($user){
            $org_id = $user->OrganizationId;
            $org = MyscrmOrganizationEntity::findOne(
                ["Id" => $org_id]
            );
            if($org){
                $org_name = $org->UniqueName;
                $friendly_name = $org->FriendlyName;
            }
        }
        return array($org_id, $org_name, $friendly_name);
    }
}
