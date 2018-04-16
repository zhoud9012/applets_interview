<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 15-3-26
 * Time: 15:00
 */

namespace common\support;

/**
 * memcached的key和到期时间都用常量集中定义，前缀做个约定，key用mk_做前缀，到期
 * 时间用mt_做前缀
 */
use common\entities\KfsDbEntity;
use Yii;


class CacheManage
{
    private static function getKsfadminCurrentUnit(){
        // 临时解决未登陆的问题
        if(!is_null(KfsDbEntity::$orgcode)){
            return KfsDbEntity::$orgcode;
        }
        return Yii::$app->user->identity->unitname;
    }
//    const MK_AGENCY_DETAIL = 'mk_agency_detail_';
//
//    private static function getAgencyDetailKey($agencyId)
//    {
//        return self::MK_AGENCY_DETAIL.$agencyId;
//    }
//
//    public static function getAgencyDetail($agencyId)
//    {
//        return Yii::$app->cache->get(self::getAgencyDetailKey($agencyId));
//    }
//
//    public static function setAgencyDetail($agencyId, $agencyDetail)
//    {
//        return Yii::$app->cache->get(self::getAgencyDetailKey($agencyId), $agencyDetail);
//    }

    /*单个楼书的缓存 开始*/
    const MK_WLS_LOUSHU = 'mk_wls_loushu_';

    private static function getLouShuKey($token,$loushuId){
        return self::MK_WLS_LOUSHU.$token."_".$loushuId;
    }

    public static function getLouShuCache($token,$loushuId){
        return Yii::$app->cache->get(self::getLouShuKey($token,$loushuId));
    }

    public static function setLouShuCache($token,$loushuId,$data){
        return Yii::$app->cache->set(self::getLouShuKey($token,$loushuId),$data);
    }

    public static function deleteLouShuCache($token,$loushuId){
        return Yii::$app->cache->delete(self::getLouShuKey($token,$loushuId));
    }
    /*单个楼书的缓存 结束*/

    /*楼书列表的缓存 开始*/
    const MK_WLS_LOUSHU_LIST = 'mk_wls_loushu-list_';

    private static function getLouShuListKey($token){
        return self::MK_WLS_LOUSHU_LIST.$token;
    }

    public static function getLouShuListCache($token){
        return Yii::$app->cache->get(self::getLouShuListKey($token));
    }

    public static function setLouShuListCache($token,$data){
        return Yii::$app->cache->set(self::getLouShuListKey($token),$data);
    }

    public static function deleteLouShuListCache($token){
        return Yii::$app->cache->delete(self::getLouShuListKey($token));
    }
    /*楼书列表的缓存 结束*/

    /*所有楼盘数据(只包括名称和ID)的缓存 开始*/
    const MK_WLS_LITE_BUILDING_LIST = 'mk_wls_lite-building-list_';
    private static function getLiteBuildingListKey($token){
        return self::MK_WLS_LITE_BUILDING_LIST.$token;
    }

    public static function getLiteBuildingList($token){
        return Yii::$app->cache->get(self::getLiteBuildingListKey($token));
    }

    public static function setLiteBuildingList($token,$data){
        return Yii::$app->cache->set(self::getLiteBuildingListKey($token),$data);
    }

    public static function deleteLiteBuildingList($token){
        return Yii::$app->cache->delete(self::getLiteBuildingListKey($token));
    }
    /*所有楼盘数据(只包括名称和ID)的缓存 结束*/

    /*所有活动数据(只包括名称和ID)的缓存 开始*/
    const MK_WLS_LITE_PARTY_LIST = 'mk_wls_lite-party-list_';
    private static function getLitePartyListKey($token){
        return self::MK_WLS_LITE_PARTY_LIST.$token;
    }

    public static function getLitePartyList($token){
        return Yii::$app->cache->get(self::getLitePartyListKey($token));
    }

    public static function setLitePartyList($token,$data){
        return Yii::$app->cache->set(self::getLitePartyListKey($token),$data);
    }

    public static function deleteLitePartyList($token){
        return Yii::$app->cache->delete(self::getLitePartyListKey($token));
    }
    /*所有活动数据(只包括名称和ID)的缓存 结束*/

