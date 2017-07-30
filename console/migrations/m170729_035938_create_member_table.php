<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m170729_035938_create_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('member', [
            'id' => $this->primaryKey(),
            //username	varchar(50)	用户名
            'username'=>$this->string(50)->comment(''),
            //auth_key	varchar(32)
            'auth_key'=>$this->string(32)->comment(''),
            //password_hash	varchar(100)	密码（密文）
            'password_hash'=>$this->string(100)->comment(''),
            //email	varchar(100)	邮箱
            'email'=>$this->string(100)->comment(''),
            //tel	char(11)	电话
            'tel'=>$this->char(11)->comment(''),
            //last_login_time	int	最后登录时间
            'last_login_time'=>$this->integer()->comment(''),
            //last_login_ip	int	最后登录ip
            'last_login_ip'=>$this->integer()->comment(''),
            //status	int(1)	状态（1正常，0删除）
            'status'=>$this->integer(1)->comment(''),
            //created_at	int	添加时间
            'created_at'=>$this->integer()->comment(''),
            //updated_at	int	修改时间
            'updated_at'=>$this->integer()->comment(''),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('member');
    }
}
