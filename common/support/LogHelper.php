<?php

namespace common\support;

use Yii;

/**
 * Description of LogHelper
 *
 * Created by liuzz02
 * Date: 2015-7-23
 * Time: 10:04:56
 */
class LogHelper extends \yii\base\Object {

    /**
     * @var string log file path or path alias. If not set, it will use the "@runtime/logs/app.log" file.
     * The directory containing the log files will be automatically created if not existing.
     */
    public $logFile;

    /**
     * @var integer maximum log file size, in kilo-bytes. Defaults to 10240, meaning 10MB.
     */
    public $maxFileSize = 10240; // in KB

    /**
     * @var integer number of log files used for rotation. Defaults to 5.
     */
    public $maxLogFiles = 5;

    /**
     * @var integer how many messages should be logged before they are flushed from memory and sent to targets.
     * Defaults to 1000, meaning the [[flush]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[flush()]].
     */
    public $flushInterval = 20;
    public $logger = null;

    public function getLogger() {
        if ($this->logger) {
            return $this->logger;
        }
        $config = [
            'targets' => [
                'file' => [
                    'class' => '\yii\log\FileTarget',
                    'logVars' => [],
                    'logFile' => $this->logFile,
                    'maxFileSize' => $this->maxFileSize, //10kb
                    'maxLogFiles' => $this->maxLogFiles, //10个文件分卷
                    'exportInterval' => $this->flushInterval,
                    'prefix' => create_function('$m', 'return "[" . getmypid() . "]";'),
                ],
            ],
            'logger' => \yii\BaseYii::createObject('yii\log\Logger'),
        ];
        $dispatcher = new \yii\log\Dispatcher($config);
        $dispatcher->setFlushInterval($this->flushInterval);
        $this->logger = $dispatcher->getLogger();
        return $this->logger;
    }

    public function log($msg, $level = \yii\log\Logger::LEVEL_INFO, $category = 'console') {
        $logger = $this->getLogger();
        $logger->log($msg, $level, $category);
    }

}
