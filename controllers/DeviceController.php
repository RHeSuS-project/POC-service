<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

class DeviceController extends ActiveController
{
    public $modelClass = 'app\models\Device';

    

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }


}
