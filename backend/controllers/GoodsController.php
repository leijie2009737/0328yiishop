<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    //显示页面
    public function actionIndex()
    {
        $query = Goods::find();
        //总条数
        //$total = $query->where(['!=','status','-1'])->count();
        $total = $query->where(['>','status',0])->orderBy('sort asc')->count();
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
    }


    //增加商品
    public function actionAdd()
    {
        $model = new Goods();
        //获取商品分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        $category = new GoodsCategory();
        //品牌分类
        $brands = Brand::find()->all();
        //商品详情
        $goods_intro= new GoodsIntro();
        return $this->render('add',['model'=>$model,'category'=>$category,'categories'=>$categories,'brands'=>$brands,'goods_intro'=>$goods_intro]);
    }

    //uploadfive插件
}
