<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "interviewer_info".
 *
 * @property string $interviewer_id 面试官id
 * @property string $phone 手机号
 * @property string $name 姓名
 * @property string $email 邮箱
 * @property string $remark 备注
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class InterviewerInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interviewer_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interviewer_id', 'phone', 'name'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['interviewer_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['phone'], 'string', 'max' => 13],
            [['name'], 'string', 'max' => 48],
            [['email'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 500],
            [['interviewer_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'interviewer_id' => 'Interviewer ID',
            'phone' => 'Phone',
            'name' => 'Name',
            'email' => 'Email',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
