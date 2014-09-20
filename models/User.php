<?php

namespace app\models;

use \Yii;
/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $authkey
 * @property string $access_token
 * @property string $email_address
 *
 * @property Device[] $devices
 */
class User extends \app\lib\db\XActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email_address'], 'required'],
            [['username', 'password', 'authkey', 'access_token', 'email_address'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'authkey' => Yii::t('app', 'Authkey'),
            'access_token' => Yii::t('app', 'Access Token'),
            'email_address' => Yii::t('app', 'Email Address'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['user' => 'id']);
    }   
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //die($token);
        //die(static::findOne(['access_token' => $token]));
        return static::findOne(['access_token' => $token]);
    }
    
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
    
    public function fields()
    {
        $fields = parent::fields();
        unset(
                $fields['authkey'],
                $fields['password'],
                $fields['access_token']
                );
        return $fields;
    }
    
    public function getAccessRule($identity=null) {
        if(!$identity)
            $identity=Yii::$app->user->identity;
        return array('id'=>$identity->getAccessQuery());
    }
    
    public function getAccessQuery($identity=null) {
        $subquery=(new \yii\db\Query())->select('patient')->from('doctor_to_patient')->where(array('doctor'=>$this->id));
        $query=(new \yii\db\Query())->select('id')->from('user')->where(array('id'=>$this->id))->orWhere(array('id'=>$subquery));
        return $query;
    }
    
    public function getCheckAccessQuery($identity) {
        return $identity->getAccessQuery()->andWhere($this->getPrimaryKey(true));
    }
    
    public function checkAccess($identity) {
        if($this->getCheckAccessQuery($identity)->one())
            return true;
        return false;
    }
}
