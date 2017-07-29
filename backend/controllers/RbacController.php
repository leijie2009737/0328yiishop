<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
    #####################  权限的基本操作   #################################
    /*
     * 权限列表
     */
    public function actionPerIndex()
    {
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getPermissions();
        return $this->render('per-index',['models'=>$models]);
    }

    /*
     * 增加权限
     */
    public function actionAddPermission()
    {
        $model = new PermissionForm();
        //指定场景
        $model->scenario=PermissionForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $authManager = \Yii::$app->authManager;
            //创建权限
            $permission= $authManager->createPermission($model->name);
            $permission->description=$model->description;
            //保存权限
            $authManager->add($permission);
            \Yii::$app->session->setFlash('success','权限添加成功');
            return $this->redirect(['per-index']);
        }
        return $this->render('per-add',['model'=>$model]);
    }

    /*
     * 修改权限
     */
    public function actionEditPermission($name)
    {
        $model = new PermissionForm();
        //判断权限是否存在
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
        //是否提交数据
        if(\Yii::$app->request->isPost){
            //是否通过验证
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //给全新赋值
                $permission->name =$model->name;
                $permission->description=$model->description;
                //更新权限
                $authManage->update($name,$permission);
                \Yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect(['per-index']);
            }
        }else{
            //回显数据
            $model->name = $permission->name;
            $model->description = $permission->description;
        }
        return $this->render('per-add',['model'=>$model]);
    }

    /*
     * 移除权限
     */
    public function actionDelPermission($name)
    {
        $authManager=\Yii::$app->authManager;
        $removeone = $authManager->getPermission($name);
//        var_dump($removeone);exit;
        $authManager->remove($removeone);
        \Yii::$app->session->setFlash('success','权限删除成功');
        return $this->redirect(['per-index']);
    }
    #####################   角色的基本操作   #################################
    /*
     *角色列表
     */
    public function actionRoleIndex()
    {
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getRoles();
        return $this->render('role-index',['models'=>$models]);
    }
    /*
     *角色添加
     */
    public function actionAddRole()
    {
        $model= new RoleForm();
        $model->scenario=RoleForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //创建角色
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            //添加角色
//            var_dump($model);exit;
            $authManager->add($role);
            //给角色赋予权限
            if(is_array($model->permissions)){
                //存在权限
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
            \Yii::$app->session->setFlash('success','角色添加成功');
            return $this->redirect(['role-index']);

        }
        return $this->render('role-add',['model'=>$model]);
    }

    /*
     *角色修改
     */
    public function actionEditRole($name)
    {
        $model= new RoleForm();
        //找到指定的角色
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //取消角色下面的所关联
            $authManager->removeChildren($role);
            //再新增关联
            if(is_array($model->permissions)){
                //存在权限
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
//            $role->name =$model->name;
            $role->description=$model->description;
            //更新权限
            $authManager->update($name,$role);
            \Yii::$app->session->setFlash('success','角色修改成功');
            return $this->redirect(['role-index']);
        }
        //表单权限多选回显
        //获取角色的权限
        $permissions = $authManager->getPermissionsByRole($name);
        $model->name = $role->name;
        $model->description = $role->description;
        $model->permissions = ArrayHelper::map($permissions,'name','name');
        return $this->render('role-add',['model'=>$model]);

    }

    /*
     *角色删除
     */
    public function actionDelRole($name)
    {
        $authManager=\Yii::$app->authManager;
        $removeone = $authManager->getRole($name);
        $authManager->remove($removeone);
        \Yii::$app->session->setFlash('success','角色删除成功');
        return $this->redirect(['role-index']);
    }

    /*
 *过滤器行为
 */
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
