<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_detail`.
 */
class m170719_061145_create_article_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_detail', [
            'article_id' => $this->primaryKey()->comment('文章id'),
            'content' =>$this->text()->comment('简介'),
        ],'ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_detail');
    }
}
