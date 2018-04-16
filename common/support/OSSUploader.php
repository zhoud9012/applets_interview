<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-04-10
 * Time: 9:47
 */

namespace common\support;


class OSSUploader extends UploaderBase {

    public function upload($localFile = null){
        $ext = $this->getFileExt();
        $key = $this->genFilePath($ext);
        if (!$localFile)
        {
        	$localFile = $this->_httpFile["tmp_name"];
        }
        
        //TODO oss端Bucket管理？
        OSS::upload($key, $localFile);
        return OSS::getUrl($key);
    }
}