<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class Password extends ActiveRecord
{
    public $password;
    public $new_password1;
    public $new_password2;

    public function rules()
    {
        return [
            [['password','new_password1','new_password2'],'required','message'=>'{attribute}必填'],

           /* ['new_password1','compare','compareAttribute'=>'new_password2'],//规则判断2次输入密码是否正确*/
        ];
    }

    public function attributeLabels()
    {
        return [
           'password'=>'旧密码',
           'new_password1'=>'新密码',
           'new_password2'=>'确认密码',
        ];
    }


    /*
     *修改密码
     */
    public function changePassword()
    {
        $id=\Yii::$app->user->identity->id;
//        $model = User::findIdentity($id);
//        $old_password = $model->password_hash;
        $old_password = \Yii::$app->user->identity->password_hash;
//        var_dump($this->new_password1);exit;
        if(\yii::$app->security->validatePassword($this->password,$old_password)){
            //原密码输入正确
            if(!($this->new_password1==$this->new_password2)){
                //两次密码输入不一致
                $this->addError('new_password2','两次密码输入不一致');
                return false;
            }
            if($this->new_password1==$this->password){
                //新密码不能和原密码一样
                $this->addError('new_password1','新密码不能和原密码一样');
                return false;
            }
            $user = User::findOne(['id'=>$id]);
            if($user){
                $user->password_hash =Yii::$app->security->generatePasswordHash($this->new_password1);
                $user->save(false);
            }
            return true;
        }else{
            $this->addError('password','密码错误');
            return false;
        }




    }



}


































































