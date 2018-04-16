<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-03-27
 * Time: 10:06
 */

namespace common\support;
use PHPExcel;

class ExcelManage {

    public static function responseToExcel($dataList,$columnDefine,$columnWidth,$filename = null,$addOrderColumn = true){
        $excelManage = new ExcelManage($columnDefine,$columnWidth,$filename,$addOrderColumn);

        $excelManage->createHeader();
        $excelManage->appendRows($dataList);
        $excelManage->responseExcel();
    }


    private  $columnDefine;
    private  $columnWidth;
    private  $filename;
    private  $addOrderColumn;

    private $phpexcel = null;
    private $rowIndex = 1;
    private $orderIndex = 0;

    public function __construct($columnDefine,$columnWidth,$filename = null,$addOrderColumn = true){
        $this->columnDefine = $columnDefine;
        $this->columnWidth = $columnWidth;
        $this->filename = $filename;
        $this->addOrderColumn = $addOrderColumn;
    }

    /**
     * 上传excel转数组
     * @param  string $filename 文件索引名
     * @param  int $startRow 第几行开始转换
     * @return array           
     */
    public static function importData($filename,$startRow)
    {
        $file=$_FILES['filename']['tmp_name'];
       return self::importDataByFilePath($file,$startRow);
    }

    public static function importDataByFilePath($filepath,$startRow){
        // Yii::import("special.extensions.PHPExcel");
        $PHPExcel = new PHPExcel();
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filepath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filepath)){
                return false;
            }
        }
        $PHPExcel = $PHPReader->load($filepath);
        $currentSheet = $PHPExcel->getSheet(0);//读取第一个工作表
        $allColumn = $currentSheet->getHighestColumn();//取得最大的列号
        $allRow = $currentSheet->getHighestRow();//取得一共有多少行
        /**从第二行开始输出，因为excel表中第一行为列名*/
        $arr=array();
        for($currentRow = $startRow;$currentRow <= $allRow;$currentRow++){
            /**从第A列开始输出*/
            for($currentColumn= 'A';($currentColumn<= $allColumn || strlen($currentColumn) < strlen($allColumn)); $currentColumn++){
                $val = $currentSheet->getCell($currentColumn.$currentRow)->getValue(); /*ord()将字符转为十进制数*/
                /**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
                //$arr[$currentRow][]=  iconv('utf-8','gb2312', $val)."＼t";
                $arr[$currentRow][]=  trim($val);
            }
        }
        return $arr;
    }

    /**
     * @return null|PHPExcel
     */
    private function getCurrentExcel(){
        if(is_null($this->phpexcel)) {
            //实例化
            $phpexcel = new PHPExcel();
            $this->phpexcel = $phpexcel;
        }
        return $this->phpexcel;
    }

    /**
     * @return \PHPExcel_Worksheet
     */
    public function getActionSheet(){
        //获取当前激活的sheet
        return $activeSheet = $this->getCurrentExcel()->getActiveSheet();
    }

    public function appendCustomRow($data){
        $activeSheet = $this->getActionSheet();
        $rowIndex = $this->rowIndex;
        $columnIndex = 'A';
        foreach ($data as $key=>$value) {
            $activeSheet->setCellValue($columnIndex.$rowIndex, $value);
            $columnIndex++;
        }
        $this->nextRow();
    }

    public function nextRow(){
        $this->rowIndex++;
    }

    public function getRowIndex(){
        return $this->rowIndex;
    }

    public function createHeader(){
        $activeSheet = $this->getActionSheet();

        $rowIndex = $this->rowIndex;
        //设置标题
        $firstColumnIndex = 'A';
        if($this->addOrderColumn){
            $activeSheet->getColumnDimension('A')->setWidth(7);
            $activeSheet->setCellValue('A'.$rowIndex, '序号');
            $firstColumnIndex = 'B';
        }
        $columnIndex = $firstColumnIndex;

        $index = 0;
        foreach ($this->columnDefine as $field=>$columnName) {
            $activeSheet->getColumnDimension($columnIndex)->setWidth($this->columnWidth[$index]);
            $activeSheet->setCellValue($columnIndex.$rowIndex, $columnName);
            $columnIndex++;
            $index++;
        }

        $rowIndex++;
        $this->rowIndex = $rowIndex;
    }

    public function appendRows($dataList){
        $activeSheet = $this->getActionSheet();
        $rowIndex = $this->rowIndex;
        foreach ($dataList as $key => $item) {
            $columnIndex = 'A';
            if($this->addOrderColumn){
                $activeSheet->setCellValueExplicit($columnIndex.$rowIndex, $this->orderIndex+1);
                $this->orderIndex++;
                $columnIndex++;
            }

            foreach ($this->columnDefine as $field=>$columnName) {
                $pValue = isset($item->$field) ? $item->$field : isset($item[$field]) ? $item[$field] : '';
                $activeSheet->setCellValueExplicit($columnIndex.$rowIndex, $pValue);
                $columnIndex ++;
            }
            $rowIndex++;
        }
        $this->rowIndex = $rowIndex;
    }

    public function saveFile($filepath){
        $obj_Writer = \PHPExcel_IOFactory::createWriter($this->getCurrentExcel(), 'Excel5');
        $obj_Writer->save($filepath);
    }

    public function responseExcel($filename = null){
        if(is_null($filename)){
            $filename = $this->filename;
        }
        $phpexcel = $this->getCurrentExcel();

        $obj_Writer = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');

        $ua = $_SERVER["HTTP_USER_AGENT"];
        if(is_null($filename)) {
            $filename = 'mysoft_account_' . date('Ymd') . ".xls"; //文件名
        }
        else{
            $filename = $filename. ".xls"; //文件名
        }
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);

        //设置header
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");

        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        }

        header("Pragma: no-cache");
        header("Expires: 0");
        $obj_Writer->save('php://output'); //输出
        die();
    }

}