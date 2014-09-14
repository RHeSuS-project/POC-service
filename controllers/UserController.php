<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    public $prepareDataProvider;
    
    public function actions() 
    {
        $actions = parent::actions();

        // disable the "delete", "options" and "create" actions
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
                        'id'=>$user_id,
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
}