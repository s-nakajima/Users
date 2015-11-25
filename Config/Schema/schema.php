<?php
/**
 * Schema file
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Schema file
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\Config\Schema
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class UsersSchema extends CakeSchema {

/**
 * Database connection
 *
 * @var string
 */
	public $connection = 'master';

/**
 * before
 *
 * @param array $event event
 * @return bool
 */
	public function before($event = array()) {
		return true;
	}

/**
 * after
 *
 * @param array $event event
 * @return void
 */
	public function after($event = array()) {
	}

/**
 * users table
 *
 * @var array
 */
	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ID | ログインID', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Password | パスワード', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Link identifier | リンク識別子', 'charset' => 'utf8'),
		'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_avatar_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'handlename' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Handle | ハンドル', 'charset' => 'utf8'),
		'is_handlename_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_name_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'E-mail | eメール', 'charset' => 'utf8'),
		'is_email_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_email_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'moblie_mail' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Mobile mail | 携帯メール', 'charset' => 'utf8'),
		'is_moblie_mail_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_moblie_mail_reception' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'sex' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Sex | 性別', 'charset' => 'utf8'),
		'is_sex_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'timezone' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'TimeZone | タイムゾーン', 'charset' => 'utf8'),
		'is_timezone_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Authority | 権限', 'charset' => 'utf8'),
		'is_role_key_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Status | 状態', 'charset' => 'utf8'),
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
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * users_languages table
 *
 * @var array
 */
	public $users_languages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Name | 氏名', 'charset' => 'utf8'),
		'profile' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Profile | プロフィール', 'charset' => 'utf8'),
		'search_keywords' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Keywords | 検索キーワード', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
