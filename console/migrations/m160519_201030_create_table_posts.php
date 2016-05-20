<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_posts`.
 */
class m160519_201030_create_table_posts extends Migration
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
        
        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'ownerId' => $this->integer(11)->notNull(),
            'title' => $this->string()->notNull(),
            'content' => $this->text()->notNull(),
            'visible' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
            'updated_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ], $tableOptions);
        
        $this->createIndex('visible_status_index', '{{%posts}}', ['visible', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%posts}}');
    }
}
