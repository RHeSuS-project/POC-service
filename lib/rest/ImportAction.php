<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\lib\rest;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * @author Philip Verbist <philip.verbist@gmail.com>
 */
class ImportAction extends Action {

    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $indexAction = 'index';

    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws \Exception if there is any error when creating the model
     */
    public function run() {
        die();
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->indexAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }
    
    
    public function import() {
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
