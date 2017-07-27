<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $description;
    public $permissions;
    const SCENARIO_ADD = 'add';

    public function rules()
    {
        return [
            [['name','description'],'required','message'=>'{attribute}必填'],
            //添加的角色名字不能重复
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['permissions','safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permissions'=>'权限'
        ];
    }


    /*
     *自定义的规则
     */
    public function validateName()
    {
        $authManage = \Yii::$app->authManager;
        if($authManage->getRole($this->name)){
            $this->addError('name','权限已存在');
        }

    }



}