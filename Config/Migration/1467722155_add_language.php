<?php
/**
 * 言語フィールド追加 migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * 言語フィールド追加 migration
 *
 * @package NetCommons\Users\Config\Migration
 */
class AddLanguage extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_language';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'language' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'is_sex_public'),
					'is_language_public' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'language'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('language', 'is_language_public'),
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
