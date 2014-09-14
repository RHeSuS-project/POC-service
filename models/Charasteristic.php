<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "charasteristic".
 *
 * @property integer $id
 * @property integer $service
 * @property string $charasteristicUuid
 *
 * @property Service $service0
 * @property Descriptor[] $descriptors
 * @property SubscriptionData[] $subscriptionDatas
 */
class Charasteristic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charasteristic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service', 'charasteristicUuid'], 'required'],
            [['service'], 'integer'],
            [['charasteristicUuid'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'service' => Yii::t('app', 'Service'),
            'charasteristicUuid' => Yii::t('app', 'Charasteristic Uuid'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService0()
    {
        return $this->hasOne(Service::className(), ['id' => 'service']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptors()
    {
        return $this->hasMany(Descriptor::className(), ['charasteristic' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptionDatas()
    {
        return $this->hasMany(SubscriptionData::className(), ['charasteristic' => 'id']);
    }

    public function extraFields()
    {
        return ['service0','descriptors'];
    }
    
}
