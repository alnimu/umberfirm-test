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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%post_has_category}}', [
            'id' => $this->primaryKey(),
            'postId' => $this->integer(11)->notNull(),
            'categoryId' => $this->integer(11)->notNull()
        ], $tableOptions);

        $this->createIndex('postId_categoryId', '{{%post_has_category}}', ['postId', 'categoryId']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%post_has_category}}');
    }
}
