<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\web\Request;

class GoodsController extends \yii\web\Controller
{
    //显示页面
    public function actionIndex()
    {
        $query = Goods::find();
        //总条数
        //$total = $query->where(['!=','status','-1'])->count();
        $total = $query->where(['>','status',0])->orderBy('id asc')->count();
        //var_dump($total);exit;
        //每页显示条数 10
        $perPage = 10;
        //分页工具类
        $page = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);

        //LIMIT 0,3   ==> limit(3)->offset(0)
        $models = $query->limit($page->limit)->offset($page->offset)->all();

        return $this->render('index',['models'=>$models,'page'=>$page]);
    }


    //增加商品
    public function actionAdd()
    {
        $category = new GoodsCategory();
        //获取商品分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //品牌分类
        $brands = Brand::find()->all();
        //商品详情
        $goods_intro= new GoodsIntro();
        $model = new Goods();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            $goods_intro->load($request->post());
//            var_dump($model);
//            var_dump($goods_intro);exit;
            if ($model->validate() && $goods_intro->validate()) {
                $sn= GoodsDayCount::getGoodsSn();
//              var_dump($sn);exit;
                $model->sn=$sn;
                //默认商品状态正常
                $model->status=1;
                $model->save();
                //保存商品的详情
                $goods_intro->goods_id=$model->id;
                $goods_intro->save();

            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
            //输出保存成功
            \yii::$app->session->setFlash('success','添加成功!');
            //跳转到列表页
            return $this->redirect(['goods/index']);
        }
        return $this->render('add',['model'=>$model,'category'=>$category,'categories'=>$categories,'brands'=>$brands,'goods_intro'=>$goods_intro]);
    }


    //修改商品
    public function actionEdit($id)
    {
       $model = Goods::findOne($id);

        $category = GoodsCategory::findOne($model->goods_category_id);
        //获取商品分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //品牌分类
        $brands = Brand::find()->all();
        //商品详情
        $goods_intro= GoodsIntro::findOne($id);

        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            $goods_intro->load($request->post());
//            var_dump($model);
//            var_dump($goods_intro);exit;
            if ($model->validate() && $goods_intro->validate()) {
                $sn= GoodsDayCount::getGoodsSn();
//              var_dump($sn);exit;
                $model->sn=$sn;
                //默认商品状态正常
                $model->status=1;
                $model->save();
                //保存商品的详情
                $goods_intro->goods_id=$model->id;
                $goods_intro->save();

            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
            //输出保存成功
            \yii::$app->session->setFlash('success','添加成功!');
            //跳转到列表页
            return $this->redirect(['goods/index']);
        }
        return $this->render('add',['model'=>$model,'category'=>$category,'categories'=>$categories,'brands'=>$brands,'goods_intro'=>$goods_intro]);
    }


    //把商品放入回收站
    public function actionRecycle($id)
    {
        $model =Goods::findOne($id);
        $model->status=0;
        $model->save();
        //跳转到列表页
        return $this->redirect(['goods/index']);
    }


    //显示回收站
    public function actionBack(){
        //分页  总条数  每页显示条数 当前页
        $query=Goods::find();
        //总条数
        $total=$query->where(['=','status','0'])->count();
        //每页显示条数
        $pageSize=15;
        //分页工具类
        $pager=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$pageSize
        ]);
        //取出数据
        $models=$query->limit($pager->limit)->offset($pager->offset)->all();
        //传输数据并展示回收站页面
        return $this->render('back',['models'=>$models,'pager'=>$pager]);
    }


    //彻底删除商品
    public function actionDel($id){
        //根据id从数据库清除一条数据
        Goods::findOne($id)->delete();
        //添加成功保存提示信息到session中然后跳转首页
        \Yii::$app->session->setFlash('danger','清除成功');
        return $this->redirect(['goods/back']);
    }

    public function actionRecover($id){
        //根据id从回收恢复一条数据
        $model=Goods::findOne($id);
        //将状态修改为显示
        $model->status=1;
        //保存状态到数据库
        $model->save();
        //添加成功保存提示信息到session中然后跳转首页
        \Yii::$app->session->setFlash('success','恢复成功');
        return $this->redirect(['goods/index']);
    }


    //uploadfive插件
    public function actions()
    {
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
                    $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename();
//                    "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl();
//                    "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath();
//                    "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"


                    //将图片上传到七牛云
//                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
//                    $qiniu->uploadFile(
//                        $action->getSavePath(), $action->getWebUrl()
//                    );
//                    $url = $qiniu->getLink($action->getWebUrl());
//                    $action->output['fileUrl']  = $url;
                },
            ],
            //UEditor插件
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]

        ];
    }



}
