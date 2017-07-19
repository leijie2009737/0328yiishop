<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord
{
    //关联详情页和文章表
    public function getArticleDetail()
    {
        return $this->hasOne(ArticleDetail::className(),['article_id'=>'id']);
    }

    public function rules()
    {
        return [
            ['content','required','message'=>'必填'],
            ['content','string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content'=>'文章详情',
        ];
    }
}