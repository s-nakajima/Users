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
				'users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'ID | ログインID'),
					'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Password | パスワード'),
					'key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Link identifier | リンク識別子'),
					'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'avatar' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Avatar | アバター'),
					'avatar_file_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'is_avatar_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'handlename' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Handle | ハンドル'),
					'is_handlename_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'is_name_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'E-mail | eメール'),
					'is_email_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'moblie_mail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Mobile mail | 携帯メール'),
					'is_moblie_mail_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'sex' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Sex | 性別'),
					'is_sex_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'timezone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'TimeZone | タイムゾーン'),
					'is_timezone_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Authority | 権限'),
					'is_role_key_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Status | 状態'),
					'is_status_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Created | 作成日時'),
					'is_created_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'Creator | 作成者'),
					'is_created_user_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Last modified | 更新日時'),
					'is_modified_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'Updater | 更新者'),
					'is_modified_user_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'password_modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Password has been changed | パスワード変更日時'),
					'is_password_modified_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'last_login' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Last login | 最終ログイン日時'),
					'is_last_login_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Name | 氏名'),
					'profile' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Profile | プロフィール'),
					'search_keywords' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Keywords | 検索キーワード'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'users', 'users_languages'
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
