<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service".
 *
 * @property integer $id
 * @property integer $device
 * @property string $serviceUuid
 *
 * @property Charasteristic[] $charasteristics
 * @property Device $device0
 */
class Service extends \app\lib\db\XActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device', 'serviceUuid'], 'required'],
            [['device'], 'integer'],
            [['serviceUuid'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'device' => Yii::t('app', 'Device'),
            'serviceUuid' => Yii::t('app', 'Service Uuid'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharasteristics()
    {
        return $this->hasMany(Charasteristic::className(), ['service' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice0()
    {
        return $this->hasOne(Device::className(), ['id' => 'device']);
    }
    
    public function getAccessRule($identity=null) {
        return array('device' => $this->device0->getAccessQuery($identity));
    }
}
