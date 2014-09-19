<?php

namespace app\lib\rest;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;

class XActiveController extends \yii\rest\ActiveController {
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
        $actions['view']['checkAccess'] = [$this, 'checkAccess'];
        $actions['update']['checkAccess'] = [$this, 'checkAccess'];
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
        'query' => $modelClass::find(),
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
    
    public function checkAccess( $action, $model = null, $params = [] ) {
        parent::checkAccess( $action, $model, $params );
        if($model && !$model->checkAccess(Yii::$app->user->identity))
            throw new \yii\web\ForbiddenHttpException('You do not have access');
    }
}
