<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170730_034438_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('收货人'),
            'sheng'=>$this->string()->comment(''),
            'city'=>$this->string()->comment(''),
            'area'=>$this->string()->comment('所在地区'),
            'address'=>$this->string()->comment('详细地址'),
            'tel'=>$this->string()->comment('手机号码'),
            'status'=>$this->string()->comment('默认收货地址'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
