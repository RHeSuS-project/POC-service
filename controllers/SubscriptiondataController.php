<?php

namespace app\controllers;

use Yii;
use app\models\Device;
use app\models\Service;
use app\models\Charasteristic;
use app\Models\SubscriptionData;

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
        //TODO validation of DATA !!!!!!
        $arrayData = json_decode($unzippedData, true);
        
        if(isset($arrayData['devices']))
        {
            $subscriptionCount = 0;
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
                    $deviceIndex = $deviceModel['id'];
                    
                    if(isset($device['services']))
                    {
                        foreach($device['services'] as $service)
                        {

                            if(!$serviceModel = Service::find()->where(array(
                                'device'=>$deviceIndex,
                                'serviceUuid'=>$service['serviceUuid'],
                            ))->one())
                            {
                                $serviceModel = new Service();

                            }
                            $service = array_merge($service, array('device'=>$deviceIndex));
                            $serviceModel->setAttributes($service);

                            if($serviceModel->save())
                            {
                                $serviceIndex = $serviceModel['id'];
                                if(isset($service['charasteristics']))
                                {
                                    foreach ($service['charasteristics'] as $charasteristics) {
                                        if(!$charasteristicsModel = Charasteristic::find()->where(array(
                                            'service' => $serviceIndex,
                                            'charasteristicUuid'=> $charasteristics['charasteristicUuid'],
                                        ))->one())
                                        {
                                            $charasteristicsModel = new Charasteristic();
                                        }
                                        $charasteristics = array_merge($charasteristics, array('service'=>$serviceIndex));
                                        $charasteristicsModel->setattributes($charasteristics);
                                        if($charasteristicsModel->save())
                                        {
                                            $charasteristicsIndex = $charasteristicsModel['id'];
                                            //return $charasteristicsIndex;
                                            if(isset($charasteristics['subscriptions']))
                                            {
                                                foreach($charasteristics['subscriptions'] as $subscriptiondata)
                                                {
                                                    if(!$subscriptionModel = \app\models\SubscriptionData::find()->where(array(
                                                            //ubscriptionData::find()->where(array(
                                                        'charasteristic'=>$charasteristicsIndex,
                                                        'datetime'=>$subscriptiondata['datetime'],
                                                    ))->one())
                                                    {
                                                        $subscriptionDataModel = new \app\models\SubscriptionData();
                                                    }
                                                    $subscriptiondata = array_merge($subscriptiondata, array('charasteristic'=>$charasteristicsIndex));
                                                    $subscriptionDataModel->setattributes($subscriptiondata);
                                                    
                                                    if($subscriptionDataModel->save())
                                                    {
                                                        ++$subscriptionCount;
                                                    }
                                                    else
                                                    {
                                                        return $subscriptionDataModel->getErrors();
                                                    }
                                                }
                                                
                                            }

                                        }
                                        else
                                        {
                                            return $charasteristicsModel->getErrors();
                                        }
                                    }
                                }
                            }
                            else
                            {
                                return $serviceModel->getErrors();
                            }
                        }
                    }
                }
                else
                {
                    return $deviceModel->getErrors();
                }
            }
            return 'total number of subscriptions made:'.$subscriptionCount;
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