    /*单个楼盘数据的缓存 开始*/
    const MK_WLS_BUILDING = 'mk_wls_building_';
    private static function getBuildingKey($token,$buildingId){
        return self::MK_WLS_BUILDING.$token.'_'.$buildingId;
    }

    public static function getBuilding($token,$buildingId){
        return Yii::$app->cache->get(self::getBuildingKey($token,$buildingId));
    }

    public static function setBuilding($token,$buildingId,$data){
        return Yii::$app->cache->set(self::getBuildingKey($token,$buildingId),$data);
    }

    public static function deleteBuilding($token,$buildingId){
        return Yii::$app->cache->delete(self::getBuildingKey($token,$buildingId));
    }
    /*单个楼盘数据的缓存 结束*/

    /*楼盘列表数据的缓存 开始*/
    const MK_WLS_BUILDING_LIST = 'mk_wls_building-list_';
    private static function getBuildingListKey($token,$pageIndex,$pageSize){
        return self::MK_WLS_BUILDING_LIST.$token.'_'.$pageIndex.'_'.$pageSize;
    }

    public static function getBuildingList($token,$pageIndex,$pageSize){
        return Yii::$app->cache->get(self::getBuildingListKey($token,$pageIndex,$pageSize));
    }

    public static function setBuildingList($token,$pageIndex,$pageSize,$data){
        return Yii::$app->cache->set(self::getBuildingListKey($token,$pageIndex,$pageSize),$data);
    }

    public static function deleteBuildingList($token,$pageIndex,$pageSize){
        return Yii::$app->cache->delete(self::getBuildingListKey($token,$pageIndex,$pageSize));
    }
    /*楼盘列表数据的缓存 结束*/

    /*单个户型数据的缓存 开始*/
    const MK_WLS_HOUSETYPE = 'mk_wls_housetype_';
    private static function getHouseTypeKey($token,$houseTypeId){
        return self::MK_WLS_HOUSETYPE.$token.'_'.$houseTypeId;
    }

    public static function getHouseType($token,$houseTypeId){
        return Yii::$app->cache->get(self::getHouseTypeKey($token,$houseTypeId));
    }

    public static function setHouseType($token,$houseTypeId,$data){
        return Yii::$app->cache->set(self::getHouseTypeKey($token,$houseTypeId),$data);
    }

    public static function deleteHouseType($token,$houseTypeId){
        return Yii::$app->cache->delete(self::getHouseTypeKey($token,$houseTypeId));
    }
    /*单个户型数据的缓存 结束*/

    /*户型列表数据的缓存 开始*/
    const MK_WLS_HOUSETYPE_LIST = 'mk_wls_housetype-list_';
    private static function getHouseTypeListKey($token,$buildingId){
        return self::MK_WLS_HOUSETYPE_LIST.$token.'_'.$buildingId;
    }

    public static function getHouseTypeList($token,$buildingId){
        return Yii::$app->cache->get(self::getHouseTypeListKey($token,$buildingId));
    }

    public static function setHouseTypeList($token,$buildingId,$data){
        return Yii::$app->cache->set(self::getHouseTypeListKey($token,$buildingId),$data);
    }

    public static function deleteHouseTypeList($token,$buildingId){
        return Yii::$app->cache->delete(self::getHouseTypeListKey($token,$buildingId));
    }
    /*户型列表数据的缓存 结束*/

    /*单个活动数据的缓存 开始*/
    const MK_WLS_PARTY = 'mk_wls_party_';
    private static function getPartyKey($token,$partyId){
        return self::MK_WLS_PARTY.$token.'_'.$partyId;
    }

    public static function getParty($token,$partyId){
        return Yii::$app->cache->get(self::getPartyKey($token,$partyId));
    }

    public static function setParty($token,$partyId,$data){
        return Yii::$app->cache->set(self::getPartyKey($token,$partyId),$data);
    }

    public static function deleteParty($token,$partyId){
        return Yii::$app->cache->delete(self::getPartyKey($token,$partyId));
    }
    /*单个活动数据的缓存 结束*/

    /*活动列表数据的缓存 开始*/
    const MK_WLS_PARTY_LIST = 'mk_wls_party-list_';
    private static function getPartyListKey($token,$pageIndex,$pageSize){
        return self::MK_WLS_PARTY_LIST.$token.'_'.$pageIndex.'_'.$pageSize;
    }

