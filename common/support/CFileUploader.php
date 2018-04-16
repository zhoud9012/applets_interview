<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-04-10
 * Time: 9:53
 */

namespace common\support;


class CFileUploader extends UploaderBase {

    protected $_webFilePath;
    protected $_webRootPath;

    public function __construct($fileSize,$cntTypeArray,$httpFile,$webFilePath,$webRootPath){
        parent::__construct($fileSize,$cntTypeArray,$httpFile,$webRootPath);

        $this->_webFilePath = $webFilePath;
        $this->_webRootPath = $webRootPath;
    }

    public function upload($localFile = null){

        $ext = $this->getFileExt();
        $webPath = $this->genFilePath($ext);

        $filepath = $this->_webFilePath.$webPath;

        if(!file_exists(dirname($filepath))){
            mkdir(dirname($filepath),0777,true);
        }
        if (!$localFile)
        {
        	$localFile = $_FILES["file"]["tmp_name"];
        }
        copy($localFile, $filepath);

        return $this->toUrl($webPath);
    }

    private function toUrl($path){
        return "http://".$_SERVER['HTTP_HOST'].$path;
    }
}