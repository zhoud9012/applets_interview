<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 17-1-5
 * Time: 下午3:17
 */

namespace common\support;

use Yii;

/**
 * 邮件发送助手，未实现异步发送
 * Class EmailHelper
 * @package common\support
 */
class EmailHelper
{
    /**
     * 同步邮件发送
     * @param      $email
     * @param      $subject
     * @param      $body
     * @param null $from
     * @return bool
     */
    public static function send($email, $subject, $body, $from = null)
    {
        $mail = Yii::$app->mailer->compose();

        if ($from) {
            $mail->setFrom($from);
        }

        $mail->setTo($email);
        $mail->setSubject($subject);
        $mail->setHtmlBody($body);
        if ($mail->send()) {
            return true;
        } else {
            Yii::error(sprintf('发送邮件失败 To:%s Subject:%s', $email, $subject));

            return false;
        }
    }

    /**
     * 异常邮件发送队列
     * TODO
     * @param $email
     * @param $subject
     * @param $body
     * @param $from
     */
    public static function asyncSend($email, $subject, $body, $from)
    {

    }
}