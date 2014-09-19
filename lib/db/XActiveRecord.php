<?php
use XActiveQuery;
namespace app\lib\db;
class XActiveRecord extends \yii\db\ActiveRecord {
    /**
    * @inheritdoc
    * @return ActiveQuery the newly created [[ActiveQuery]] instance.
    */
    public static function find()
    {
        return \Yii::createObject(XActiveQuery::className(), [get_called_class()]);
    }
    
    public function getAccessRule() {
        return array();
    }
    
    public function checkAccess($identity) {
        if($this->find()->Where($this->getAccessRule($identity))->andWhere(array('id'=>$this->id))->one())
            return true;
        return false;
    }
}