<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Request;

class ArticleController extends \yii\web\Controller
{
    //显示
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Article::find();
        //总条数(只查询未删除的数据)
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
        $article_detail=new ArticleDetail();
        $request= new Request();
        if($request->isPost){
            $model->load($request->post());
            $article_detail->load($request->post());
            //var_dump($model->id);exit;
            //var_dump($article_detail);exit;
            if ($model->validate() && $article_detail->validate()) {
                //保存
                $model->save();
                //var_dump($model->id);exit;
                //ArticleDetail::findOne($model->id);
                //查找文章的id赋值给详情页的article_id
                $article_detail->article_id=$model->id;
                $article_detail->save();
                    //输出保存成功
                    \yii::$app->session->setFlash('success','保存成功!');
                    //跳转到列表页
                    return $this->redirect(['article/index']);

            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'article_category'=>$article_category,'article_detail'=>$article_detail]);
    }

    //修改
    public function actionEdit($id)
    {
        $model = Article::findOne($id);
        $article_category =ArticleCategory::find()->all();
        $article_detail=ArticleDetail::findOne($id);
        $request= new Request();
        if($request->isPost){
            $model->load($request->post());
            $article_detail->load($request->post());
            //var_dump($model->id);exit;
            //var_dump($article_detail);exit;
            if ($model->validate()) {
                //保存
                $model->save();
                //var_dump($model->id);exit;
                //ArticleDetail::findOne($model->id);
                //查找文章的id赋值给详情页的article_id
                $article_detail->article_id=$model->id;
                $article_detail->save();
                //输出保存成功
                \yii::$app->session->setFlash('success','修改成功!');
                //跳转到列表页
                return $this->redirect(['article/index']);

            } else {
                //打印模型的验证错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'article_category'=>$article_category,'article_detail'=>$article_detail]);
    }
    //删除
    public function actionDel($id)
    {
        //逻辑删除
        $model =Article::findOne($id);
        $model->status=-1;
        //保存
        if($model->save()){
            //输出删除成功
            \yii::$app->session->setFlash('success','删除成功!');
            //跳转到列表页
            return $this->redirect(['article/index']);//当前控制器 index操作
        }
    }

    //显示文章的详情页
    public function actionShow($id)
    {
        $model2=Article::findOne($id);
        $model = ArticleDetail::findOne($id);
        $model1=ArticleCategory::findOne($model2->article_category_id);

        //var_dump($model);exit;
        return $this->render('show',['model'=>$model,'model1'=>$model1,'model2'=>$model2]);
    }

    //UEditor插件
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }
}
