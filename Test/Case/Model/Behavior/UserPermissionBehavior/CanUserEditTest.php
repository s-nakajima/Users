<?php
/**
 * UserPermissionBehavior::canUserEdit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');
App::uses('UserRole', 'UserRoles.Model');

/**
 * UserPermissionBehavior::canUserEdit()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\Behavior\UserPermissionBehavior
 */
class UserPermissionBehaviorCanUserEditTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Users', 'TestUsers');
		$this->TestModel = ClassRegistry::init('TestUsers.TestUserPermissionBehaviorModel');
	}

/**
 * canUserEdit()テストのDataProvider
 *
 * ### 戻り値
 *  - roleKey 会員権限キー
 *  - user ユーザデータ
 *  - expected 期待値
 *
 * @return array データ
 */
	public function dataProvider() {
		//0: データが取れなかった場合
		$index = 0;
		$result[$index] = array();
		$result[$index]['roleKey'] = UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR;
		$result[$index]['user'] = array();
		$result[$index]['expected'] = false;

		//1: サイト管理者がシステム管理者を編集しようとした場合
		$index = 1;
		$result[$index] = array();
		$result[$index]['roleKey'] = UserRole::USER_ROLE_KEY_ADMINISTRATOR;
		$result[$index]['user'] = array('User' => array('role_key' => UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR));
		$result[$index]['expected'] = false;

		//2: 編集OK
		$index = 2;
		$result[$index] = array();
		$result[$index]['roleKey'] = UserRole::USER_ROLE_KEY_ADMINISTRATOR;
		$result[$index]['user'] = array('User' => array('role_key' => UserRole::USER_ROLE_KEY_ADMINISTRATOR));
		$result[$index]['expected'] = true;

		return $result;
	}

/**
 * canUserEdit()のテスト
 *
 * @param string $roleKey 会員権限キー
 * @param array $user ユーザデータ
 * @param bool $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testCanUserEdit($roleKey, $user, $expected) {
		//テストデータ
		Current::write('User.role_key', $roleKey);

		//テスト実施
		$result = $this->TestModel->canUserEdit($user);

		//チェック
		$this->assertEquals($result, $expected);
	}

}
