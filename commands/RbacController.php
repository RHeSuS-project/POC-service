<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionIndex()
        {
                $auth = Yii::$app->getAuthManager();
                // Clean everything
                $auth->removeAll();
                // add "createData" permission
                $createData = $auth->createPermission('createData');
                $createData->description = 'Create data and everything else';
                $auth->add($createData);

                // add "readData" permission
                $readData = $auth->createPermission('readData');
                $readData->description = 'Read data and others';
                $auth->add($readData);
                
                // add "updateData" permission
                $updateData = $auth->createPermission('updateData');
                $updateData->description = 'Update data and other settings';
                $auth->add($updateData);
                
                // add "deleteData" permission
                $deleteData = $auth->createPermission('deleteData');
                $deleteData->description = 'Delete data and others';
                $auth->add($deleteData);

                // add "createUser" permission
                $createUser = $auth->createPermission('createUser');
                $createUser->description = 'Create a User';
                $auth->add($createUser);

                // add "updateUser" permission
                $updateUser = $auth->createPermission('updateUser');
                $updateUser->description = 'Update user settings';
                $auth->add($updateUser);
                
                // add "deleteUser" permission
                $deleteUser = $auth->createPermission('deleteUser');
                $deleteUser->description = 'delete a user';
                $auth->add($deleteUser);
                
                // add "patient" role and give this role the "createData" permission
                $patient = $auth->createRole('patient');
                $auth->add($patient);
                $auth->addChild($patient, $createData);

                // add "doctor" role 
                $doctor = $auth->createRole('doctor');
                $auth->add($doctor);
                $auth->addChild($doctor, $patient);
                
                // add "admin" role and give this role the "updateData" permission
                // as well as the permissions of the "patient" role
                $admin = $auth->createRole('admin');
                $auth->add($admin);
                $auth->addChild($admin, $updateData);
                $auth->addChild($admin, $deleteData);
                $auth->addChild($admin, $readData);
                $auth->addChild($admin, $patient);
                $auth->addChild($admin, $doctor);

                // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
                // usually implemented in your User model.
                $auth->assign($patient, 2);
                $auth->assign($admin, 1);
                $auth->assign($doctor,3);
                
                // add the rule
                $rule = new \app\modules\rbac\rules\UpdateDataPatientRule;
                $auth->add($rule);

                // add the "updateDataPatient" permission and associate the rule with it.
                $updateDataPatient = $auth->createPermission('updateDataPatient');
                $updateDataPatient->description = 'Update own data';
                $updateDataPatient->ruleName = $rule->name;
                $auth->add($updateDataPatient);

                // "updateDataPatient" will be used from "updatePost"
                $auth->addChild($updateDataPatient, $updateData);

                // allow "author" to update their own posts
                $auth->addChild($patient, $updateDataPatient);
        }

}
