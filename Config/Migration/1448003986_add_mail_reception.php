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
 * * 各自でメール受信可否の設定を可能にするフィールド追加('is_email_reception', 'is_moblie_mail_reception')
 *
 * @package NetCommons\Users\Config\Migration
 */
class AddMailReception extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'Add_mail_reception';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'is_email_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_email_public'),
					'is_moblie_mail_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'is_moblie_mail_public'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('is_email_reception', 'is_moblie_mail_reception'),
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
