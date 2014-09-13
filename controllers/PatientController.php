<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;


class PatientController extends ActiveController
{
    public $modelClass = 'app\models\DoctorToPatient';
 
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        // $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
    //TODO check permissions (check if has patientPermissions)
    //TODO actionIndex only needs to return patients for current doctor OR user self;
    public function fields()
    {
        $fields = parent::fields();
        //unset($fields['auth_key'], $fields['password'], $fields['access_token']);
        return $fields;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }
}