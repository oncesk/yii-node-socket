<?php

class m131126_114536_node_socket_migration extends CDbMigration {

	public function up() {
		$this->createTable('ns_channel', array(
			'id' => 'pk',
			'name' => 'string not null',
			'properties' => 'text',
			'is_authentication_required' => 'boolean not null',
			'allowed_roles' => 'string',
			'subscriber_source' => 'boolean not null',
			'event_source' => 'boolean not null',
			'create_date' => 'timestamp'
		), 'ENGINE=InnoDb default charset=utf8 collate utf8_general_ci');

		$this->createIndex('ns_channel_unique_name', 'ns_channel', 'name', true);
		$this->createIndex('ns_channel_name', 'ns_channel', 'name');

		$this->createTable('ns_subscriber', array(
			'id' => 'pk',
			'role' => 'string',
			'user_id' => 'integer',
			'sid' => 'string',
			'sid_expiration' => 'datetime',
			'create_date' => 'timestamp'
		), 'ENGINE=InnoDb default charset=utf8 collate utf8_general_ci');

		$this->createTable('ns_channel_subscriber', array(
			'id' => 'pk',
			'subscriber_id' => 'integer not null',
			'channel_id' => 'integer not null',
			'can_send_event_from_php' => 'boolean not null',
			'can_send_event_from_js' => 'boolean not null',
			'create_date' => 'timestamp'
		), 'ENGINE=InnoDb default charset=utf8 collate utf8_general_ci');

		$this->addForeignKey('ns_channel_subscriber_subscriber_id_fk', 'ns_channel_subscriber', 'subscriber_id', 'ns_subscriber', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('ns_channel_subscriber_channel_id_fk', 'ns_channel_subscriber', 'channel_id', 'ns_channel', 'id', 'CASCADE', 'CASCADE');
	}

	public function down() {
		$this->dropForeignKey('ns_channel_subscriber_subscriber_id_fk', 'ns_channel_subscriber');
		$this->dropForeignKey('ns_channel_subscriber_channel_id_fk', 'ns_channel_subscriber');

		$this->dropTable('ns_channel');
		$this->dropTable('ns_subscriber');
		$this->dropTable('ns_channel_subscriber');
	}
}