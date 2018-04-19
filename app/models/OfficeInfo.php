<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "office_info".
 *
 * @property string $office_id 职位id
 * @property string $office_name 职位名称
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class OfficeInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'office_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['office_id', 'office_name'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['office_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['office_name'], 'string', 'max' => 48],
            [['office_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'office_id' => 'Office ID',
            'office_name' => 'Office Name',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
