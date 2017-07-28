<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $parent_id
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','parent_id','sort'],'required'],
            ['url','safe'],
            [['parent_id', 'sort'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '菜单名称',
            'parent_id' => '上级菜单',
            'url' => '路由地址',
            'sort' => '排序',
        ];
    }


    /*
     *获取上级分类
     */
    public function getParentId()
    {
        return $this->hasOne(Menu::className(),['id'=>'id']);
    }


    /*
     *获取下级菜单
     */
    public function getChildren()
    {
        return $this->hasMany(Menu::className(),['parent_id'=>'id']);
    }

    /*
     *生成菜单列表
     */
    public static function getMenus()
    {
        $menus=self::findAll(['parent_id'=>0]);
//        var_dump($menus);exit;
        $menuItems = [];
        foreach ($menus as $menu){
            $items=[];
            //顶级菜单
            foreach ($menu->children as $child){
                //判断当前用户是否有该路由（菜单）的权限
                if(Yii::$app->user->can($child->url)){
                    $items[] = ['label' => $child->name, 'url' => [$child->url]];
                }
            }
            //没有子菜单时，不显示一级菜单
            if(!empty($items)){
                $menuItems[] = ['label' => $menu->name, 'items' => $items];
            }
        }
        return $menuItems;
    }

}
