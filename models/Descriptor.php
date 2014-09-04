<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "descriptor".
 *
 * @property integer $id
 * @property integer $charasteristic
 * @property string $value
 *
 * @property Charasteristic $charasteristic0
 */
class Descriptor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'descriptor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['charasteristic'], 'required'],
            [['charasteristic'], 'integer'],
            [['value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'charasteristic' => Yii::t('app', 'Charasteristic'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharasteristic0()
    {
        return $this->hasOne(Charasteristic::className(), ['id' => 'charasteristic']);
    }
}
