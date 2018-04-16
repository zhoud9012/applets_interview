<?php

/**
 * 分页参数设置
 * @author wangwx 2015-5-15
 */
namespace common\support;

class PageParam {

    public $pageIndex;
    public $pageSize;
    public $sortBy;
    public $sortAsc;

    public function __construct($pageIndex, $pageSize, $sortBy, $sortAsc) {
        $this->pageIndex = intval($pageIndex) == 0 ? 1 : intval($pageIndex);
        $this->pageSize = intval($pageSize) > 0 ? intval($pageSize) : 10000;
        $this->sortBy = empty($sortBy) || is_null($sortBy) ? '' : $sortBy;
        $this->sortAsc = $this->isTrue($sortAsc)?SORT_ASC:SORT_DESC;
    }
    
    /**
     * 获取跳过的条数
     * @return int
     */
    public function getSkipByPageParam()
    {
        return $this->pageSize * ($this->pageIndex - 1);
    }     

    function isTrue($var) {
        if (is_bool($var)) {
            return $var;
        } else if ($var === NULL || $var === 'NULL' || $var === 'null') {
            return false;
        } else if (is_string($var)) {
            $var = trim($var);
            if (strcasecmp($var, 'false') == 0) {
                return false;
            } else if (strcasecmp($var, 'true') == 0) {
                return true;
            } else if (ctype_digit($var)) {
                if ((int) $var) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else if (ctype_digit((string) $var)) {
            if ((int) $var) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
