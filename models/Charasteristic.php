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
class Charasteristic extends \app\lib\db\XActiveRecord
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
    
    public function getAccessRule($identity=null) {
        return array('service' => $this->service0->getAccessQuery($identity));
    }
    
    public function import($charasteristicsArray, $serviceIndex) {
        $subscriptionCount=0;
        foreach ($charasteristicsArray as $charasteristics) {
            if (!$charasteristicsModel = Charasteristic::find()->where(array(
                        'service' => $serviceIndex,
                        'charasteristicUuid' => $charasteristics['charasteristicUuid'],
                    ))->one()) {
                $charasteristicsModel = new Charasteristic();
            }
            $charasteristics = array_merge($charasteristics, array('service' => $serviceIndex));
            $charasteristicsModel->setattributes($charasteristics);
            if ($charasteristicsModel->save()) {
                $charasteristicsIndex = $charasteristicsModel->getPrimaryKey();
                //return $charasteristicsIndex;
                if (isset($charasteristics['subscriptions'])) {
                    $subscriptionCount+=\app\models\SubscriptionData::import($charasteristics['subscriptions'], $charasteristicsIndex);
                }
            } /*else {
                return $charasteristicsModel->getErrors();
            }*/
        }
        return $subscriptionCount;
    }
}
