<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "candidates_info".
 *
 * @property string $candidates_id 应试者id
 * @property string $phone 手机号
 * @property string $name 姓名
 * @property string $email 邮箱
 * @property string $office_id 职位id
 * @property string $interviewer_id 面试官id
 * @property string $interview_time 预约面试时间
 * @property string $company_id 公司id
 * @property string $sign_in_time 签到时间
 * @property int $interview_state 面试状态 1:未签到 2:答题中 3:等待审题 4:审题中 5:面试中 6:面试结束
 * @property int $interview_result 面试结果 1:暂无 2:通过 3:淘汰 4:爽约
 * @property string $interview_appraise 面试评价
 * @property string $written_test_appraise 笔试评价
 * @property string $created_by 创建人
 * @property string $created_on 创建时间
 * @property string $modified_by 修改人
 * @property string $modified_on 修改时间
 * @property int $is_del 1:未删除;2:已删除
 */
class CandidatesInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'candidates_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['candidates_id', 'phone', 'name', 'office_id', 'interviewer_id', 'company_id'], 'required'],
            [['interview_time', 'sign_in_time', 'created_on', 'modified_on'], 'safe'],
            [['candidates_id', 'company_id', 'created_by', 'modified_by'], 'string', 'max' => 36],
            [['phone'], 'string', 'max' => 13],
            [['name'], 'string', 'max' => 48],
            [['email', 'office_id', 'interviewer_id'], 'string', 'max' => 255],
            [['interview_state', 'interview_result'], 'string', 'max' => 1],
            [['interview_appraise', 'written_test_appraise'], 'string', 'max' => 500],
            [['is_del'], 'string', 'max' => 4],
            [['candidates_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'candidates_id' => 'Candidates ID',
            'phone' => 'Phone',
            'name' => 'Name',
            'email' => 'Email',
            'office_id' => 'Office ID',
            'interviewer_id' => 'Interviewer ID',
            'interview_time' => 'Interview Time',
            'company_id' => 'Company ID',
            'sign_in_time' => 'Sign In Time',
            'interview_state' => 'Interview State',
            'interview_result' => 'Interview Result',
            'interview_appraise' => 'Interview Appraise',
            'written_test_appraise' => 'Written Test Appraise',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
            'is_del' => 'Is Del',
        ];
    }
}
