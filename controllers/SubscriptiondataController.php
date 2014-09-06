<?php

namespace app\controllers;

use Yii;
use app\models\Device;
use yii\rest\ActiveController;

class SubscriptiondataController extends ActiveController {

    public $modelClass = 'app\models\SubscriptionData';

    /*
      public function importData() {
      return 'ok';
      } */

    public function actionCreate() {

        $data = $_POST['data'];
        $unzippedData = gzuncompress($data);
        
        $arrayData = json_decode($unzippedData, true);
        
        if(isset($arrayData['devices']))
        {
            foreach($arrayData['devices'] as $device)
            {
                if(isset($device['type']) && isset($device['address']))
                {
                    if(!$deviceModel = Device::find()->where(array(
                        'type'=>$device['type'], 
                        'address'=>$device['address'],
                        ))->one())
                    {
                        $deviceModel = new Device();
                    }
                }    
                $deviceModel->setAttributes($device);
                //TODO User needs to be pulled out of authorisation 
                $deviceModel->user=1;
                if($deviceModel->save())
                {
                    if(isset($device['services']))
                    {
                        foreach($device['services'] as $service)
                        {
                        
                        }
                    }
                }
                else
                {
                    return $deviceModel->getErrors();
                }
            }
        }
        
        return $arrayData;
        /*
        $model = new SubscriptionData();
        $model->attributes = $params;

        if ($model->save()) {

            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }*/
    }
/*
    public function runAction($id, $params = array()) {
        $params = array_merge($_POST, $params);
        parent::runAction($id, $params);
    }
*/
    public function actions() {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        // $actions['index']['importData'] = [$this, 'importData'];

        return $actions;
    }

}
