<?php

namespace app\modules\rbac\rules;

use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class UpdateDataPatientRule extends Rule
{
    public $name = 'ownData';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        /* For now we will check only on device for the correct permissions*/
        return isset($params['device']) ? $params['device']->user == $user : false;
    }
}