<?php

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\User;
use yii\data\Pagination;
use yii\web\Request;

class UserController extends \yii\web\Controller
{
    /*
     * 显示管理员
     */
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = User::find();
        //总条数
        $total = $query->where(['>=','status',10])->count();
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


    /*
     * 增加管理员
     */
    public function actionAdd()
    {
        $model = new User();
        //判断请求方式
        $request=new Request();
        if($request->isPost){
            //实例化一个文件上传对象
            $model->load($request->post());
//            var_dump($model);exit;
            if($model->validate()){

                //使用hash加密密码
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->save();
                \yii::$app->session->setFlash('success','添加成功!');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    /*
     * 修改管理员
     */
    public function actionEdit($id)
    {
        $model =User::findOne(['id'=>$id]);
        $request=new Request();
        if($request->isPost){
            //实例化一个文件上传对象
            $model->load($request->post());
//            var_dump($model);exit;
            if($model->validate()){

                //使用hash加密密码
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->save();
                \yii::$app->session->setFlash('success','修改成功!');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    /*
     * 删除管理员
     */
    public function actionDel($id)
    {
        $model =User::findOne(['id'=>$id]);
        $model->status=0;
        $model->save();
        \yii::$app->session->setFlash('success','删除成功!');
        return $this->redirect(['user/index']);
    }


    /*
     * 管理员登陆
     */
    public function actionLogin()
    {
        $model=new LoginForm();
        //判断请求方式
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate() && $model->login()){
                //输出登录成功
                \yii::$app->session->setFlash('success','登录成功!');
                //var_dump(\Yii::$app->user->isGuest);exit;
                return $this->redirect(['user/index']);
            }
        }

        return $this->render('login',['model'=>$model]);
    }


    /*
     * 管理员注销
     */
    public function actionLogout(){

        \yii::$app->user->logout();
        \yii::$app->session->setFlash('success','退出成功!');
        return $this->redirect(['user/login']);
    }
}
