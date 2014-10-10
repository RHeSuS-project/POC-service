<?php

namespace app\controllers;

use Yii;
use app\models\Device;
use app\models\Service;
use app\models\Charasteristic;

class SubscriptionController extends \app\lib\rest\XActiveController {

    public $modelClass = 'app\models\SubscriptionData';
    
    public function actions() 
    {
        $actions = parent::actions();

        /*$actions['import'] = array(
            'class' => 'app\lib\rest\ImportAction',
            //'modelClass' => $this->modelClass,
            //'checkAccess' => [$this, 'checkAccess'],
            //'scenario' => $this->createScenario,
        );*/
        
        return $actions;
    }
    
    public function verbs() {
        $verbs=parent::verbs();
        $verbs['import']=['POST'];
        $verbs['graphdata']=['GET'];
        return $verbs;
    }
    
    public function prepareDataProvider() {
        $dataProvider=parent::prepareDataProvider();
        
        $start=time()-3600;
        $startTime=Yii::$app->getRequest()->get('startTime');
        if($startTime)
        {
            $start=strtotime($startTime);
            $dataProvider->query->andWhere(['>=','dateTime',$start*1000]);
        }
        $end=time();
        $endTime=Yii::$app->getRequest()->get('endTime');
        if($endTime)
        {
            $end=strtotime($endTime);
            $dataProvider->query->andWhere(['<=','dateTime',$end*1000]);
        }
        $charasteristic=Yii::$app->getRequest()->get('charasteristic');
        if($charasteristic)
        {
            $dataProvider->query->andWhere(['charasteristic'=>$charasteristic]);
        }
        $totalPoints=Yii::$app->getRequest()->get('totalPoints');
        if(is_numeric($totalPoints) && $totalPoints>0 && $end*1000-$start*1000>0)
        {
            $dataProvider->query->groupBy(array('(ROUND((datetime)/'.(($end*1000-$start*1000)/$totalPoints).'))'));
        }
        
        return $dataProvider;
    }
    
    public function actionImport()
    {
        ini_set('memory_limit','5G');
        if(isset($_POST['data']))
            $data = $_POST['data'];
        else
            $data = gzcompress(json_encode(array()));
        $unzippedData = gzuncompress($data);
        $arrayData = json_decode($unzippedData, true);
        if(isset($arrayData['devices']))
        {
            $subscriptionCount=Device::import($arrayData['devices']);
            return 'total number of subscriptions made:'.$subscriptionCount;
        }
        return $arrayData;
    }
    
    public function actionGraphdata($charasteristic, $startTime=null, $endTime=null, $period=null)
    {
        ini_set('memory_limit','5G');
        $start=time()-24*3600;
        if($startTime)
            $start=strtotime($startTime);
        $start*=1000;
        $end=time();
        if($endTime)
            $end=strtotime($endTime);
        $end*=1000;
        $modelclass=$this->modelClass;
        return $modelclass::find()->andWhere(['>=', 'datetime',$start,])->andWhere(['<=', 'datetime',$end,])->all();
    }
}