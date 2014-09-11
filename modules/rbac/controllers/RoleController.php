<?php

namespace app\models\rbac\controllers;

class RoleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
