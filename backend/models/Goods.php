<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    //定义私有属性（是否上架）
    public static $getIsOnSale=[ 1=>'上架',0=>'下架'];
//    public function getIsOnSale(){
//         return $options=[0=>'下架', 1=>'上架'];
//    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','goods_category_id','brand_id','market_price','shop_price','stock','is_on_sale','sort','logo'],'required','message'=>"{attribute}必填"],
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time', 'view_times'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 60],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'LOGO图片',
            'goods_category_id' => '	商品分类',
            'brand_id' => ' 品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '	库存',
            'is_on_sale' => '	是否在售(1在售 0下架)',
            'status' => '状态(1正常 0回收站)',
            'sort' => '	排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }


    //时间行为
    public function behaviors()
    {
        return [
            'time'=>[
                'class'=>TimestampBehavior::className(),
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                    // ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ]
            ]
        ];
    }


    /*
    * 商品和相册关系 1对多
    */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }
}
