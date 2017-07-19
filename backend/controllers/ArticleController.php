<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleController extends \yii\web\Controller
{
    //显示
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Article::find();
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

    //增加
    public function actionAdd()
    {
        $model =new Article();
        $article_category =ArticleCategory::find()->all();
        $request= new Request();
        if($request->isPost){
            $model->load($request->post());
            if ($model->validate()) {
                //保存
                if($model->save()){
                    //输出保存成功
                    \yii::$app->session->setFlash('success','保存成功!');
                    //跳转到列表页
                    return $this->redirect(['article/index']);
                }
            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'article_category'=>$article_category]);
    }

    //修改
    public function actionEdit()
    {

    }
    //删除
    public function actionDel()
    {

    }
}
