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
        $this->createTable('{{%post_categories}}', [
            'id' => $this->primaryKey(),
            'ownerId' => $this->integer(11)->notNull(),
            'name' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
            'updated_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%post_categories}}');
    }
}
