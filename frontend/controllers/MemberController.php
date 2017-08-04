<?php

namespace frontend\controllers;

use backend\models\GoodsCategory;
use frontend\models\Cart;
use frontend\models\Locations;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\captcha\CaptchaAction;
use yii\helpers\Json;
use frontend\models\Address;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class MemberController extends \yii\web\Controller
{

    //阻止yii的默认视图
    public $layout=false;
    ############ 重要，关闭csrf #############
    public $enableCsrfValidation=false;

    ###############################    用户注册    ###############################
    /*
    *用户注册
    */
    public function actionAjaxRegist(){
        $model = new Member();
        // $model->scenario = Member::SCENARIO_REGIST;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $code = \Yii::$app->session->get('code_'.$model->tel);
            if($model->telCode == $code){
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->created_at = time();
                $model->status = 1;
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                $model->save(false);
                //保存数据提示验证成功
                return Json::encode(['status'=>true,'msg'=>'注册成功']);
            }else{
                $model->addError('telCode',"手机验证码不正确 ");
                return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
            }
        }else{
            //返回错误信息
            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }


    }

    public function actionTel(){
        //$goods_id = Yii::$app->request->post('goods_id');
        $tels =\Yii::$app->request->post('tels');
        $code = rand(100000,999999);
        $res = \Yii::$app->sms->setPhoneNumbers($tels)->setTemplateParam(['code'=>$code])->send();
        if($res){
            \Yii::$app->session->set('code_'.$tels,$code);
            return Json::encode(['status'=>true,'msg'=>'短信发送成功']);
        }else{
            return Json::encode(['status'=>false,'msg'=>"短信发送失败"]);
        }

    }


    public function actionRegist()
    {
        $model =new Member();
        //提交信息
//        if($model->load(\Yii::$app->request->post(),'Member') && $model->validate())
//        {
//            \yii::$app->session->setFlash('success','注册成功');
//            return $this->redirect(['member/login']);
//        }
        return $this->render('regist',['model'=>$model]);
    }

/*    //AJAX表单验证
    public function actionAjaxRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;

        if($model->load(\Yii::$app->request->post()) && $model->validate() ){
            $model->save(false);
            //保存数据，提示保存成功
            return Json::encode(['status'=>true,'msg'=>'注册成功']);
        }else{
            //验证失败，提示错误信息
            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }
    }*/


/*    //前台AJAX输入框验证（不保存）
    public function actionAjaxTest()
    {
        $model = new Member();


        if($model->load(\Yii::$app->request->post()) && $model->validate() ){
//            $model->save(false);
            //保存数据，提示保存成功
            return Json::encode(['status'=>true,'msg'=>'注册成功']);
        }else{
            //验证失败，提示错误信息
            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }
    }*/




    //验证码
    public function actions(){
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'minLength'=>3,
                'maxLength'=>3,
            ]
        ];
    }

##################       商城首页       ##############
    public function actionIndex()
    {
        $goods_category  = GoodsCategory::find()->orderBy('tree,lft')->where('depth=0')->limit(10)->all();
        /*
         *第二种方法：
         * 使用后台的GoodsCategoryphp  和  GoodsCategoryQuery.php
         * 调用GoodsCategoryQuery.php里面的children 和其他方法(自己阅读文档)
         *  然后生成树状图的形式，传递到前台
         */


        return $this->render('index',['goods_category'=>$goods_category]);
    }






###############################    用户登录    ###############################
    /*
     *用户登陆
     */
    public function actionLogin(){

        $model= new LoginForm();
        //加载数据
        if($model->load(\Yii::$app->request->post())){
            //验证数据
            if( $model->validate() && $model->login()){
                //登陆成功，取出用户Id
                $member_id=\Yii::$app->user->identity->id;
                //q取出cookie中的购物信息
                $cookies=\Yii::$app->request->cookies;
                $carts=$cookies->get('cart');
                if($carts){//如果存在购物信息
                    $carts=unserialize($carts);
                    foreach(array_keys($carts) as $goods_id){
                        //遍历键值，得到商品id
                        $model=Cart::find()->andWhere(['goods_id'=>$goods_id])
                            ->andWhere(['member_id'=>$member_id])
                            ->one();
                        if($model){
                            $model->amount+=$carts[$goods_id];
                            $model->save();
                        }else{
                            $model=new Cart();
                            $model->goods_id=$goods_id;
                            $model->amount=$carts[$goods_id];
                            $model->member_id=$member_id;
                            $model->save();
                        }
                    }
                    \Yii::$app->response->cookies->remove('cart');
                }

                //var_dump($model);exit;
                \yii::$app->session->setFlash('success','登陆成功');
                $referrer=\Yii::$app->request->getReferrer();
                if($referrer='http://www.yiishop.com/goods/show-cart'){
                    return $this->redirect(['carts/check-order']);
                }
//                var_dump($referrer);exit;
                return $this->redirect(['goods/index']);
            }else{
                //print_r($model->getErrors());exit;
            }
        }
        return $this->render('login',['model'=>$model]);
    }




    /*
     * 用户注销
     */
    public function actionLogout(){

        \yii::$app->user->logout();
        \yii::$app->session->setFlash('success','退出成功!');
        return $this->redirect(['member/index']);
    }
###############################    收货地址    ###############################
    /*
     *用户收货地址管理
     */
    public function actionAddress()
    {
        $model = new Address();
        $user_id=1;
//        $user_id=\Yii::$app->user->identity->id;
        $address =$model->find()->where(['user_id'=>$user_id])->all();

        //判断提条方式
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->save()){
//                var_dump($model);exit;
                return $this->redirect(['member/address']);
            }var_dump($model->getErrors());exit;
        }
        //调用视图，分配数据
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }


    /*
     *修改  用户收货地址
     */
    public function actionEditAddress($id){
        //实例化模型
        $model = new Address();
        $user_id=1;
//        $user_id=\Yii::$app->user->identity->id;
        $address =$model->find()->where(['user_id'=>$user_id])->all();
        $model=Address::findOne(['id'=>$id]);
        if(!$model){
            throw new NotFoundHttpException('地址不存在');
        }
        $request = new Request();
        //判断提条方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if( $model->validate()){
                $model->save();
                return $this->redirect(['member/address']);
            }else{//验证失败，打印错误信息
                print_r($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }


    /*
     *删除  用户收货地址
     */
    public function actionDelAddress($id){
        $model=Address::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('地址不存在');
        }
        $model->delete();
        return $this->redirect(['member/address']);
    }


    //设置默认地址
    public function actionChgStatus($id){
        $model=Address::findOne(['id'=>$id]);
        if($model->status==0){
            $model->status=1;
        }
        $model->save();
        return $this->redirect(['member/address']);
    }

    //得到三级联动城市
    public function actionLocations($id){
        $model=new Locations();
        return $model->getProvince($id);
    }


    ################     测试发短信  ##############
    public function actionTest(/*$tel*/)
    {
        $code = rand(000000,999999);
        $tel = '13880166455';
        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
        var_dump($res);
    }

}
