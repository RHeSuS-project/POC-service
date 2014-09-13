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
class DoctorToPatient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor_to_patient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doctor', 'patient'], 'required'],
            [['doctor', 'patient'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'doctor' => Yii::t('app', 'Doctor'),
            'patient' => Yii::t('app', 'Patient'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor0()
    {
        return $this->hasOne(User::className(), ['id' => 'doctor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient0()
    {
        return $this->hasOne(User::className(), ['id' => 'patient']);
    }
    
    public function extraFields()
    {
        return ['doctor0','patient0'];
    }
}
