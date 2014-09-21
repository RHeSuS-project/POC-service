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
    
    public function saveAll($models, $runValidation = true, $attributeNames = null) {
        ini_set('memory_limit','5G');
        $db = static::getDb();
        $sql='';
        if ($this->isTransactional(self::OP_INSERT)) {
            $transaction = $db->beginTransaction();
        }
        $i=0;
        foreach ($models as $model) {
            if ($model->getIsNewRecord()) {
                if ($runValidation && !$model->validate($attributeNames)) {
                    Yii::info('Model not inserted due to validation error.', __METHOD__);
                }
                if (!$model->beforeSave(true)) {
                    return false;
                }
                $values = $model->getDirtyAttributes($attributeNames);
                if (empty($values)) {
                    foreach ($model->getPrimaryKey(true) as $key => $value) {
                        $values[$key] = $value;
                    }
                }
                $command = $db->createCommand()->insert($model->tableName(), $values);
                    
                $rawSql=$command->getRawSql();
                $sql.=$rawSql.';';
            } else {
                if ($runValidation && !$model->validate($attributeNames)) {
                    Yii::info('Model not updated due to validation error.', __METHOD__);
                }
                if (!$model->beforeSave(true)) {
                    return false;
                }
                $values = $model->getDirtyAttributes($attributeNames);
                if (empty($values)) {
                    foreach ($model->getPrimaryKey(true) as $key => $value) {
                        $values[$key] = $value;
                    }
                }
                $command = $db->createCommand()->update($model->tableName(), $values);
                    
                $rawSql=$command->getRawSql();
                $sql.=$rawSql.';';
            }
            $i++;
            if($i>250)
            {
                $db->createCommand()->setSQL($sql)->execute();
                $sql='';
                $i=0;
            }
        }
        if($i)
            $db->createCommand()->setSQL($sql)->execute();

        if ($this->isTransactional(self::OP_INSERT)) {
            try {
                $result = $this->insertInternal($attributes);
                if ($result === false) {
                    $transaction->rollBack();
                } else {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }
}