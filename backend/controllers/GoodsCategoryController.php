<?php

namespace backend\controllers;


use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\HttpException;

class GoodsCategoryController extends \yii\web\Controller
{
    //显示商品分类
    public function actionIndex($keywords='')
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = GoodsCategory::find()->where(['and',"name like '%{$keywords}%'"]);
        //var_dump($query);exit;
        //总条数
        $total = $query->orderBy('tree,lft')->count();
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
    }


    //增加商品分类
    public function actionAdd()
    {
        $model = new GoodsCategory(['parent_id'=>0]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //判断同级分类是否重名
            //var_dump($model);exit;

            //判断是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在');
                }

            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','商品分类添加成功');
            return $this->redirect(['index']);

        }
        //获取所以分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }

    //修改分类
    public function actionEdit($parent_id)
    {
        $model =GoodsCategory::findOne($parent_id);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //$model->save();
            //判断是否是添加一级分类
            if($model->parent_id){
                //非一级分类

                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在');
                }

            }else{
                if($model->oldAttributes['parent_id']==0){
                  $model->save();
                }
                //一级分类
                $model->makeRoot();

            }
            \Yii::$app->session->setFlash('success','商品分类添加成功');
            return $this->redirect(['index']);

        }
        //获取所以分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);

    }


    //删除
    public function actionDel($id)
    {
        $model = GoodsCategory::findOne(['parent_id'=>$id]);
        if($model){
            \Yii::$app->session->setFlash('danger','分类下有子分类，不能删除');
            return $this->redirect(['index']);

        }
        GoodsCategory::findOne($id)->delete();
        \Yii::$app->session->setFlash('danger','删除成功');
        return $this->redirect(['index']);
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

    //测试Ztree
    public function actionTest1()
    {
        //$this->layout = false;
        //不加载布局文件
        return $this->renderPartial('test');
    }
}
