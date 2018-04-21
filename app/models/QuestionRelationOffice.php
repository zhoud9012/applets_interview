<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "question_relation_office".
 *
 * @property string $relation_id 关系id
 * @property string $question_id 试题id
 * @property string $office_id 职位id
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class QuestionRelationOffice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_relation_office';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['relation_id', 'question_id', 'office_id'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['relation_id', 'question_id', 'office_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['is_del'], 'string', 'max' => 4],
            [['relation_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'relation_id' => 'Relation ID',
            'question_id' => 'Question ID',
            'office_id' => 'Office ID',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
