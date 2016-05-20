<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_post_has_category`.
 */
class m160519_201508_create_table_post_has_category extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%post_has_category}}', [
            'id' => $this->primaryKey(),
            'postId' => $this->integer(11)->notNull(),
            'categoryId' => $this->integer(11)->notNull()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%post_has_category}}');
    }
}
