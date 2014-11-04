<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $address
 * @property integer $user
 *
 * @property User $user0
 * @property Service[] $services
 */
class Device extends \app\lib\db\XActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'address', 'user'], 'required'],
            [['user'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'address' => Yii::t('app', 'Address'),
            'user' => Yii::t('app', 'User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['device' => 'id']);
    }
    
    public function extraFields()
    {
        return ['user0','services'];
    }
    
    public function getAccessRule($identity=null) {
        if(!$identity)
            $identity=Yii::$app->user->identity;
        return array('user' => $identity->getAccessQuery());
    }
    
    public function import($devices) {
        $subscriptionCount = 0;
        foreach ($devices as $device) {
            if (isset($device['type']) && isset($device['address'])) {
                if (!$deviceModel = Device::find()->where(array(
                            'type' => $device['type'],
                            'address' => $device['address'],
                        ))->one()) {
                    $deviceModel = new Device();
                }
            }
            $deviceModel->setAttributes($device);
            $identity = Yii::$app->user->identity;
            $deviceModel->user = $identity->id;
            //return $identity->id;
            if ($deviceModel->save()) {
                $deviceIndex = $deviceModel->getPrimaryKey();
                //return $deviceIndex;
                if (isset($device['services'])) {
                    //die(print_r($device['services'],true));
                    $subscriptionCount+=Service::import($device['services'], $deviceIndex);
                }
            } /*else {
                return $deviceModel->getErrors();
            }*/
            return $subscriptionCount;
        }
    }
/*   
    public function fields()
    {
        return [
            'id',
            'type',
            'name',
            'address',
            'user',
        ];
    }
*/
}
