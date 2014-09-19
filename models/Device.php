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
