<?php

namespace backend\controllers;


use backend\models\GoodsCategory;

class GoodsCategoryController extends \yii\web\Controller
{
    //显示商品分类
    public function actionIndex()
    {
        $model =new GoodsCategory();
        return $this->render('index',['model'=>$model]);
    }


    //增加商品分类
    public function actionAdd()
    {
        $model =new GoodsCategory();


        return $this->render('add',['model'=>$model]);
    }

    //测试嵌套集合
    public function actionTest()
    {
//        $category = new GoodsCategory();
//        $category->name = '家用电器';/*因为规则限定了parent_id必须填写，所以先默认一个值*/
//        $category->parent_id = 0;
//        $category->makeRoot();
//        var_dump($category->getErrors());exit;


        $category2 = new GoodsCategory();
        $category2->name = '厨房小家电';
        $category = GoodsCategory::findOne(['id'=>1]);
        $category2->parent_id = $category->id;
        $category2->prependTo($category);
        echo 1111222;
    }
}