    public static function getPartyList($token,$pageIndex,$pageSize){
        return Yii::$app->cache->get(self::getPartyListKey($token,$pageIndex,$pageSize));
    }

    public static function setPartyList($token,$pageIndex,$pageSize,$data){
        return Yii::$app->cache->set(self::getPartyListKey($token,$pageIndex,$pageSize),$data);
    }

    public static function deletePartyList($token,$pageIndex,$pageSize){
        return Yii::$app->cache->delete(self::getPartyListKey($token,$pageIndex,$pageSize));
    }
    /*活动列表数据的缓存 结束*/
    
    /*************************************微楼书前台缓存开始********************************/
    /*微楼书前台首页的缓存 开始*/
    const MK_WLS_FRONT_LOUSHU = 'mk_wls_front_loushu_';
    private static function getFrontLoushuKey($token,$id){
        return self::MK_WLS_FRONT_LOUSHU.$token.'_'.$id;
    }

    public static function getFrontLoushu($token,$id){
        return Yii::$app->cache->get(self::getFrontLoushuKey($token,$id));
    }

    public static function setFrontLoushu($token,$id,$data){
        return Yii::$app->cache->set(self::getFrontLoushuKey($token,$id),$data);
    }

    /*
     * 删除场景：楼书+区域 | 楼书+关系 | 楼书+项目(building)，具体要看这个楼书是什么类型的版本
     */
    public static function deleteFrontLoushu($token,$id){
        return Yii::$app->cache->delete(self::getFrontLoushuKey($token,$id));
    }
    /*微楼书前台首页的缓存 结束*/   
 
    /*微楼书前台区域列表的缓存 开始*/
    const MK_WLS_FRONT_FLOORLIST = 'mk_wls_front_floorlist_';
    private static function getFrontFloorListKey($token, $loushuId, $sectionId){
        return self::MK_WLS_FRONT_FLOORLIST.$token.'_'.$loushuId.'_'.$sectionId;
    }

    public static function getFrontFloorList($token, $loushuId, $sectionId){
        return Yii::$app->cache->get(self::getFrontFloorListKey($token, $loushuId, $sectionId));
    }

    public static function setFrontFloorList($token, $loushuId, $sectionId, $data){
        return Yii::$app->cache->set(self::getFrontFloorListKey($token, $loushuId, $sectionId),$data);
    }

    /*
     * 删除场景：关系+项目(building) | 关系+活动，只要关系、项目或者活动变化都要更新
     */
    public static function deleteFrontFloorList($token, $loushuId, $sectionId){
        return Yii::$app->cache->delete(self::getFrontFloorListKey($token, $loushuId, $sectionId));
    }
    /*微楼书前台区域列表的缓存 结束*/ 
    

    /*微楼书前台项目详情的缓存 开始*/
    const MK_WLS_FRONT_BUILDINGINFO = 'mk_wls_front_buildinginfo_';
    private static function getFrontBuildingInfoKey($token, $buildingId){
        return self::MK_WLS_FRONT_BUILDINGINFO.$token.'_'.$buildingId;
    }

    public static function getFrontBuildingInfo($token, $buildingId){
        return Yii::$app->cache->get(self::getFrontBuildingInfoKey($token, $buildingId));
    }

    public static function setFrontBuildingInfo($token, $buildingId, $data){
        return Yii::$app->cache->set(self::getFrontBuildingInfoKey($token, $buildingId),$data);
    }

    /*
     * 删除场景：项目详情或者户型变动
     */
    public static function deleteFrontBuildingInfo($token, $buildingId){
        return Yii::$app->cache->delete(self::getFrontBuildingInfoKey($token, $buildingId));
    }
    /*微楼书前台项目详情的缓存 结束*/ 
    
    /*微楼书前台户型鉴赏的缓存 开始*/
    const MK_WLS_FRONT_HOUSETYPEINFO = 'mk_wls_front_housetypeinfo_';
    private static function getFrontHouseTypeInfoKey($token, $id){
        return self::MK_WLS_FRONT_HOUSETYPEINFO.$token.'_'.$id;
    }

    public static function getFrontHouseTypeInfo($token, $id){
        return Yii::$app->cache->get(self::getFrontHouseTypeInfoKey($token, $id));
    }

