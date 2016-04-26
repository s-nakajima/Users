<?php
/**
 * Init migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Init migration
 *
 * @package NetCommons\Users\Config\Migration
 */
class Init extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'init';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'user_select_counts' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'select_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ログインID', 'charset' => 'utf8'),
					'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'charset' => 'utf8'),
					'key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'リンク識別子', 'charset' => 'utf8'),
					'activate_key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクティベートキー', 'charset' => 'utf8'),
					'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_avatar_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_avatar_auto_created' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
					'handlename' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ハンドル', 'charset' => 'utf8'),
					'is_handlename_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_name_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'eメール', 'charset' => 'utf8'),
					'is_email_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_email_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'moblie_mail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '携帯メール', 'charset' => 'utf8'),
					'is_moblie_mail_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_moblie_mail_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'sex' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '性別', 'charset' => 'utf8'),
					'is_sex_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'timezone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイムゾーン', 'charset' => 'utf8'),
					'is_timezone_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '権限', 'charset' => 'utf8'),
					'is_role_key_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '状態', 'charset' => 'utf8'),
					'is_status_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
					'is_created_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '作成者'),
					'is_created_user_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
					'is_modified_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '更新者'),
					'is_modified_user_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'password_modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'パスワード変更日時'),
					'is_password_modified_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'last_login' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '最終ログイン日時'),
					'is_last_login_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'previous_login' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '前回ログイン日時'),
					'is_previous_login_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_profile_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_search_keywords_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'users_languages' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '氏名', 'charset' => 'utf8'),
					'profile' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール', 'charset' => 'utf8'),
					'search_keywords' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '検索キーワード', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'user_select_counts', 'users', 'users_languages'
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
