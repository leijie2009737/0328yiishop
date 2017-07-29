<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    //操作执行之前
    public function beforeAction($action)
    {
        //如果用户没有登录，引导用户登录
        if(\Yii::$app->user->isGuest){
            return $action->controller->redirect(\Yii::$app->user->loginUrl);
        }
        //$action->uniqueId 路由
        if(!\Yii::$app->user->can($action->uniqueId)){
            //return false;
            throw new ForbiddenHttpException('对不起，您没有该访问权限');
        }
        //return false;
        return parent::beforeAction($action); //return true;//放行
    }
}