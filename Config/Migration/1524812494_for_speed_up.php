<?php
/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * 速度改善のためのインデックス見直し
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Config\Migration
 */
class ForSpeedUp extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'for_speed_up';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'indexes' => array(
						'username' => array('column' => 'username', 'unique' => 0),
						'userlist' => array('column' => array('is_deleted', 'id'), 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				'users' => array(
					'username' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_bin', 'comment' => 'ログインID', 'charset' => 'utf8'),
					'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('indexes' => array('username', 'userlist')),
			),
			'alter_field' => array(
				'users' => array(
					'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_bin', 'comment' => 'ログインID', 'charset' => 'utf8'),
					'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
