<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //显示品牌页面
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Brand::find();
        //总条数
        $total = $query->where(['!=','status','-1'])->count();
        //var_dump($total);exit;
        //每页显示条数 2
        $perPage = 2;
        //分页工具类
        $page = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);

        //LIMIT 0,3   ==> limit(3)->offset(0)
        $models = $query->limit($page->limit)->offset($page->offset)->all();

        return $this->render('index',['models'=>$models,'page'=>$page]);
//        $models =Brand::find()->where(['!=','status','-1'])->all();
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
                if($model->save()){
                    //输出保存成功
                    \yii::$app->session->setFlash('success','保存成功!');
                    //跳转到列表页
                    return $this->redirect(['brand/index']);//当前控制器 index操作
                }
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
                if($model->save()){
                    //输出修改成功
                    \yii::$app->session->setFlash('success','修改成功!');
                    //跳转到列表页
                    return $this->redirect(['brand/index']);//当前控制器 index操作
                }
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
//        $model =Brand::findOne($id);
//        //var_dump(\Yii::getAlias('@webroot'). $model->logo);exit;
//        //删除图片
//        if ($model->logo) {
//            unlink(\Yii::getAlias('@webroot') . $model->logo);
//        }
//        //删除该数据
//        $model->delete();
//        //跳转
//        return $this->redirect(['brand/index']);

        //逻辑删除
        $model =Brand::findOne($id);
        $model->status=-1;
        //保存
        if($model->save()){
            //输出删除成功
            \yii::$app->session->setFlash('success','删除成功!');
            //跳转到列表页
            return $this->redirect(['brand/index']);//当前控制器 index操作
        }
    }

}

