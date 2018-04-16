<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-04-10
 * Time: 9:36
 */

namespace common\support;

use common\support\StringHelper;


/**
 * 文件上传基类
 */
class UploaderBase {

    protected $_fileSize;
    protected $_cntTypeArray;
    protected $_httpFile;
    protected $_rootPath;
    protected $_sourceName;

    public function __construct($fileSize,$cntTypeArray,$httpFile,$rootPath){
        $this->_fileSize = $fileSize;
        $this->_cntTypeArray = $cntTypeArray;
        $this->_httpFile = $httpFile;
        $this->_rootPath = $rootPath;
    }

    /**
     * 检测文件类型
     * @return type
     */
    public function checkFileType(){
        if(is_null($this->_cntTypeArray) || count($this->_cntTypeArray)==0){
            return true;
        }
        return in_array($this->_httpFile["type"],$this->_cntTypeArray);
    }

    /**
     * 检测文件大小，字节比较
     * @return type
     */
    public function checkFileSize(){
        return $this->_httpFile["size"] < $this->_fileSize;
    }

    protected function getFileExtByName(){
        $sourceName = $this->_httpFile["name"];
        $filenameParts = explode(".",$sourceName);
        return $filenameParts[count($filenameParts)-1];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    protected function getFileExt(){
        $type = $this->_httpFile["type"];
        $name = $this->_httpFile["name"];
        $ext = "png";
        switch($type){
            case "image/gif":
                $ext = "gif";
                break;
            case "image/jpeg":
            case "image/pjpeg":
                $ext = "jpg";
                break;
            case "image/png":
                $ext = "png";
                break;
            default:                
                $ext = StringHelper::getFileExtName($name);
                break;
        }
        return $ext;
    }

    public function upload(){
        return "";
    }

    protected function genFilePath($ext){
        $path = $this->_rootPath.'/'.date("Ym")."/".date("YmdH")."/file_".StringHelper::uuid().".".$ext;
        return $path;
    }
}