    public static function setFrontHouseTypeInfo($token, $id, $data){
        return Yii::$app->cache->set(self::getFrontHouseTypeInfoKey($token, $id),$data);
    }

    /*
     * 删除场景：户型变动
     */
    public static function deleteFrontHouseTypeInfo($token, $id){
        return Yii::$app->cache->delete(self::getFrontHouseTypeInfoKey($token, $id));
    }
    /*微楼书前台户型鉴赏的缓存 结束*/ 
    
    /*微楼书前台活动详情的缓存 开始*/
    const MK_WLS_FRONT_PARTYINFO = 'mk_wls_front_partyinfo_';
    private static function getFrontPartyInfoKey($token, $id){
        return self::MK_WLS_FRONT_PARTYINFO.$token.'_'.$id;
    }

    public static function getFrontPartyInfo($token, $id){
        return Yii::$app->cache->get(self::getFrontPartyInfoKey($token, $id));
    }

    public static function setFrontPartyInfo($token, $id, $data){
        return Yii::$app->cache->set(self::getFrontPartyInfoKey($token, $id),$data);
    }

    /*
     * 删除场景：活动详情变动
     */
    public static function deleteFrontPartyInfo($token, $id){
        return Yii::$app->cache->delete(self::getFrontPartyInfoKey($token, $id));
    }
    /*微楼书前台活动详情的缓存 结束*/
    
    /*微楼书前台活动列表的缓存 开始*/
    const MK_WLS_FRONT_PARTYLIST = 'mk_wls_front_partylist_';
    private static function getFrontPartyListKey($token, $loushuId){
        return self::MK_WLS_FRONT_PARTYLIST.$token.'_'.$loushuId;
    }

    public static function getFrontPartyList($token, $loushuId){
        return Yii::$app->cache->get(self::getFrontPartyListKey($token, $loushuId));
    }

    public static function setFrontPartyList($token, $loushuId, $data){
        return Yii::$app->cache->set(self::getFrontPartyListKey($token, $loushuId),$data);
    }

    /*
     * 删除场景：关系+活动变动
     */
    public static function deleteFrontPartyList($token, $loushuId){
        return Yii::$app->cache->delete(self::getFrontPartyListKey($token, $loushuId));
    }
    /*微楼书前台活动列表的缓存 结束*/
    
    //getFrontSectionBuildings($token, $loushuId, $sectionId)
    /*微楼书前台区域项目列表的缓存 开始*/
    const MK_WLS_FRONT_SECTIONBUILDINGS = 'mk_wls_front_sectionbuildings_';
    private static function getFrontSectionBuildingsKey($token, $loushuId, $sectionId){
        return self::MK_WLS_FRONT_SECTIONBUILDINGS.$token.'_'.$loushuId.'_'.$sectionId;
    }

    public static function getFrontSectionBuildings($token, $loushuId, $sectionId){
        return Yii::$app->cache->get(self::getFrontSectionBuildingsKey($token, $loushuId, $sectionId));
    }

    public static function setFrontSectionBuildings($token, $loushuId, $sectionId, $data){
        return Yii::$app->cache->set(self::getFrontSectionBuildingsKey($token, $loushuId, $sectionId),$data);
    }

    /*
     * 删除场景：区域项目列表关系+活动变动
     */
    public static function deleteFrontSectionBuildings($token, $loushuId, $sectionId){
        return Yii::$app->cache->delete(self::getFrontSectionBuildingsKey($token, $loushuId, $sectionId));
    }
    /*微楼书前台区域项目列表的缓存 结束*/
    
    /*************************************微楼前台缓存结束********************************/

    /*数据库链接数据的缓存 开始*/
    const MK_SYS_ORGANIZATION_CONNECTION = 'mk_sys_organization-connection_';
    private static function getOrganizationConnectionKey($orgName){
        return self::MK_SYS_ORGANIZATION_CONNECTION.$orgName;
    }

    public static function getOrganizationConnectionCache($orgName){
        return Yii::$app->cache->get(self::getOrganizationConnectionKey($orgName));
    }

    public static function setOrganizationConnectionCache($orgName,$data){
        return Yii::$app->cache->set(self::getOrganizationConnectionKey($orgName),$data);
    }

    public static function deleteOrganizationConnectionCache($orgName){
        return Yii::$app->cache->delete(self::getOrganizationConnectionKey($orgName));
    }
    /*数据库链接数据的缓存 结束*/

