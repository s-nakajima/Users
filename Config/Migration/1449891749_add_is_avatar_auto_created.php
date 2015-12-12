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
 * * アバターの自動生成かどうかのフィールド追加（is_avatar_auto_created）
 *
 * @package NetCommons\Users\Config\Migration
 */
class AddIsAvatarAutoCreated extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_is_avatar_auto_created';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'is_avatar_auto_created' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'is_avatar_public'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('is_avatar_auto_created'),
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
