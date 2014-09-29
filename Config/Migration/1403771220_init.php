<?php
class Init extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'user_attributes' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
					'type' => array('type' => 'integer', 'null' => true),
					'required' => array('type' => 'boolean', 'null' => true),
					'is_each_language' => array('type' => 'boolean', 'null' => true),
					'can_read_self' => array('type' => 'boolean', 'null' => true),
					'can_edit_self' => array('type' => 'boolean', 'null' => true),
					'position' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
					'created_user_id' => array('type' => 'integer', 'null' => true),
					'created' => array('type' => 'datetime', 'null' => true),
					'modified_user_id' => array('type' => 'integer', 'null' => true),
					'modified' => array('type' => 'datetime', 'null' => true),
					'indexes' => array(
						'PRIMARY' => array('unique' => true, 'column' => 'id'),
					),
					'tableParameters' => array(),
				),
				'user_attributes_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false),
					'user_attribute_id' => array('type' => 'integer', 'null' => false),
					'value' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
					'created_user_id' => array('type' => 'integer', 'null' => true),
					'created' => array('type' => 'datetime', 'null' => true),
					'modified_user_id' => array('type' => 'integer', 'null' => true),
					'modified' => array('type' => 'datetime', 'null' => true),
					'indexes' => array(
						'PRIMARY' => array('unique' => true, 'column' => 'id'),
					),
					'tableParameters' => array(),
				),
				'user_select_attributes' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
					'user_attribute_id' => array('type' => 'integer', 'null' => true),
					'created_user_id' => array('type' => 'integer', 'null' => true),
					'created' => array('type' => 'datetime', 'null' => true),
					'modified_user_id' => array('type' => 'integer', 'null' => true),
					'modified' => array('type' => 'datetime', 'null' => true),
					'indexes' => array(
						'PRIMARY' => array('unique' => true, 'column' => 'id'),
					),
					'tableParameters' => array(),
				),
				'user_select_attributes_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false),
					'user_select_attribute_id' => array('type' => 'integer', 'null' => false),
					'value' => array('type' => 'integer', 'null' => true),
					'created_user_id' => array('type' => 'integer', 'null' => true),
					'created' => array('type' => 'datetime', 'null' => true),
					'modified_user_id' => array('type' => 'integer', 'null' => true),
					'modified' => array('type' => 'datetime', 'null' => true),
					'indexes' => array(
						'PRIMARY' => array('unique' => true, 'column' => 'id'),
					),
					'tableParameters' => array(),
				),
				'users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
					'username' => array('type' => 'string', 'null' => true, 'default' => null),
					'password' => array('type' => 'string', 'null' => true, 'default' => null),
					'role_id' => array('type' => 'integer', 'null' => false),
					'created_user_id' => array('type' => 'integer', 'null' => true),
					'created' => array('type' => 'datetime', 'null' => true),
					'modified_user_id' => array('type' => 'integer', 'null' => true),
					'modified' => array('type' => 'datetime', 'null' => true),
					'indexes' => array(
						'PRIMARY' => array('unique' => true, 'column' => 'id'),
					),
					'tableParameters' => array(),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'user_attributes', 'user_attributes_users', 'user_select_attributes', 'user_select_attributes_users', 'users'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
