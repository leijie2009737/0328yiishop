<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use EasyWeChat\Foundation\Application;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;

class CartsController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        return $this->render('index');
    }

    /*
     *核算订单页面
     */
    //订单提交之前页面
    public function actionOrder(){
        //收货人信息
        $user_id=\Yii::$app->user->identity->id;
        $address=Address::find()->where(['user_id'=>$user_id])->all();

//        var_dump($address);exit;
        //送货方式
        $deliveries=Order::$deliveries;
        //付款方式
        $payments=Order::$payments;
        //商品信息，从购物车表中读取
        $model=Cart::find()->where(['member_id'=>$user_id])->asArray()->all();
        $goods_id=[];
        $carts=[];
        foreach($model as $cart){
            $goods_id[]=$cart['goods_id'];
            $carts[$cart['goods_id']]=$cart['amount'];
        }
        //var_dump($carts);exit;
        $goods=Goods::find()->where(['in','id',$goods_id])->asArray()->all();
        //调用视图，分配数据
        return $this->render('check',['address'=>$address,'deliveries'=>$deliveries,'payments'=>$payments,'goods'=>$goods,'carts'=>$carts]);
    }


    //提交订单
    public function actionAddOrder($address_id,$delivery_id,$payment_id){
        //实例化模型
        $model=new Order();

        //开始事物
        $transaction=\Yii::$app->db->beginTransaction();
        $user_id=\Yii::$app->user->identity->id;
        $carts=Cart::find()->where(['member_id'=>$user_id])->all();
        if($carts==null){
            return json_encode('NULL');
        }
        try{
            //处理数据
            //获取地址信息
            $address=Address::findOne(['user_id'=>$user_id,'id'=>$address_id]);
            $model->member_id=$user_id;
            $model->name=$address->name;
            $model->province=$address->sheng;
            $model->city=$address->city;
            $model->area=$address->area;
            $model->address=$address->address;
            $model->tel=$address->tel;
            //获取配送方式
            $model->delivery_id=$delivery_id;
            $model->delivery_name = Order::$deliveries[$delivery_id]['name'];
            $model->delivery_price = Order::$deliveries[$delivery_id]['price'];
            //付款方式
            $model->payment_id=$payment_id;
            $model->payment_name=Order::$payments[$payment_id]['name'];
            $model->total=0;
            $model->status=1;
            $model->create_time=time();
            $model->save(false);
            //处理订单商品表数据
            //获取购物车数据
            $total=0;
            foreach($carts as $cart){
                $goods=Goods::findOne(['id'=>$cart->goods_id]);
                $order_goods=new OrderGoods();
                if($cart->amount<=$goods->stock){
                    $order_goods->order_id=$model->id;
                    $order_goods->goods_id=$goods->id;
                    $order_goods->goods_name=$goods->name;
                    $order_goods->logo=$goods->logo;
                    $order_goods->price=$goods->shop_price;
                    $order_goods->amount=$cart->amount;
                    $order_goods->total=$cart->amount*$goods->shop_price;
                    $order_goods->save();
                    //改变订单表的统计金额,将购买的每个商品的价钱相加
                    $total+=$order_goods->total;
                    //下单成功后改变商品库存
                    $goods->stock-=$cart->amount;
                    $goods->save();
                    //下单成功后清除购物车
                    $cart->delete();
                }else{
                    //（检查库存，如果库存不够抛出异常）
                    throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                }
            }
            //订单生成成功后，计算订单表总金额
            $model->total=$total;
            $model->update(false);
            //提交事务
            $transaction->commit();
            return json_encode('success');
        }catch(Exception $e){//捕获异常
            //如果异常回滚数据
            $transaction->rollBack();
        }
        // }else{
        var_dump($model->getErrors());exit;
        //  }
        //}
        //return 'success';
    }

    /*
     *结算成功
     */
    public function actionEnd(){
        return $this->render('scuess');
    }


    /*
     *查看订单状态
     */
    public function actionShowOrder()
    {   $member_id=\Yii::$app->user->id;
        $orders =Order::find()->where(['member_id'=>$member_id])->all();

        return $this->render('order',['orders'=>$orders]);
    }


    /*
     *判断是否登陆
     */
    public function behaviors()
    {
        return [
            'ACF'=>[
                'class'=>AccessControl::className(),
                'only'=>['order','add-order','show-order'],//哪些操作需要使用该过滤器
                'rules'=>[
                    [
                        'allow'=>true,//是否允许
                        'actions'=>['order','add-order','show-order'],//指定操作
                        'roles'=>['@'],//指定角色 ?表示未认证用户(未登录) @表示已认证用户(已登录)
                    ],
//                    [
//                        'allow'=>true,
//                        'actions'=>['view-article'],
//                        //'roles'=>['?','@']
//                        'matchCallback'=>function(){
//                            //return (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->username=='admin');
//                            return !(date('d')%2);
//                        }
//                    ],
                ]
            ]
        ];
    }


    /*
     *redis测试
     */
    public function actionTest()
    {
        phpinfo();
    }

    //微信支付
    public function actionPay($order_id)
    {
        $goods_order = Order::findOne(['id'=>$order_id]);
        if($goods_order==null){
            throw new NotFoundHttpException('订单不存在');
        }
        if($goods_order->status != 1){
            throw new NotFoundHttpException('订单已支付或者已取消');
        }
        //1 生成订单（微信支付订单）
        //use EasyWeChat\Payment\Order;
        $options = \Yii::$app->params['wechat'];
        $app = new Application($options);
        $payment = $app->payment;
        //var_dump($payment);exit;
        $attributes = [
            'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...
            'body'             => '京西商城的订单',
            'detail'           => '三星超极本',
            'out_trade_no'     => $goods_order->trade_no,
            'total_fee'        => $goods_order->total*100, // 单位：分 订单金额
            'notify_url'       => 'http://www.yii2shop.com/site/notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            //'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new \EasyWeChat\Payment\Order($attributes);
        //var_dump($order);exit;
        //2 调统一下单api（返回交易链接code_url）
        $result = $payment->prepare($order);
        //var_dump($result);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            //$prepayId = $result->prepay_id;
            $result->code_url;// weixin://wxpay/bizpayurl?pr=ZxbNr1d
        }

        //3. 将code_url生成二维码（显示到页面）

        //use Symfony\Component\HttpFoundation\Response;

// Create a basic QR code
        $qrCode = new QrCode($result->code_url);
        //$qrCode->setSize(300);

// Set advanced options
        /*$qrCode
            ->setWriterByName('png')
            ->setMargin(10)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            ->setLabel('Scan the code', 16, __DIR__.'/../assets/noto_sans.otf', LabelAlignment::CENTER)
            ->setLogoPath(__DIR__.'/../assets/symfony.png')
            ->setLogoWidth(150)
            ->setValidateResult(false)
        ;*/

// Directly output the QR code
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();

// Save it to a file
        //$qrCode->writeFile(__DIR__.'/qrcode.png');

// Create a response object
        //$response = new Response($qrCode->writeString(), Response::HTTP_OK, ['Content-Type' => $qrCode->getContentType()]);

    }


    public function actionNotify(){
        $options = \Yii::$app->params['wechat'];
        $app = new Application($options);

        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::findOne(['trade_no'=>$notify->out_trade_no]);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            /*if ($order->paid_at) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }*/
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                //$order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 2;
                $order->save(); // 保存订单
            } else { // 用户支付失败
                //$order->status = 'paid_fail';
            }

            return true; // 返回处理完成
        });
        return $response;
    }

    //查询订单
    public function actionQuery()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $result = $app->payment->query('20170806001');
        var_dump($result);
//        trade_state
//        SUCCESS—支付成功
//REFUND—转入退款
//NOTPAY—未支付
//CLOSED—已关闭
//REVOKED—已撤销（刷卡支付）
//USERPAYING--用户支付中
//PAYERROR--支付失败(其他原因，如银行返回失败)
    }
}
