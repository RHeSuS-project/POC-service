<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;

class DeviceController extends ActiveController
{
    public $modelClass = 'app\models\Device';
    public $prepareDataProvider;
/*
    public function actionIndex()
    {
        $identity = Yii::$app->user->identity;
        $user_id = $identity->id;
        $modelClass = $this->modelClass;
        if ($deviceModel = \app\models\Device::find()->where(array(
                        'user'=>$user_id,
                        ))->all())
        {
            return $deviceModel;
        }
        else
        {
            //TODO correct Errorhandling !! 
            return $deviceModel->getErrors();
        }

    }
    public function actionView($id)
    {
        $identity = Yii::$app->user->identity;
        return $identity;
    }
*/    
    public function actions() {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset(
                $actions['create'],
                $actions['delete'],
                $actions['options']
             );

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
    
    public function prepareDataProvider()
    {
        // prepare and return a data provider for the "index" action
        if ($this->prepareDataProvider !== null) {
        return call_user_func($this->prepareDataProvider, $this);
        }
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        $identity = Yii::$app->user->identity;
        $user_id = $identity->id;
        //die(print_r($modelClass::find()));        
        return new ActiveDataProvider([
        'query' => $modelClass::find()->where(array(
                        'user'=>$user_id,
                        )),
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }
    /*
    public function fields()
    {
        return [
            // field name is the same as the attribute name
            'id',
            // field name is "email", the corresponding attribute name is "email_address"
            'type' => 'device_type',
            // field name is "name", its value is defined by a PHP callback
            'name' => function () {
                return ' Devicename: '.$this->name;
            },
            'user' => Yii::$app->user->identity,
        ];
    }*/


}
