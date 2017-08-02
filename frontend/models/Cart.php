<?php

namespace frontend\models;

use Yii;

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
}
