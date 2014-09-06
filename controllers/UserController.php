<?php

namespace app\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['auth_key'], $fields['password'], $fields['accessToken']);
        return $fields;
    }


}