    /*UserIdentity数据的缓存 开始*/
    const MK_SYS_USER_IDENTITY = 'mk_sys_user-identity_';
    private static function getUserIdentityKey($id){
        return self::MK_SYS_USER_IDENTITY.$id;
    }

    public static function getUserIdentityCache($id){
        return Yii::$app->cache->get(self::getUserIdentityKey($id));
    }

    public static function setUserIdentityCache($id,$data){
        return Yii::$app->cache->set(self::getUserIdentityKey($id),$data);
    }

    public static function deleteUserIdentityCache($id){
        return Yii::$app->cache->delete(self::getUserIdentityKey($id));
    }
    /*UserIdentity数据的缓存 结束*/


    /*所有组织机构数据的缓存 开始*/
    const MK_USERMANAGE_ALL_ORGANIZATIONS = 'mk_usermanage_all-organizations_';
    private static function getAllOrganizationsKey(){
        return self::MK_USERMANAGE_ALL_ORGANIZATIONS."_".self::getKsfadminCurrentUnit()."";
    }

    public static function getAllOrganizationsCache(){
        return Yii::$app->cache->get(self::getAllOrganizationsKey());
    }

    public static function setAllOrganizationsCache($data){
        return Yii::$app->cache->set(self::getAllOrganizationsKey(),$data);
    }

    public static function deleteAllOrganizationsCache(){
        return Yii::$app->cache->delete(self::getAllOrganizationsKey());
    }
    /*所有组织机构数据的缓存 结束*/

    /*下一级组织机构数据的缓存 开始*/
    const MK_USERMANAGE_CHILD_ORGANIZATIONS = 'mk_usermanage_child-organizations_';
    private static function getChildOrganizationsKey($parentId){
        return self::MK_USERMANAGE_CHILD_ORGANIZATIONS."_".self::getKsfadminCurrentUnit()."_".$parentId;
    }

    public static function getChildOrganizationsCache($parentId){
        return Yii::$app->cache->get(self::getChildOrganizationsKey($parentId));
    }

    public static function setChildOrganizationsCache($parentId,$data){
        return Yii::$app->cache->set(self::getChildOrganizationsKey($parentId),$data);
    }

    public static function deleteChildOrganizationsCache($parentId){
        return Yii::$app->cache->delete(self::getChildOrganizationsKey($parentId));
    }
    /*下一级组织机构数据的缓存 结束*/


    /*所有角色数据的缓存 开始*/
    const MK_USERMANAGE_ALL_ROLES = 'mk_usermanage_all-roles_';
    private static function getAllRolesKey(){
        return self::MK_USERMANAGE_ALL_ROLES."_".self::getKsfadminCurrentUnit()."";
    }

    public static function getAllRolesCache(){
        return Yii::$app->cache->get(self::getAllRolesKey());
    }

    public static function setAllRolesCache($data){
        return Yii::$app->cache->set(self::getAllRolesKey(),$data);
    }

    public static function deleteAllRolesCache(){
        return Yii::$app->cache->delete(self::getAllRolesKey());
    }
    /*所有角色数据的缓存 结束*/

    /*所有功能点数据的缓存 开始*/
    const MK_USERMANAGE_ALL_APPLICATIONS = 'mk_usermanage_all-applications_';
    private static function getAllApplicationsKey(){
        return self::MK_USERMANAGE_ALL_APPLICATIONS."_".self::getKsfadminCurrentUnit()."";
    }

    public static function getAllApplicationsCache(){
        return Yii::$app->cache->get(self::getAllApplicationsKey());
    }

    public static function setAllApplicationsCache($data){
        return Yii::$app->cache->set(self::getAllApplicationsKey(),$data,5*3600);
    }

    public static function deleteAllApplicationsCache(){
        return Yii::$app->cache->delete(self::getAllApplicationsKey());
    }
    /*所有功能点数据的缓存 结束*/

    /*所有YIIAction数据的缓存 开始*/
    const MK_USERMANAGE_ALL_YII_ACTION = 'mk_usermanage_all-yii-action_';
    private static function getAllYiiActionKey(){
        return self::MK_USERMANAGE_ALL_YII_ACTION."_".self::getKsfadminCurrentUnit()."";
    }

