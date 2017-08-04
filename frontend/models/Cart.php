<?php

namespace frontend\models;

use Yii;
use yii\web\Cookie;

/**
 * This is the model class for table "cart".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $amount
 * @property integer $member_id
 */
class Cart extends \yii\db\ActiveRecord
{

//    public function beforeSave($insert)
//    {
//        if($insert)//判断是添加还是修改
//        {   //添加
//            $this->member_id =Yii::$app->user->identity->id;
//
//
//        }else{
//            $this->updated_at = time();
//        }
//
//        return parent::beforeSave($insert);
//    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'amount', 'member_id'], 'integer'],
            [['goods_id', 'amount', 'member_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'amount' => '商品数量',
            'member_id' => '用户id',
        ];
    }

    public static function delCart($goods_id)
    {
        if (Yii::$app->user->isGuest) {
            //删除cookie中数据
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
//            var_dump($cookie);exit;
            if ($cookie !== null) {
                //有cookie
                $date = unserialize($cookie->value);
                if (isset($date[$goods_id])) {
                    //有对应的商品
                    unset($date[$goods_id]);
                    $cookie->value = serialize($date);
                    Yii::$app->response->cookies->add($cookie);
//                    var_dump($cookie);exit;
                    return true;
                }
            }
        } else {//删除数据库中的数据
            $model = self::findOne(['goods_id' => $goods_id, 'member_id' => Yii::$app->user->id]);
            if ($model !== null) {
                return $model->delete();
            }
        }
        return false;
    }

    }
