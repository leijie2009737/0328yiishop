<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\web\NotFoundHttpException;

class GoodsController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        return $this->render('index');
    }


    /*
     *商品  列表页
     */
    public function actionList($id)
    {   //商品分类$id

        $goods =Goods::find()->where("goods_category_id=$id and status=1")->all();
        $models=\frontend\models\GoodsCategory::find()->where('parent_id=0')->all();
        if(!$goods){
            throw new NotFoundHttpException('你要找的商品不存在!');exit;
        }
        return $this->render('list',['goods'=>$goods,'models'=>$models]);
    }

    /*
     *商品根据id的详情页
     */
    public function actionGoods($id)
    {
        $goods =Goods::findOne($id);
        if(!$goods){
            throw new NotFoundHttpException('你要找的商品不存在!');
        }
        $category_id=$goods->goods_category_id;
        $models=\frontend\models\GoodsCategory::find()->where('parent_id=0')->all();
        $goods_category2=GoodsCategory::findOne(["id"=>$category_id]);
        $goods_category1=GoodsCategory::findOne(["id"=>$goods_category2->parent_id]);

        $goods_intro =GoodsIntro::findOne($id);
        $goods_gallery =GoodsGallery::find()->where(['goods_id'=>$id])->all();

        return $this->render('goods',['goods'=>$goods,'goods_intro'=>$goods_intro,'goods_gallery'=>$goods_gallery,'goods_category2'=>$goods_category2,'goods_category1'=>$goods_category1,'models'=>$models]);
    }






}
