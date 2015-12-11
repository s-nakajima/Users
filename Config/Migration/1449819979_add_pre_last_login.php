<?php
/**
 * Migration file
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Migration file
 *
 * * 前回ログイン日時に関するするフィールド追加（pre_last_login、is_pre_last_login_public）
 *
 * @package NetCommons\Users\Config\Migration
 */
class AddPreLastLogin extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_pre_last_login';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'pre_last_login' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'PreLast login | 前回ログイン日時', 'after' => 'is_last_login_public'),
					'is_pre_last_login_public' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'pre_last_login'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('pre_last_login', 'is_pre_last_login_public'),
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
