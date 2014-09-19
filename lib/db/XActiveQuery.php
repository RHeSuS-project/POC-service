<?php
namespace app\lib\db;

class XActiveQuery extends \yii\db\ActiveQuery  {
    public function getAccessRule() {
        $class = $this->modelClass;
        return $class::getAccessRule();
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