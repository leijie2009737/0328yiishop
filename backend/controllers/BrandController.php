<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    //显示品牌页面
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Brand::find();
        //总条数
        //$total = $query->where(['!=','status','-1'])->count();
        $total = $query->where(['>','status',-1])->orderBy('sort desc')->count();
        //var_dump($total);exit;
        //每页显示条数 5
        $perPage = 5;
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


    //图片上传到七牛云和uploadfive
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(), $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl']  = $url;
                },
            ],
        ];
    }


    //七牛云文件上传
    public function actionQiniu()
    {

        $config = [
            'accessKey'=>'q9_5AT9EB7j15amMdaithQjZ9laxY6boLziM3RVp',
            'secretKey'=>'yOg0SP9zBe5dQxzfUv-SzlUjuiktwQo3rO97SfhX',
            'domain'=>'http://otbjurh1u.bkt.clouddn.com/',
            'bucket'=>'yiishop',
            'area'=>Qiniu::AREA_HUADONG
        ];


        $qiniu = new Qiniu($config);
        $key = 'upload/e4/30/e430e732cc6744dbfc6b6adb5b3c0b6450a4bbd8.jpg';

        //将图片上传到七牛云
        $qiniu->uploadFile(
            \Yii::getAlias('@webroot').'/upload/e4/30/e430e732cc6744dbfc6b6adb5b3c0b6450a4bbd8.jpg',
            $key);
        //获取该图片在七牛云的地址
        $url = $qiniu->getLink($key);
        var_dump($url);
    }


    /*
     *过滤器行为
     */
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['brand/index','brand/add','brand/edit','brand/del'],
            ]
        ];
    }
}

