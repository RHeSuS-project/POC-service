<?php

namespace app\lib\rest;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\data\ActiveDataProvider;

class XActiveController extends \yii\rest\ActiveController {
    public $prepareDataProvider;
    
    public function actions() 
    {
        $actions = parent::actions();

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['checkAccess'] = [$this, 'checkAccess'];
        $actions['update']['checkAccess'] = [$this, 'checkAccess'];
        $actions['create']['checkAccess'] = [$this, 'checkAccess'];
        $actions['delete']['checkAccess'] = [$this, 'checkAccess'];
        return $actions;
    }
    
    protected function verbs() {
        $verbs = parent::verbs();
        /*
         * We want to override the default verbs, because otherwise OPTIONS don't work.
         * Somehow the Yii2 documentation didn't describe reality. 
         * This fixes one of those issues.
         */
        $verbs['options'] = ['OPTIONS'];
        return $verbs;
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
        /*
         * The W3 spec for CORS preflight requests clearly states that user credentials should be excluded. 
         * There is a bug in Chrome and WebKit where OPTIONS requests returning a status of 401 still send 
         * the subsequent request.
         *
         * Firefox has a related bug filed that ends with a link to the W3 public webapps mailing list asking 
         * for the CORS spec to be changed to allow authentication headers to be sent on the OPTIONS request 
         * at the benefit of IIS users. Basically, they are waiting for those servers to be obsoleted.
         * 
         * How can I get the OPTIONS request to send and respond consistently?
         * 
         * Simply have the server (API in this example) respond to OPTIONS requests without requiring authentication. 
         */
        $behaviors['access'] = [
                'class' => AccessControl::className(),
                'only' => ['options'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => '?',
                    ],
                ]
            ];
        $behaviors['contentNegotiator']['formats']['application/json'] = isset($_GET['callback'])?\yii\web\Response::FORMAT_JSONP:\yii\web\Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/jsonp'] = \yii\web\Response::FORMAT_JSONP;
        
        return $behaviors;
    }
    
    public function checkAccess( $action, $model = null, $params = [] ) {
        parent::checkAccess( $action, $model, $params );
        if( $model && !$model->checkAccess(Yii::$app->user->identity))
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
        /*
         * CORS requires some headers in order for the requests to work. 
         * These are current working headers for the app.
         * 
         * We may want to move this to a better location, or maybe change 
         * the entire Header-logic.
         */
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Credentials', 'true');
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', Yii::$app->request->getHeaders()->get('Origin'));
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Headers', 'Authorization');
        $headers=implode(',',array_keys(Yii::$app->response->getHeaders()->toArray()));
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Expose-Headers', $headers);
        return $result;
    }
}
