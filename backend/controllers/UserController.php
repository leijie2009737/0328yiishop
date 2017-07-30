<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\LoginForm;
use backend\models\PasswordForm;
use backend\models\User;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
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
        $models = $query->limit($page->limit)->orderBy('id asc')->offset($page->offset)->all();

        return $this->render('index',['models'=>$models,'page'=>$page]);
//        $models =Brand::find()->where(['!=','status','-1'])->all();
    }


    /*
     * 增加管理员
     */
    public function actionAdd()
    {
        $model = new User(['scenario'=>User::SCENARIO_ADD]);
        //判断请求方式
        $request=new Request();
        $authManager=\Yii::$app->authManager;
//        $authManager->assign()
        if($request->isPost){
            //加载提交信息
            $model->load($request->post());

            if($model->validate()){
                $model->save();
//                var_dump($model->id);exit;
                $userId=$model->id;
                //给用户赋予角色
                if(is_array($model->roles)){
                    //存在角色
                    foreach ($model->roles as $roleName){
                        $role = $authManager->getRole($roleName);
                        if($role){
                            $authManager->assign($role,$userId);
                        }
                    }
                }

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
        $authManager=\Yii::$app->authManager;
        if($request->isPost){
            //实例化一个文件上传对象
            $model->load($request->post());
//            var_dump($model);exit;
            if($model->validate()){
                $model->save();
                //取消用户的所有角色
                $authManager->revokeAll($id);
                //再次赋予角色
                if(is_array($model->roles)){
                    //存在角色
                    foreach ($model->roles as $roleName){
                        $role = $authManager->getRole($roleName);
                        if($role){
                            $authManager->assign($role,$id);
                        }
                    }
                }
                \yii::$app->session->setFlash('success','修改成功!');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        //用户角色的回显

        //根据id获取用户的角色
        $roles = $authManager->getRolesByUser($id);
        $model->roles = ArrayHelper::map($roles,'name','name');
        return $this->render('add',['model'=>$model]);
    }

    /*
     * 删除管理员
     */
    public function actionDel($id)
    {
        $model =User::findOne(['id'=>$id]);
        $authManager=\Yii::$app->authManager;
        $authManager->revokeAll($id);
     /*   $model->status=0;
        $model->save(false);*/
        $model->delete();
//        var_dump($model->getErrors());exit;
        \yii::$app->session->setFlash('success','删除成功!');
        return $this->redirect(['user/index']);
    }


    /*
     * 管理员登陆
     */
    public function actionLogin()
    {
        $admin=['admin'];
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //var_dump($model);exit;
            if($model->login()){
//                var_dump(\Yii::$app->user->identity->username);exit;
                //判断是不是超级管理员登陆
                $user=\Yii::$app->user->identity->username;
                if(in_array($user,$admin)){
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect('index');
                }else{
//                    var_dump('user');exit;
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['user/homepage']);

                }

            }
        }else{
            //var_dump($model->getErrors());exit;
        }
        return $this->render('login',['model'=>$model]);
    }

    /*
     *非超级管理员返回自己的首页
     */
    public  function actionHomepage()
    {
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('success','请登录');
            return $this->redirect(['user/login']);
        }
        return $this->render('homepage');
    }


    /*
     * 重置密码
     */
    public function actionPassword()
    {
        if(\Yii::$app->user->isGuest){

            \Yii::$app->session->setFlash('success','请登录');
//            var_dump(\Yii::$app->user->identity->usernmae);exit;
            return $this->redirect(['user/login']);
        }
//        $id=\Yii::$app->user->identity->id;
//        var_dump(\Yii::$app->user->identity->id);exit;
//        $userone = User::findOne(['id'=>$id]);
        $model= new PasswordForm();
        $request = \YII::$app->request;
        if($request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate() && $model->changePassword()){

            \Yii::$app->session->setFlash('success','修改密码成功');
            return $this->redirect(['user/homepage']);
//            var_dump($model->getErrors());exit;

        }
        return $this->render('password',['model'=>$model]);
    }


/*    //修改自己密码（登录状态才能使用）
    public function actionChPw(){
        //表单字段  旧密码 新密码 确认新密码
        //验证规则  都不能为空  验证旧密码是否正确  新密码不能和旧密码一样  确认新密码和新密码一样
        //表单验证通过 更新新密码
        $model = new PasswordForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //验证通过，更新新密码
            \Yii::$app->session->setFlash('success','密码修改成功');
            return $this->redirect(['index']);
        }

        return $this->render('password',['model'=>$model]);
    }*/


    /*
     * 管理员注销
     */
    public function actionLogout(){

        \yii::$app->user->logout();
        \yii::$app->session->setFlash('success','退出成功!');
        return $this->redirect(['user/login']);
    }


    /*
     *过滤器行为
     */
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['add','edit','index','del'],
            ]
        ];
    }
}
