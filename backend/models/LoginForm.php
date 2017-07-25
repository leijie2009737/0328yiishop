<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model
{
    public  $name;
    public  $password;
    public  $rememberMe;

    public function rules(){
        return [
            [['name','password'],'required'],
            ['rememberMe','boolean']
        ];
    }

    public function attributeLabels(){
        return [
            'name'=>'用户名',
            'password'=>'密码',

        ];
    }


    /*
     *登陆
     */
    public function login(){
        //取出数据验证
        $user=User::findOne(['username'=>$this->name]);
        if($user){
            if(\yii::$app->security->validatePassword($this->password,$user->password_hash)){
                //密码正确可以登录
                \yii::$app->user->login($user,$this->rememberMe?3600*24:0);
                $user->last_login_ip=ip2long(\Yii::$app->request->userIP);
                $user->last_login_time=time();
                $user->save();
                return true;
            }else{
                //错误信息
                $this->addError('password','密码错误');
            }
        }else{
            //用户不存在输出错误；
            $this->addError('name','用户名不存在');
        }
        return false;
    }
}