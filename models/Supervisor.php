<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doctor_to_patient".
 *
 * @property integer $id
 * @property integer $doctor
 * @property integer $patient
 *
 * @property User $doctor0
 * @property User $patient0
 */
class Supervisor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supervisor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supervisor', 'user'], 'required'],
            [['supervisor', 'user'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'supervisor' => Yii::t('app', 'supervisor'),
            'user' => Yii::t('app', 'user'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupervisor0()
    {
        return $this->hasOne(User::className(), ['id' => 'supervisor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }
    
    public function extraFields()
    {
        return ['supervisor0','user0'];
    }
}
