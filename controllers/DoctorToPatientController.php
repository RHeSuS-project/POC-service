<?php

namespace app\models\rbac\controllers;

class DoctorToPatientController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
