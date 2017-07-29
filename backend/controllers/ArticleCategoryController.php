<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleCategoryController extends \yii\web\Controller
{
    //显示页面
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = ArticleCategory::find();
        //总条数
        $total = $query->where(['!=','status','-1'])->orderBy('sort desc')->count();
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


    //增加文章分类
    public function actionAdd()
    {
       $model = new ArticleCategory();
       $request = new Request();
        if($request->isPost) {
            //加载数据
            $model->load($request->post());
            if ($model->validate()) {
                //保存
                if($model->save()){
                    //输出保存成功
                    \yii::$app->session->setFlash('success','保存成功!');
                    //跳转到列表页
                    return $this->redirect(['article-category/index']);
                }
            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //修改文章分类
    public function actionEdit($id)
    {
        $model = ArticleCategory::findOne($id);
        $request = new Request();
        if($request->isPost) {
            //加载数据
            $model->load($request->post());
            if ($model->validate()) {
                //保存
                if($model->save()){
                    //输出修改成功
                    \yii::$app->session->setFlash('success','修改成功!');
                    //跳转到列表页
                    return $this->redirect(['article-category/index']);
                }
            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //删除文章分类
    public function actionDel($id)
    {
        //逻辑删除
        $model =ArticleCategory::findOne($id);
        $model->status=-1;
        //保存
        if($model->save()){
            //输出删除成功
            \yii::$app->session->setFlash('success','删除成功!');
            //跳转到列表页
            return $this->redirect(['article-category/index']);
        }
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
