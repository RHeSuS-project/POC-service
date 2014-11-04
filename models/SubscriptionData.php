<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscription_data".
 *
 * @property integer $id
 * @property integer $charasteristic
 * @property double $value
 * @property string $datetime
 *
 * @property Charasteristic $charasteristic0
 */
class SubscriptionData extends \app\lib\db\XActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'subscription_data';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['charasteristic', 'value', 'datetime'], 'required'],
            [['charasteristic', 'datetime'], 'integer'],
            [['value'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'charasteristic' => Yii::t('app', 'Charasteristic'),
            'value' => Yii::t('app', 'Value'),
            'datetime' => Yii::t('app', 'Datetime'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharasteristic0() {
        return $this->hasOne(Charasteristic::className(), ['id' => 'charasteristic']);
    }

    public function getService0() {
        return $this->getCharasteristic0()->with('service0');
    }
    
    public function getAccessRule($identity=null) {
        return array('charasteristic' => $this->getCharasteristic0()->getAccessQuery($identity));
    }

    public function extraFields() {
        return ['charasteristic0', 'service0'];
    }

    public function import($subscriptions, $charasteristicsIndex) {
        $subscription=new SubscriptionData();
        $subscriptionCount = 0;
        $subscriptionModels = array();
        foreach ($subscriptions as $subscriptiondata) {
            if ($subscriptionDataModel = \app\models\SubscriptionData::find()->where(array(
                        'charasteristic' => $charasteristicsIndex,
                        'datetime' => $subscriptiondata['datetime'],
                        'value' => $subscriptiondata['value'],
                    ))->one()) {

                
                $subscriptiondata = array_merge($subscriptiondata, array('charasteristic' => $charasteristicsIndex));
                //die(print_r($subscriptiondata,true));
                $subscriptionDataModel->setattributes($subscriptiondata);
                $subscriptionModels[] = $subscriptionDataModel;
                //if ($subscriptionDataModel->save()) {
                //++$subscriptionCount;
                //} 
                /* else {
                  return $subscriptionDataModel->getErrors();
                  } */
            }
        }
        $subscription->saveAll($subscriptionModels);
        return sizeof($subscriptions);
    }
}
