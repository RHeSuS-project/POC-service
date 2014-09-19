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
    
    public function getAccessRule($identity=null) {
        return array();
    }
    
    public function getAccessQuery($identity) {
        $query=(new \yii\db\Query())->select('id')->from($this->tableName())->where($this->getAccessRule($identity));
        return $query;
    }
    
    public function checkAccess($identity) {
        if($this->find()->Where($this->getAccessRule($identity))->andWhere($this->getPrimaryKey(true))->one())
            return true;
        return false;
    }
}