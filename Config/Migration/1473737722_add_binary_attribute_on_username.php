<?php
/**
 * usernameをutf8_binに変更
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * usernameをutf8_binに変更
 * ログインIDの大文字、小文字を区別するため。
 *
 * @package NetCommons\Users\Config\Migration
 * @link https://github.com/NetCommons3/NetCommons3/issues/666
 */
class AddBinaryAttributeOnUsername extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_binary_attribute_on_username';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'users' => array(
					'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_bin', 'comment' => 'ログインID', 'charset' => 'utf8'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'users' => array(
					'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ログインID', 'charset' => 'utf8'),
				),
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
