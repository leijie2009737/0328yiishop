<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model
{
    public $name;
    public $description;
    const SCENARIO_ADD = 'add';
    public function rules()
    {
        return [
          [['name','description'],'required','message'=>'{attribute}必填'],
            //添加时权限名字不能重复
            ['name','validateName','on'=>self::SCENARIO_ADD],
        ];
    }


    public function attributeLabels()
    {
        return [
          'name'=>'权限名称(路由地址)',
          'description'=>'权限描述',
        ];
    }


    /*
     *自定义的规则
     */
    public function validateName()
    {
        $authManage = \Yii::$app->authManager;
        if($authManage->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }

    }
}