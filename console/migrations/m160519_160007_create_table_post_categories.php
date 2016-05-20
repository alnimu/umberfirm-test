<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_post_categories`.
 */
class m160519_160007_create_table_post_categories extends Migration
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

        $this->createTable('{{%post_categories}}', [
            'id' => $this->primaryKey(),
            'ownerId' => $this->integer(11)->notNull(),
            'name' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
            'updated_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ], $tableOptions);

        $this->createIndex('name_unique', '{{%post_categories}}', ['name'], true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%post_categories}}');
    }
}
