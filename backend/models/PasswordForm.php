<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class PasswordForm extends ActiveRecord
{
    public $password;
    public $new_password1;
    public $new_password2;

    public function rules()
    {
        return [
            [['password','new_password1','new_password2'],'required','message'=>'{attribute}必填'],

           /* ['new_password1','compare','compareAttribute'=>'new_password2'],//规则判断2次输入密码是否正确*/

             /*['new_password1','compare','compareAttribute'=>'new_password2','operator'=>'!='],//新密码不能和旧密码一样*/

           /* ['oldPassword','validatePassword'],//验证旧密码是否正确 自定义验证规则*/
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

/*    //自定义验证方法
    public function validatePassword()
    {
        //只处理验证不通过的情况，添加相应的错误信息
        //$this->oldPassword
        if(!\Yii::$app->security->validatePassword($this->oldPassword,\Yii::$app->user->identity->password_hash)){
            //密码错误
            $this->addError('oldPassword','旧密码不正确');
        }

    }*/


}


































































