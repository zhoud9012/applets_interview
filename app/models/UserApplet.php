<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_applet".
 *
 * @property string $user_id 用户id
 * @property string $phone 手机号
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class UserApplet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_applet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'phone'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['user_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['phone'], 'string', 'max' => 13],
            [['is_del'], 'string', 'max' => 4],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
