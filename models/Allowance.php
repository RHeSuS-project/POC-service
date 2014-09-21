<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "allowance".
 *
 * @property integer $id
 * @property integer $user
 * @property integer $allowance
 * @property integer $time
 *
 * @property User $user0
 */
class Allowance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'allowance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'allowance', 'time'], 'required'],
            [['user', 'allowance', 'time'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'allowance' => Yii::t('app', 'Allowance'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }
}
