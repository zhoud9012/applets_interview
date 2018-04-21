<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "question_info".
 *
 * @property string $question_id 试题id
 * @property string $question_name 试题名称
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class QuestionInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'question_name'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['question_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['question_name'], 'string', 'max' => 48],
            [['question_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'question_name' => 'Question Name',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
