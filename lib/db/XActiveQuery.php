<?php
namespace app\lib\db;

class XActiveQuery extends \yii\db\ActiveQuery  {
    public function getAccessRule() {
        $class = $this->modelClass;
        $model= new $class;
        return $model->getAccessRule();
    }
    
    public function getAccessQuery() {
        $modelClass=$this->modelClass;
        $query=(new \yii\db\Query())->select('id')->from($modelClass::tableName())->where($this->getAccessRule());
        return $query;
    }
    
    public function all($db = null)
    {
        $rows = $this->andWhere($this->getAccessRule())->createCommand($db)->queryAll();
        return $this->populate($rows);
    }
    /*public function one($db = null)
    {
        return $this->andWhere($this->getAccessRule())->createCommand($db)->query();
    }*/
}