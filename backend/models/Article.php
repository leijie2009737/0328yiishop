<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    //文章状态
    public function getStatusOptions($hiddend=true)
    {
        $options=[-1=>'删除', 0=>'隐藏', 1=>'正常'];
        if($hiddend){
            unset($options[-1]);
            return $options;
        }
    }


    //关联文章和文章分类表
    public function getArticleCategory()
    {
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','sort','article_category_id','status'],'required','message'=>'{attribute}必填'],
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'article_category_id' => '文章分类',
            'sort' => '排序',
            'status' => '状态',
            'create_time' => '创建时间',
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
}
