<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Cart;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class GoodsController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        return $this->render('index');
    }


    /*
     *商品  列表页
     */
    public function actionList($id)
    {   //商品分类$id

        $goods =Goods::find()->where("goods_category_id=$id and status=1")->all();
        $models=\frontend\models\GoodsCategory::find()->where('parent_id=0')->all();
        if(!$goods){
            throw new NotFoundHttpException('你要找的商品不存在!');exit;
        }
        return $this->render('list',['goods'=>$goods,'models'=>$models]);
    }

    /*
     *商品根据id的详情页
     */
    public function actionGoods($id)
    {
        $goods =Goods::findOne($id);
        if(!$goods){
            throw new NotFoundHttpException('你要找的商品不存在!');
        }
        $category_id=$goods->goods_category_id;
        $models=\frontend\models\GoodsCategory::find()->where('parent_id=0')->all();
        $goods_category2=GoodsCategory::findOne(["id"=>$category_id]);
        $goods_category1=GoodsCategory::findOne(["id"=>$goods_category2->parent_id]);

        $goods_intro =GoodsIntro::findOne($id);
        $goods_gallery =GoodsGallery::find()->where(['goods_id'=>$id])->all();

        return $this->render('goods',['goods'=>$goods,'goods_intro'=>$goods_intro,'goods_gallery'=>$goods_gallery,'goods_category2'=>$goods_category2,'goods_category1'=>$goods_category1,'models'=>$models]);
    }


    /*
     *从详情页添加到购物车
     */
    public function actionToCart($goods_id,$amount)
    {
        //未登录
        if(\Yii::$app->user->isGuest){
            //如果没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，数量累加
                    $carts[$goods_id] += $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time(),
            ]);
            $cookies->add($cookie);
            //var_dump(unserialize($cookies->get('cart')));exit;
        }else{
            //用户已登录，操作购物车数据表
            $model =new Cart();
            $member_id=\Yii::$app->user->identity->id;
            $cart =Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);

//            var_dump($cart);exit;
            if($cart){
                //存在商品，合并商品数量
               $cart->amount+=$amount;
               $cart->save();

            }else{
                $model->member_id=$member_id;
                $model->goods_id=$goods_id;
                $model->amount=$amount;
                if($model->validate()){
                    $model->save();
                }else{
                    throw new NotFoundHttpException('你要找的商品不存在!');
                }
            }
        }
        return $this->redirect(['show-cart']);
    }


    /*
     * 购物车显示页面
     */
    public function actionShowCart()
    {
        $this->layout = false;
        //1 用户未登录，购物车数据从cookie取出
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //var_dump(unserialize($cookies->getValue('cart')));
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [];
            }else{
                $carts = unserialize($cart->value);
            }
            //获取商品数据
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
        }else{
            //2 用户已登录，购物车数据从数据表取
            $cart = Cart::find()->select(['goods_id','amount'])->where(['member_id'=>\Yii::$app->user->identity->id])->asArray()->all();
//            var_dump($cart);exit;
            $carts=[];
            foreach ($cart as $car){
                $carts[$car['goods_id']]=$car['amount'];
            }
//            var_dump($carts);exit;
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();

        }
        return $this->render('cart',['models'=>$models,'carts'=>$carts]);

    }


    /*
     *Ajax修改购物车的数据
     */
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
//      var_dump($goods_id);exit;

        if(\Yii::$app->user->isGuest){
            //未登录时
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }else{
            //用户登录时
            $member_id =\Yii::$app->user->identity->id;
            $cart =Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
//            var_dump($cart);exit;
            $cart->amount=$amount;
            $cart->save();
        }

    }


    /*
     *删除购物车的数据
     */
    public function actionGoodsDel($id)
    {
        if(\Yii::$app->user->isGuest){
            $cookies1 = \Yii::$app->request->cookies;
            $cookies=\Yii::$app->response->cookies;
            $cookie=unserialize($cookies1->getValue('cart'));
            unset($cookie[$id]);
//            $cookies->remove('cart');
//            var_dump($cookie);exit;

            //直接覆盖cookie
            $del = new Cookie([
                'name'=>'cart',
                'value'=>serialize($cookie),
                'expire'=>7*24*3600+time()
            ]);
           $cookies->add($del);
            $cookie=unserialize($cookies->getValue('cart'));
//            var_dump($cookie);exit;
            return $this->redirect(['show-cart']);
        }else{
            $car=Cart::findOne(['goods_id'=>$id]);
            if($car){
                $car->delete();
                return $this->redirect(['show-cart']);
            }else{
                throw new NotFoundHttpException('你要找的商品不存在!');
            }

        }
    }
}
