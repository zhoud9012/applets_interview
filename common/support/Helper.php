<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 15-4-3
 * Time: 17:57
 */

namespace common\support;


class Helper
{
    /**
     * 组装分页结果数据
     * 
     * @param $total
     * @param $items
     * @return array
     */
    public static function setPageResult($total, $items)
    {
        return ['total' => $total, 'items' => $items];
    }
    
    /**
     * 获取分页skip
     * @param int $pageIndex 页数
     * @param int $pageSize 页码大小
     * @return number
     */
    public static function getSkip($pageIndex, $pageSize)
    {
    	if ($pageIndex <=0 || $pageIndex == 1) {
    		return 0;
    	}else{
    		return $pageSize * ($pageIndex - 1);
    	}
    }
    
    /**
     * 获取分页skip
     * @param \common\support\PageParam $pageParam
     * @return number
     */
    public static function getSkipByPageParam($pageParam)
    {
    	if ($pageParam->pageIndex <= 0 || $pageParam->pageIndex == 1) {
    		return 0;
    	}else{
    		return $pageParam->pageSize * ($pageParam->pageIndex - 1);
    	}
    }
    
    /**
     * 格式化文件大小
     * @param type int
     * @return string
     */
    public static function setupSize($fileSize) {
        $size = sprintf("%u", $fileSize);
        if ($size == 0) {
            return("0 Bytes");
        }
        $sizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizename[$i];
    }


}