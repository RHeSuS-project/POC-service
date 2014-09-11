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
                // add "createDevice" permission
                $createDevice = $auth->createPermission('createDevice');
                $createDevice->description = 'Create a device';
                $auth->add($createDevice);

                // add "updateDevice" permission
                $updateDevice = $auth->createPermission('updateDevice');
                $updateDevice->description = 'Update device settings';
                $auth->add($updateDevice);
                
                // add "deleteDevice" permission
                $deleteDevice = $auth->createPermission('deleteDevice');
                $deleteDevice->description = 'delete a device';
                $auth->add($deleteDevice);

                // add "createUser" permission
                $createUser = $auth->createPermission('UserDevice');
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
                
                // add "patient" role and give this role the "createDevice" permission
                $patient = $auth->createRole('patient');
                $auth->add($patient);
                $auth->addChild($patient, $createDevice);

                // add "doctor" role 
                $doctor = $auth->createRole('doctor');
                $auth->add($doctor);
                $auth->addChild($doctor, $patient);
                
                // add "admin" role and give this role the "updateDevice" permission
                // as well as the permissions of the "patient" role
                $admin = $auth->createRole('admin');
                $auth->add($admin);
                $auth->addChild($admin, $updateDevice);
                $auth->addChild($admin, $deleteDevice);
                $auth->addChild($admin, $patient);

                // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
                // usually implemented in your User model.
                $auth->assign($patient, 2);
                $auth->assign($admin, 1);
        }

}
