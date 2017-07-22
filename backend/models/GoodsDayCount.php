<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_day_count".
 *
 * @property string $day
 * @property integer $count
 */
class GoodsDayCount extends \yii\db\ActiveRecord
{
    //获取商品商号
    public static function getGoodsSn(){
        $date =date('Ymd');
        //根据当天日期查找数据库有米有今天的字段
        $count =self::find()->where(['day'=>$date])->one();
//        var_dump($count);
        if($count){
            //如果有，就商品数加1
            $count->count=+1;
        }else{
            //没有就创建第一个商品
            $count= new self ;
            $count->day=$date;
            $count->count=1;
        }
        //保存
        $count->save(false);
        //返回以日期为开始的货号“201705210001”
        return $date.str_pad($count->count,4,'0',STR_PAD_LEFT);

    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_day_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day'], 'safe'],
            [['count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day' => '日期',
            'count' => '商品数',
        ];
    }
}
