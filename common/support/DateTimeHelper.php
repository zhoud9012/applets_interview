<?php
namespace common\support;

class DateTimeHelper
{
    const ONE_WEEK_AGO = '1week';
    const ONE_MONTH_AGO = '1month';
    const THREE_MONTH_AGO = '3month';

    /**
     * 根据关键字获取时间，如果不在支持的关键字范围内，则空字符串
     * @param type $keyword 最近一周：1week  最近一个月：1month 最近三个月：3month
     * @param type $baseTime 当前时间 
     * @return string
     */
    public static function getDateByKeyword($keyword,$baseTime)
    {
        if(is_null($baseTime))
        {
            $baseTime = date("Y-m-d H:i:s");//如不存在开始时间，则拿当前服务器时间
        }
        
        $date = '';
        switch ($keyword) {
            case '3month': //3个月
                $date = date("Y-m-d 00:00:00", strtotime("1 day", strtotime("-3 month", $baseTime)));
                break;
            case '1month': //1个月
                $date = date("Y-m-d 00:00:00", strtotime("1 day", strtotime("-1 month", $baseTime)));
                break;
            case '1week': //1周
                $date = date("Y-m-d 00:00:00", strtotime("1 day", strtotime("-1 week", $baseTime)));
                break;
        }        
        return $date;
    }
    
    /**
     * 获取数据库时间
     * @return string
     * @author sunfx
     */
    public static function getNow($db){
    	$sql = "select now() as now;";
    	return $db->createCommand($sql)->queryScalar();
    }

    public static function getDate($datetime, $format = 'Y-m-d')
    {
        if (!empty($datetime)) {
            $result = date($format, strtotime($datetime));
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * 验证日期
     * @param $date string 待验证的日期
     * @return bool
     */
    public static function validateDate($date){
        //匹配时间格式为2012-02-16或2012-02-16 23:59:59前面为0的时候可以不写
        $patten = "/^(\d{4}|\d{2})-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])\s*(\s+(0?[0-9]|1[0-9]|2[0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9]))?$/";
        if (preg_match ( $patten, $date )) {
            return true;
        } else {
            return false;
        }
    }

}