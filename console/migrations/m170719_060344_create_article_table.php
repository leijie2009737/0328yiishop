<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170719_060344_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'name' =>$this->string(50)->comment('名称'),
            'intro' =>$this->text(50)->comment('简介'),
            'article_category_id' =>$this->integer()->comment('文章分类id'),
            'sort'  =>$this->integer(11)->comment('排序'),
            'status'=>$this->integer(2)->comment('	状态(-1删除 0隐藏 1正常)'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ],'ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
