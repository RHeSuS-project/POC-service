<?php

namespace app\models\rbac\controllers;

class PermissionController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
