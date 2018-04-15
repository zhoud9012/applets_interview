<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $name
 * @property int $num
 */
class Book extends \yii\db\ActiveRecord
{

    public function fields() {
        return [
            'id',

            'company_id',

            'type_id',

            'company'=>function () {
                return User::findOne($this->id);
            },
            'create_time'=> function () {
                return \Yii::$app->formatter->asDatetime(time(),'php:Y-m-d H:i:s');
            },
            'update_time'=> function () {
                return \Yii::$app->formatter->asDatetime(time(),'php:Y-m-d H:i:s');
            },
        ];
        //$fields = parent::fields();
        // 去掉一些包含敏感信息的字段
        //unset($fields['update_time']);
        //return $fields;
    }

    public function extraFields()
    {
        return ['projects', 'projs'];
    }

    // 连接第二张表
    public function getProjects()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }

    // 连接第三张表
    public function getProjs()
    {
        return '333';
        //return $this->hasMany(Proj::className(), ['idproj' => 'proj_id'])->via('projects');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
            [['num'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'num' => 'Num',
        ];
    }
}
