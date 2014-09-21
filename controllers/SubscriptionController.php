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
        return $verbs;
    }
    
    public function actionImport()
    {
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
}
