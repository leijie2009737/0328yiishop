<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property integer $last_login_ip
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*[['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'last_login_time', 'last_login_ip'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],*/

            [['username',  'password_hash', 'email'], 'required'],
            [['username', 'password_hash','email'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['email'], 'unique'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => '记住密码',
            'password_hash' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => '邮箱',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登陆ip',
        ];
    }


    /*
     *登陆
     */
    public function login(){
        //取出数据验证
        $user=User::findOne(['username'=>$this->usernamename]);
        if($user){
            if(\yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //密码正确可以登录
                \yii::$app->user->login($user);
                return true;

            }else{
                //错误信息
                $this->addError('password_hash','密码错误');

            }
        }else{
            //用户不存在输出错误；
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}
