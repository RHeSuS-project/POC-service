<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
//namespace console\controllers;
use yii\console\Controller;


class MyController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionInit()
    {
        $message = 'hello world';
        echo $message . "\n";
    }
    
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }
}
