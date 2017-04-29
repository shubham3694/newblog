<?php
class m170429_082322_user_last_request_at_column extends CDbMigration {
	
	public function up() {
		$this->addColumn('user', 'last_request_at', 'int(11) AFTER status');
	}

	public function down() {
		$this->dropColumn('user', 'last_request_at', 'int(11) AFTER status');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}