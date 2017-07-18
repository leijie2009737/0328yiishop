<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //显示品牌页面
    public function actionIndex()
    {
        $models =Brand::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    //增加品牌
    public function actionAdd()
    {
        //var_dump(\Yii::getAlias('@webroot'));exit;
        $model =new Brand();
        $request =new Request();
        if($request->isPost) {
            //加载数据
            $model->load($request->post());
            //实例化文件上传对象
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');
            if ($model->validate()) {
                if ($model->logoFile) {
                    $dir = \Yii::getAlias('@webroot') . '/upload/brand' . date('Ymd');
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                    $fileName = '/upload/brand' . date('Ymd') . '/' . uniqid() . '.' . $model->logoFile->extension;
                    //保存上传文件
                    $model->logoFile->saveAs(\Yii::getAlias('@webroot') . $fileName, false);
                    $model->logo = $fileName;
                }
                //保存
                $model->save();
                //跳转到列表页
                return $this->redirect(['brand/index']);//当前控制器 index操作
            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //修改品牌
    public function actionEdit($id)
    {
        $model =Brand::findOne($id);
        $request =new Request();
        if($request->isPost) {
            //加载数据
            $model->load($request->post());
            //实例化文件上传对象
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');
            if ($model->validate()) {
                if ($model->logoFile) {
                    $dir = \Yii::getAlias('@webroot') . '/upload/brand' . date('Ymd');
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                    $fileName = '/upload/brand' . date('Ymd') . '/' . uniqid() . '.' . $model->logoFile->extension;
                    //保存上传文件
                    $model->logoFile->saveAs(\Yii::getAlias('@webroot') . $fileName, false);
                    $model->logo = $fileName;
                }
                //保存
                $model->save();
                //跳转到列表页
                return $this->redirect(['brand/index']);//当前控制器 index操作
            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //删除品牌
    public function actionDel($id)
    {
        $model =Brand::findOne($id);
        //var_dump(\Yii::getAlias('@webroot'). $model->logo);exit;
        //删除图片
        if ($model->logo) {
            unlink(\Yii::getAlias('@webroot') . $model->logo);
        }
        //删除该数据
        $model->delete();
        //跳转
        return $this->redirect(['brand/index']);
    }

}