    public static function getAllYiiActionCache(){
        return Yii::$app->cache->get(self::getAllYiiActionKey());
    }

    public static function setAllYiiActionCache($data){
        return Yii::$app->cache->set(self::getAllYiiActionKey(),$data);
    }

    public static function deleteAllYiiActionCache(){
        return Yii::$app->cache->delete(self::getAllYiiActionKey());
    }
    /*所有YIIAction数据的缓存 结束*/


    /*用户登录错误次数数据的缓存 开始*/
    const MK_SYS_LOGIN_ERROR_COUNT = 'mk_sys_login-error-count_';
    private static function getLoginErrorCountKey($userCode,$unitName){
        return self::MK_SYS_LOGIN_ERROR_COUNT.$userCode.'_'.$unitName;
    }

    public static function getLoginErrorCountCache($userCode,$unitName){
        return Yii::$app->cache->get(self::getLoginErrorCountKey($userCode,$unitName));
    }

    public static function setLoginErrorCountCache($userCode,$unitName,$data,$duration = 0){
        return Yii::$app->cache->set(self::getLoginErrorCountKey($userCode,$unitName),$data,$duration);
    }

    public static function deleteLoginErrorCountCache($userCode,$unitName){
        return Yii::$app->cache->delete(self::getLoginErrorCountKey($userCode,$unitName));
    }
    /*用户登录错误次数数据的缓存 结束*/
    
    
    /*调用ERPApi token数据的缓存 开始*/
    const MK_SYS_ERP_API_TOKEN = 'mk_sys_erp_api_token_';
    private static function getERPApiTokenKey($token){
    	return self::MK_SYS_ERP_API_TOKEN.$token;
    }
    
    public static function getERPApiTokenCache($token){
    	return Yii::$app->cache->get(self::getERPApiTokenKey($token));
    }
    
    public static function setERPApiTokenCache($token,$data,$duration = 0){
    	return Yii::$app->cache->set(self::getERPApiTokenKey($token),$data,$duration);
    }
    
    public static function deleteERPApiTokenCache($token){
    	return Yii::$app->cache->delete(self::getERPApiTokenKey($token));
    }
    /*调用ERPApi token数据的缓存 结束*/

    /*cms缓存 开始 */
    const MK_CMS_DATA_INDEX = 'mk_cms_data_index';
    const MK_CMS_DATA_SUB_PAGE = 'mk_cms_data_sub_page';
    public static function getCmsDataIndex(){
        return Yii::$app->cache->get(self::MK_CMS_DATA_INDEX);
    }
    public static function setCmsDataIndex($data,$exp=60){
        return Yii::$app->cache->set(self::MK_CMS_DATA_INDEX,$data,$exp);
    }
    public static function getCmsDataSubPge(){
        return Yii::$app->cache->get(self::MK_CMS_DATA_SUB_PAGE);
    }
    public static function setCmsDataSubPge($data,$exp=60){
        return Yii::$app->cache->set(self::MK_CMS_DATA_SUB_PAGE,$data,$exp);
    }
    /*cms缓存 结束*/
    
    /**合同导出数据缓存 start */
    const MK_EXPORT_CONTRACT_CACHE = 'mk_export_contract_cache';
    public static function getExportContract(){
        return Yii::$app->cache->get(self::MK_EXPORT_CONTRACT_CACHE);
    }
    public static function setExportContract($data,$exp=600){
        return Yii::$app->cache->set(self::MK_EXPORT_CONTRACT_CACHE,$data,$exp);
    }
    public static function deleteExportContract(){
    	return Yii::$app->cache->delete(self::MK_EXPORT_CONTRACT_CACHE);
    }
    /**合同导出数据缓存 end */
    /*****ConfigSettings start *****/
    const CONFIG_SETTINGS_DATA = "config_settings_data_";
    public static function setConfigSettingsData ($keyName, $data, $exp = 60) {
        return Yii::$app->cache->set(self::CONFIG_SETTINGS_DATA . $keyName, $data, $exp);
    }
    public static function getConfigSettingsData ($keyName) {
        return Yii::$app->cache->get(self::CONFIG_SETTINGS_DATA . $keyName);
    }
    public static function deleteConfigSettingsData($keyName){
        return Yii::$app->cache->delete(self::CONFIG_SETTINGS_DATA . $keyName);
    }

}