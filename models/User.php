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
class User extends \app\lib\db\XActiveRecord implements \yii\web\IdentityInterface, \yii\filters\RateLimitInterface
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
            [['username', 'password', 'authkey', 'access_token', 'email_address','salt'], 'string', 'max' => 255]
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
            'salt' => Yii::t('app', 'Salt'),
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
        if($type=='yii\filters\auth\HttpBasicAuth')
        {
            return User::validateLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        }
        return static::findOne(['access_token' => $token]);
    }
    
    public function validateLogin($username, $password){
        //die('validatelogin:'.$password);
        
        if ($salt = User::getSaltbyUsername($username)){
            $pepper = Yii::$app->params["pepper"];
            $saltedPasswordHash =  hash('sha256', $salt.$pepper.$password);
            return static::findOne(['username' => $username,'password'=> $saltedPasswordHash]);
        }
        return false;
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
        if ($salt = User::getSaltbyUsername($this->username)){
            $pepper = Yii::$app->params["pepper"];
            $saltedPasswordHash =  hash('sha256', $salt.$pepper.$password);        
            return $this->password === $saltedPasswordHash;
        }
        return false;
    }
    
    public function fields()
    {
        $fields = parent::fields();
        unset(
                $fields['authkey'],
                $fields['password'],
                $fields['access_token'],
                $fields['salt']
                );
        return $fields;
    }
    
    public function getAccessRule($identity=null) {
        if(!$identity)
            $identity=Yii::$app->user->identity;
        return array('id'=>$identity->getAccessQuery());
    }
    
    public function getAccessQuery($identity=null) {
        $subquery=(new \yii\db\Query())->select('user')->from('supervisor')->where(array('supervisor'=>$this->id));
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
    
    public function loadAllowance($request, $action) {
        $rateLimit=$this->getRateLimit($request, $action);
        //die($rateLimit[1].' ');
        $allowance=Allowance::find()->where(array('and', ['>','time',(time()-$rateLimit[1])], 'user'=>Yii::$app->user->identity->id))->one();
        $allowed=$rateLimit[0];
        if($allowance)
            $allowed=$allowance->allowance;
        
        return array($allowed, time());
    }
    
    public function getRateLimit( $request, $action){
        return array(Yii::$app->params['rateLimit']['limit'],Yii::$app->params['rateLimit']['time']);
    }
    
    public function saveAllowance($request, $action, $allowance, $timestamp) {
        $rateLimit=$this->getRateLimit($request, $action);
        //die(' '.(1413035935-(time()-$rateLimit[1])) );
        if(!$model=Allowance::find()->where(array('user'=>Yii::$app->user->identity->id))->one())
        {
                $model=new Allowance();
                $model->user=Yii::$app->user->identity->id;
        }
        if($model->time<=(time()-$rateLimit[1]))
            $model->time=$timestamp;
        $model->allowance=$allowance;
        $model->save();
    }
    
    public function getSaltbyUsername($username){
        try {
            $salt = User::find()->where(['username' => $username])->one()->salt;
            return $salt;
            } catch (\Exception $e) {
                return false;
            }
            //die($salt);
    }
    
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord) {
                if (Yii::$app->user->can('createUser')) {
                    $this->salt = uniqid(mt_rand(), true);
                    $pepper = Yii::$app->params["pepper"];
                    $this->password = hash('sha256', $this->salt.$pepper.$this->password); 

                }

            }
        }
        return true;
    }
}
