<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class GoodsCategory extends ActiveRecord
{


    /*
     *商品分类和下级的遍历
     */
    public function getchildren()
    {
        return $this->hasMany(GoodsCategory::className(),['parent_id'=>'id']);
    }



}