<?php
/**
 * User::createUser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * User::createUser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\User
 */
class UserCreateUserTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.user',
		'plugin.users.user_select_count',
		'plugin.users.users_language',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'User';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'createUser';

/**
 * createUser()のテスト
 *
 * @return void
 */
	public function testCreateUser() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		$expected = array(
			'UsersLanguage' => array(
				0 => array(
					'user_id' => '',
					'language_id' => 1,
					'name' => null,
					'profile' => null,
					'search_keywords' => null,
					'id' => null,
				),
				1 => array(
					'user_id' => '',
					'language_id' => 2,
					'name' => null,
					'profile' => null,
					'search_keywords' => null,
					'id' => null,
				),
			),
			'User' => array(
				'is_deleted' => '0',
				'is_avatar_public' => '0',
				'is_avatar_auto_created' => '1',
				'is_handlename_public' => '0',
				'is_name_public' => '0',
				'is_email_public' => '0',
				'is_email_reception' => '1',
				'is_moblie_mail_public' => '0',
				'is_moblie_mail_reception' => '1',
				'is_sex_public' => '0',
				'is_language_public' => '0',
				'is_timezone_public' => '0',
				'is_role_key_public' => '0',
				'is_status_public' => '0',
				'is_created_public' => '0',
				'created_user' => '0',
				'is_created_user_public' => '0',
				'is_modified_public' => '0',
				'modified_user' => '0',
				'is_modified_user_public' => '0',
				'is_password_modified_public' => '0',
				'is_last_login_public' => '0',
				'is_previous_login_public' => '0',
				'is_profile_public' => '0',
				'is_search_keywords_public' => '0',
				'username' => '',
				'password' => null,
				'key' => null,
				'activate_key' => null,
				'activated' => null,
				'handlename' => null,
				'email' => null,
				'moblie_mail' => null,
				'sex' => null,
				'language' => 'auto',
				'timezone' => 'Asia/Tokyo',
				'role_key' => 'common_user',
				'status' => null,
				'created' => null,
				'modified' => null,
				'password_modified' => null,
				'last_login' => null,
				'previous_login' => null,
				'id' => null,
			),
		);
		$this->assertEquals($result, $expected);
	}

}
