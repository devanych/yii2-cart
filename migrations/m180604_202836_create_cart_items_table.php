<?php

use yii\db\Migration;
/**
 * Handles the creation of table `cart_items`.
 */
class m180604_202836_create_cart_items_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cart_items}}', [
            'user_id' => $this->integer()->unsigned()->notNull(),
            'product_id' => $this->integer()->unsigned()->notNull(),
            'quantity' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('{{%pk-cart_items}}', '{{%cart_items}}', ['user_id', 'product_id']);

        $this->createIndex('{{%idx-cart_items-user_id}}', '{{%cart_items}}', 'user_id');
        $this->createIndex('{{%idx-cart_items-product_id}}', '{{%cart_items}}', 'product_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%cart_items}}');
    }
}
