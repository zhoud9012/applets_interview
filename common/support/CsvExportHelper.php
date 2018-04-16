<?php
/**
 * 导出CSV格式文件
 * Created by PhpStorm.
 * User: zhongyb
 * Date: 17-2-14
 * Time: 上午10:30
 */

namespace common\support;


class CsvExportHelper
{
    /**
     * @var string 导出目录，默认是@runtime/web/csv/
     */
    public $exportDir;

    /**
     * @var string 文件名
     */
    public $fileName;

    /**
     * @var string 编码
     */
    public $codingFormat;

    /**
     * @var string 传到oss上的url地址
     */
    public $ossUrl = '';

    /**
     * @var object 创建的CSV文件实体
     */
    public $csvFile = null;

    public function __construct($fileName, $codingFormat = 'utf-8', $exportDir = '@runtime/web/csv/')
    {
        $this->codingFormat = $codingFormat;
        $this->fileName = $fileName;

        //创建目录
        $this->exportDir = \Yii::getAlias($exportDir);
        \yii\helpers\FileHelper::createDirectory($this->exportDir);

        //创建文件
        $allFilePath = $this->exportDir . $fileName;
        $csvFile = fopen($allFilePath, 'a');

        $this->csvFile = $csvFile;

        //非中文编码需要输出BOM头
        if($codingFormat != 'gbk' || $codingFormat != 'gb2312' || $codingFormat != 'GBK' || $codingFormat != 'GB2312') {
            $bom = $this->selectBomByCodingFormat($codingFormat);
            //输出BOM头
            fwrite($this->csvFile, $bom);
        }
    }

    /**
     * 写入数据
     * 中文编码需要转码
     * @param $data array 要写入的数据
     * @param bool $isMultipleLines 是否多行数据
     */
    public function writeData($data, $isMultipleLines = false)
    {
        if($isMultipleLines){
            foreach ($data as $row){
                $codingChange = $this->_arrayEncode($row, $this->codingFormat);
                fputcsv($this->csvFile, $codingChange);
            }
        }else {
            $codingChange = $this->_arrayEncode($data, $this->codingFormat);
            fputcsv($this->csvFile, $codingChange);
        }
        fclose($this->csvFile);
    }

    /**
     * 直接在客户端输出文件
     */
    public function outputFile()
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-Disposition:attachment;filename=" . $this->fileName);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header("Pragma: no-cache");
        header("Expires: 0");

        $file = $this->exportDir . $this->fileName;
        echo file_get_contents($file);

        //删除本地文件
        @unlink($this->exportDir . $this->fileName);
    }

    /**
     * 把生成的文件上传到OSS，并删除本地文件
     */
    public function uploadToOss()
    {
        $key = sprintf("%s-%s", date('YmdHis'), $this->fileName);
        OSS::upload($key, $this->exportDir . $this->fileName);

        //删除本地文件
        @unlink($this->exportDir . $this->fileName);

        $this->ossUrl = OSS::getUrl($key);
    }

    /**
     * 非中文编码时，通过编码选择BOM头
     * @param string $codingFormat
     * @return string
     */
    private function selectBomByCodingFormat($codingFormat = 'utf-8')
    {
        switch ($codingFormat) {
            default :
                $ret = chr(0xEF) . chr(0xBB) . chr(0xBF);
                break;
        }

        return $ret;
    }

    private function _arrayEncode($data, $codingFormat)
    {
        if($codingFormat == 'utf-8'){
            return $data;
        }

        $result = [];
        foreach ($data as $item) {
            $result[] = mb_convert_encoding($item, $codingFormat, 'utf-8');
        }
        return $result;
    }
}