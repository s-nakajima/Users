<?php
/**
 * AddIndex migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * AddIndex migration
 *
 * @package NetCommons\Users\Config\Migration
 */
class AddIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'user_select_counts' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
				'users_languages' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				'user_select_counts' => array(
					'indexes' => array(
						'user_id' => array('column' => 'user_id', 'unique' => 0),
					),
				),
				'users_languages' => array(
					'indexes' => array(
						'user_id' => array('column' => array('user_id', 'language_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'user_select_counts' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'users_languages' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				'user_select_counts' => array('indexes' => array('user_id')),
				'users_languages' => array('indexes' => array('user_id')),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
