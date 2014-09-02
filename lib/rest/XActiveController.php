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
        
        $behaviors['contentNegotiator']['formats']['application/json'] = isset($_GET['callback'])?\yii\web\Response::FORMAT_JSONP:\yii\web\Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/jsonp'] = \yii\web\Response::FORMAT_JSONP;
        
        return $behaviors;
    }
    
    public function checkAccess( $action, $model = null, $params = [] ) {
        parent::checkAccess( $action, $model, $params );
        if($model && !$model->checkAccess(Yii::$app->user->identity))
            throw new \yii\web\ForbiddenHttpException('You do not have access');
    }
    public function afterAction($action, $result){
        $result=parent::afterAction($action, $result);
        
        if(Yii::$app->response->format==\yii\web\Response::FORMAT_JSONP)
        {
            if(isset($_GET['callback']))
            {
                $result=array('callback'=>$_GET['callback'], 'data'=>$result);
            }
        }
        
        return $result;
    }
}
