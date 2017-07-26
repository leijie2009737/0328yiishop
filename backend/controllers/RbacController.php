<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
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
}
