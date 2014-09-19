<?php

namespace app\controllers;

use Yii;
use app\models\Device;
use app\models\Service;
use app\models\Charasteristic;

class SubscriptionController extends \app\lib\rest\XActiveController {

    public $modelClass = 'app\models\SubscriptionData';
    
    public function actionImport() {
        $data = $_POST['data'];
        $unzippedData = gzuncompress($data);
        //TODO validation of DATA !!!!!!
        $arrayData = json_decode($unzippedData, true);
        //return (print_r($arrayData));
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
                $identity = Yii::$app->user->identity;
                $deviceModel->user = $identity->id;
                //return $identity->id;
                if($deviceModel->save())
                {
                    $deviceIndex = $deviceModel->getPrimaryKey();
                    //return $deviceIndex;
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
                                $serviceIndex = $serviceModel->getPrimaryKey();
                                
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
                                            $charasteristicsIndex = $charasteristicsModel->getPrimaryKey();
                                            //return $charasteristicsIndex;
                                            if(isset($charasteristics['subscriptions']))
                                            {
                                                foreach($charasteristics['subscriptions'] as $subscriptiondata)
                                                {
                                                    if(!$subscriptionModel = \app\models\SubscriptionData::find()->where(array(
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
    }
}