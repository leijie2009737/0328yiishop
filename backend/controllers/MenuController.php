<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use cebe\markdown\MarkdownExtra;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MenuController extends \yii\web\Controller
{
    /*
     *菜单列表显示
     */
    public function actionIndex()
    {
        $models=Menu::find()->where('parent_id=0')->all();
        return $this->render('index',['models'=>$models]);
    }


    /*
     *添加菜单
     */
    public function actionAdd()
    {
        $model =new Menu();
        //菜单上级分类
        $models =Menu::find()->where('parent_id=0')->all();
//        $models[]=['id'=>0,'name'=>'顶级分类'];
        $models=\yii\helpers\ArrayHelper::merge(['0'=>'顶级分类'],ArrayHelper::map($models,'id','name'));
        //url获取
        $authManager =\Yii::$app->authManager;
//        $authManager=$authManager->getPermissions();
//        $authManager=ArrayHelper::map($authManager,'name','name');
        if($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            //保存菜单信息
            $model->save();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect('index');
        }

        return $this->render('add',['model'=>$model,'models'=>$models,/*'authManager'=>$authManager*/]);
    }


    /*
     *修改菜单
     */
    public function actionEdit($id)
    {
        $model =Menu::findOne(['id'=>$id]);
        $models =Menu::find()->where('parent_id=0')->all();
        $models=\yii\helpers\ArrayHelper::merge(['0'=>'顶级分类'],ArrayHelper::map($models,'id','name'));
        if($model==null){
            //输入id不存在，提示404
            throw new \yii\web\HttpException(404, '用户不存在');
        }else{
            if($model->load(\Yii::$app->request->post()) && $model->validate())
            {
                //保存菜单信息
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('index');
            }
        }
        return $this->render('add',['model'=>$model,'models'=>$models]);
    }


    /*
     *删除菜单
     */
    public function actionDel($id)
    {
        $model =Menu::findOne(['id'=>$id]);
        if($model==null){
            //输入id不存在，提示404
            throw new \yii\web\HttpException(404, '用户不存在');
        }else{
            //判断是否有下级菜单
            if($model->children){
                \Yii::$app->session->setFlash('danger','有下级菜单不能删除');
                return $this->redirect('index');
            }
            $model->delete();
        }
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect('index');

    }


    //测试生成的菜单
    public function actionTest()
    {
        $model=Menu::getMenus();
        var_dump($model);exit;
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
