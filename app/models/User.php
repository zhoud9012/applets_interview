<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $password_hash 密码
 * @property string $password_reset_token 密码token
 * @property string $email 邮箱
 * @property string $auth_key
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $access_token restful请求token
 * @property int $allowance restful剩余的允许的请求数
 * @property int $allowance_updated_at restful请求的UNIX时间戳数
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'allowance', 'allowance_updated_at'], 'integer'],
            [['username', 'email'], 'string', 'max' => 20],
            [['password_hash'], 'string', 'max' => 100],
            [['password_reset_token', 'auth_key', 'access_token'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 3],
            [['username'], 'unique'],
            [['access_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'access_token' => 'Access Token',
            'allowance' => 'Allowance',
            'allowance_updated_at' => 'Allowance Updated At',
        ];
    }
}